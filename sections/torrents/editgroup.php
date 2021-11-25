<?

/************************************************************************
||------------|| Edit torrent group wiki page ||-----------------------||

This page is the page that is displayed when someone feels like editing
a torrent group's wiki page.

It is called when $_GET['action'] == 'edit'. $_GET['groupid'] is the
ID of the torrent group and must be set.

The page inserts a new revision into the wiki_torrents table, and clears
the cache for the torrent group page.

 ************************************************************************/

$GroupID = $_GET['groupid'];
if (!is_number($GroupID) || !$GroupID) {
    error(0);
}

// Get the torrent group name and the body of the last revision
$DB->query("
	SELECT
		tg.Name,
		tg.SubName,
        tg.IMDBID,
        tg.DoubanID,
        tg.RTTitle,
		tg.WikiImage,
		tg.WikiBody,
		tg.Year,
		tg.RecordLabel,
		tg.CatalogueNumber,
		tg.ReleaseType,
		tg.CategoryID,
		tg.VanityHouse
	FROM torrents_group AS tg
	WHERE tg.ID = '$GroupID'");
if (!$DB->has_results()) {
    error(404);
}
list($Name, $SubName, $IMDBID, $DoubanID, $RTTitle, $Image, $Body, $Year, $RecordLabel, $CatalogueNumber, $ReleaseType, $CategoryID, $VanityHouse) = $DB->next_record();

View::show_header(Lang::get('torrents', 'edit_torrent_group'));

// Start printing form
?>
<div class="thin">
    <div class="header">
        <h2><?= Lang::get('global', 'edit') ?> <a href="torrents.php?id=<?= $GroupID ?>"><?= $Name ?></a></h2>
    </div>
    <div class="box pad">
        <form class="edit_form" name="torrent_group" action="torrents.php" method="post">
            <div>
                <input type="hidden" name="action" value="takegroupedit" />
                <input type="hidden" name="auth" value="<?= $LoggedUser['AuthKey'] ?>" />
                <input type="hidden" name="groupid" value="<?= $GroupID ?>" />
                <h3><?= Lang::get('torrents', 'image') ?>:</h3>
                <input type="text" name="image" size="92" value="<?= $Image ?>" /><br />
                <h3><?= Lang::get('upload', 'chinese_movie_synopsis') ?>:</h3>
                <?php new TEXTAREA_PREVIEW('body', 'body', $Body, 91, 20); ?><br />
                <!-- <h3><?= Lang::get('upload', 'english_movie_synopsis') ?>:</h3>
                <?php new TEXTAREA_PREVIEW('body', 'body', $Body, 91, 20); ?><br /> -->
                <? if ($CategoryID == 1) { ?>
                    <h3><?= Lang::get('torrents', 'release_type') ?>:
                        <select id="releasetype" name="releasetype">
                            <? foreach ($ReleaseTypes as $Key => $Val) { ?>
                                <option value="<?= $Key ?>" <?= ($Key == $ReleaseType ? ' selected="selected"' : '') ?>><?= Lang::get('torrents', 'release_types')[$Key] ?></option>
                            <?      } ?>
                        </select>
                    </h3>
                <?
                }
                ?>
                <h3><?= Lang::get('torrents', 'database_ids') ?>:</h3>
                <table id="database_ids_table">
                    <tr>
                        <td style="width: 130px;"><?= Lang::get('torrents', 'imdb_id') ?>:</td>
                        <td><input <?= check_perms('users_mod') ? '' : 'readonly' ?> type="text" name="imdbid" size="20" value="<?= $IMDBID ?>" placeholder="tt1234567">
                            <input class="hidden" id='lack_of_imdb_info'>
                            <label class="hidden" for="lack_of_imdb_info"><?= Lang::get('torrents', 'lack_of_info_now') ?></label>
                        </td>
                    </tr>
                    <tr>
                        <td><?= Lang::get('torrents', 'douban_id') ?>:</td>
                        <td><input type="text" name="doubanid" size="20" value="<?= $DoubanID ? $DoubanID : '' ?>" placeholder="12345678" />
                            <input class="hidden" id='lack_of_douban_info'>
                            <label class="hidden" for="lack_of_douban_info"><?= Lang::get('torrents', 'lack_of_info_now') ?></label>
                        </td>
                    </tr>
                    <tr>
                        <td><?= Lang::get('torrents', 'rt_title') ?>:</td>
                        <td><input type="text" name="rttitle" value="<?= $RTTitle ?>" size="20" placeholder="english_name" />
                            <input class="hidden" id='lack_of_rt_info'>
                            <label class="hidden" for="lack_of_rt_info"><?= Lang::get('torrents', 'lack_of_info_now') ?></label>
                        </td>
                    </tr>
                </table>

                <h3><?= Lang::get('torrents', 'edit_summary') ?>:</h3>
                <input type="text" name="summary" size="92" /><br />
                <div style="text-align: center;">
                    <input type="submit" value="Submit" />
                </div>
            </div>
        </form>
    </div>
    <?
    $DB->query("
		SELECT UserID
		FROM torrents
		WHERE GroupID = $GroupID");
    //Users can edit the group info if they've uploaded a torrent to the group or have torrents_edit
    if (in_array($LoggedUser['ID'], $DB->collect('UserID')) || check_perms('torrents_edit')) { ?>
        <h3><?= Lang::get('torrents', 'non_wiki_torrent_group_editing') ?></h3>
        <div class="box pad">
            <form class="edit_form" name="torrent_group" action="torrents.php" method="post">
                <input type="hidden" name="action" value="nonwikiedit" />
                <input type="hidden" name="auth" value="<?= $LoggedUser['AuthKey'] ?>" />
                <input type="hidden" name="groupid" value="<?= $GroupID ?>" />
                <table cellpadding="3" cellspacing="1" border="0" class="layout border" width="100%">
                    <tr>
                        <td colspan="2" class="center"><?= Lang::get('torrents', 'torrent_group_editing_note') ?></td>
                    </tr>
                    <tr>
                        <td class="label"><?= Lang::get('torrents', 'ft_year') ?>:</td>
                        <td>
                            <input type="text" name="year" size="10" value="<?= $Year ?>" />
                        </td>
                    </tr>

                    <? if (check_perms('torrents_freeleech')) { ?>
                        <tr>
                            <td class="label"><?= Lang::get('torrents', 'torrent_group_leech_status') ?>:</td>
                            <td>
                                <input type="checkbox" id="unfreeleech" name="unfreeleech" /><label for="unfreeleech"> <?= Lang::get('torrents', 'reset') ?></label>
                                <input type="checkbox" id="freeleech" name="freeleech" /><label for="freeleech"> <?= Lang::get('torrents', 'freeleech') ?></label>
                                <input type="checkbox" id="neutralleech" name="neutralleech" /><label for="neutralleech"> <?= Lang::get('torrents', 'neutral_leech') ?></label>
                                <input type="checkbox" id="off25leech" name="off25leech" /><label for="off25leech"> <?= Lang::get('torrents', 'off25') ?></label>
                                <input type="checkbox" id="off50leech" name="off50leech" /><label for="off50leech"> <?= Lang::get('torrents', 'off50') ?></label>
                                <input type="checkbox" id="off75leech" name="off75leech" /><label for="off75leech"> <?= Lang::get('torrents', 'off75') ?></label>
                                <?= Lang::get('torrents', 'because') ?>
                                <select name="freeleechtype" <? $FL = array('N/A', 'Staff Pick', 'Perma-FL', 'Vanity House');
                                                                // TODO 种子组的free类型当前没有记忆功能
                                                                foreach ($FL as $Key => $FLType) { ?> <option value="<?= $Key ?>"><?= $FLType ?></option>
                                <?      } ?>
                                </select>
                            </td>
                        </tr>
                    <?  } ?>
                </table>
                <div style="text-align: center;">
                    <input type="submit" value="Edit" />
                </div>
            </form>
        </div>
    <?
    }
    if (check_perms('torrents_edit')) {
    ?>
        <h3><?= Lang::get('torrents', 'rename') ?></h3>
        <div class="box pad">
            <form class="rename_form" name="torrent_group" action="torrents.php" method="post">
                <div>
                    <input type="hidden" name="action" value="rename" />
                    <input type="hidden" name="auth" value="<?= $LoggedUser['AuthKey'] ?>" />
                    <input type="hidden" name="groupid" value="<?= $GroupID ?>" />
                    <table cellpadding="3" cellspacing="1" border="0" class="layout border" width="100%">
                        <tr>
                            <td class="label"><?= Lang::get('torrents', 'group_title') ?>:</td>
                            <td>
                                <input type="text" name="name" size="92" value="<?= $Name ?>" /><br>
                            </td>
                        </tr>
                        <tr>
                            <td class="label"><?= Lang::get('torrents', 'chinese_title') ?>:</td>
                            <td>
                                <input type="text" name="subname" size="92" value="<?= $SubName ?>" />
                            </td>
                        </tr>
                    </table>

                    <div style="text-align: center;">
                        <input type="submit" value="Rename" />
                    </div>
                </div>
            </form>
        </div>
        <h3><?= Lang::get('torrents', 'merge_with') ?></h3>
        <div class="box pad">
            <form class="merge_form" name="torrent_group" action="torrents.php" method="post">
                <div>
                    <input type="hidden" name="action" value="merge" />
                    <input type="hidden" name="auth" value="<?= $LoggedUser['AuthKey'] ?>" />
                    <input type="hidden" name="groupid" value="<?= $GroupID ?>" />
                    <h3><?= Lang::get('torrents', 'merge_target') ?>:
                        <input type="text" name="targetgroupid" size="10" />
                    </h3>
                    <div style="text-align: center;">
                        <input type="submit" value="Merge" />
                    </div>
                </div>
            </form>
        </div>
    <?  } ?>
</div>
<? View::show_footer(); ?>