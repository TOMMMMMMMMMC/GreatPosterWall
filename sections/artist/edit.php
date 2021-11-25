<?

/************************************************************************
||------------|| Edit artist wiki page ||------------------------------||

This page is the page that is displayed when someone feels like editing
an artist's wiki page.

It is called when $_GET['action'] == 'edit'. $_GET['artistid'] is the
ID of the artist, and must be set.

 ************************************************************************/

$ArtistID = $_GET['artistid'];
if (!is_number($ArtistID)) {
    error(0);
}

// Get the artist name and the body of the last revision
$DB->query("
	SELECT
		Name,
		Image,
		Body,
		VanityHouse,
        ChineseName,
        IMDBID
	FROM artists_group AS a
		LEFT JOIN wiki_artists ON wiki_artists.RevisionID = a.RevisionID
	WHERE a.ArtistID = '$ArtistID'");

if (!$DB->has_results()) {
    error("Cannot find an artist with the ID {$ArtistID}: See the <a href=\"log.php?search=Artist+$ArtistID\">site log</a>.");
}

list($Name, $Image, $Body, $VanityHouse, $ChineseName, $IMDBID) = $DB->next_record(MYSQLI_NUM, true);

// Start printing form
View::show_header(Lang::get('artist', 'edit_artist'));
?>
<div class="thin">
    <div class="header">
        <h2><?= Lang::get('global', 'edit') ?> <a href="artist.php?id=<?= $ArtistID ?>"><?= $Name ?></a></h2>
    </div>
    <div class="box pad">
        <form class="edit_form" name="artist" action="artist.php" method="post">
            <input type="hidden" name="action" value="edit" />
            <input type="hidden" name="auth" value="<?= $LoggedUser['AuthKey'] ?>" />
            <input type="hidden" name="artistid" value="<?= $ArtistID ?>" />
            <div>
                <h3><?= Lang::get('artist', 'image') ?>:</h3>
                <input type="text" name="image" size="92" value="<?= $Image ?>" /><br />
                <h3><?= Lang::get('artist', 'artist_info') ?>:</h3>
                <textarea name="body" cols="91" rows="20"><?= $Body ?></textarea> <br />
                <h3><?= Lang::get('artist', 'imdb_artist_id') ?>:</h3>
                <input type="text" name="imdb_id" size="20" placeholder="nm1234567" value="<?= $IMDBID ?>" />
                <input type="text" name="cname" size="20" placeholder="<?= Lang::get('artist', 'chinese_name') ?>" value="<?= $ChineseName ?>" />
                <h3><?= Lang::get('artist', 'edit_summary') ?>:</h3>
                <input type="text" name="summary" size="92" /><br />
                <div style="text-align: center;">
                    <input type="submit" value="Submit" />
                </div>
            </div>
        </form>
    </div>
    <? if (check_perms('torrents_edit')) { ?>
        <h2><?= Lang::get('artist', 'rename') ?></h2>
        <div class="box pad">
            <form class="rename_form" name="artist" action="artist.php" method="post">
                <input type="hidden" name="action" value="rename" />
                <input type="hidden" name="auth" value="<?= $LoggedUser['AuthKey'] ?>" />
                <input type="hidden" name="artistid" value="<?= $ArtistID ?>" />
                <div>
                    <input type="text" name="name" size="92" value="<?= $Name ?>" />
                    <div style="text-align: center;">
                        <input type="submit" value="Rename" />
                    </div>
                </div>
            </form>
        </div>

        <h2><?= Lang::get('artist', 'make_into') ?></h2>
        <div class="box pad">
            <form class="merge_form" name="artist" action="artist.php" method="post">
                <input type="hidden" name="action" value="change_artistid" />
                <input type="hidden" name="auth" value="<?= $LoggedUser['AuthKey'] ?>" />
                <input type="hidden" name="artistid" value="<?= $ArtistID ?>" />
                <div>
                    <p><?= Lang::get('artist', 'make_into_note_1') ?><?= $Name ?><?= Lang::get('artist', 'make_into_note_2') ?><?= $Name ?><?= Lang::get('artist', 'make_into_note_3') ?></p><br />
                    <div style="text-align: center;">
                        <label for="newartistid"><?= Lang::get('artist', 'artist_id') ?>: </label>&nbsp;<input type="text" id="newartistid" name="newartistid" size="40" value="" /><br />
                        <strong><?= Lang::get('artist', 'or') ?></strong><br />
                        <label for="newartistid"><?= Lang::get('artist', 'artist_name') ?>: </label>&nbsp;<input type="text" id="newartistname" name="newartistname" size="40" value="" />
                        <br /><br />
                        <input type="submit" value="Change artist ID" />
                    </div>
                </div>
            </form>
        </div>

        <h2><?= Lang::get('artist', 'artist_aliases') ?></h2>
        <div class="box pad">
            <h3><?= Lang::get('artist', 'aliases_list') ?></h3>
            <div class="pad">
                <ul>

                    <?
                    $NonRedirectingAliases = array();
                    $DB->query("
		SELECT AliasID, Name, UserID, Redirect
		FROM artists_alias
		WHERE ArtistID = '$ArtistID'");
                    while (list($AliasID, $AliasName, $User, $Redirect) = $DB->next_record(MYSQLI_NUM, true)) {
                        if ($AliasName == $Name) {
                            $DefaultRedirectID = $AliasID;
                        }
                    ?>
                        <li>
                            <span class="tooltip" title="Alias ID"><?= $AliasID ?></span>. <span class="tooltip" title="Alias name"><?= $AliasName ?></span>
                            <? if ($User) { ?>
                                <a href="user.php?id=<?= $User ?>" title="Alias creator" class="brackets tooltip"><?= Lang::get('artist', 'user') ?></a>
                            <?      }
                            if ($Redirect) { ?>
                                (<?= Lang::get('artist', 'writes_redirect_to') ?> <span class="tooltip" title="Target alias ID"><?= $Redirect ?></span>)
                            <?      } else {
                                $NonRedirectingAliases[$AliasID] = $AliasName;
                            }
                            ?>

                            <a href="artist.php?action=delete_alias&amp;aliasid=<?= $AliasID ?>&amp;auth=<?= $LoggedUser['AuthKey'] ?>" title="<?= Lang::get('artist', 'delete_this_alias') ?>" class="brackets tooltip">X</a>
                        </li>
                    <?  }
                    ?>
                </ul>
            </div>
            <br />
            <h3><?= Lang::get('artist', 'add_alias') ?></h3>
            <div class="pad">
                <p><?= Lang::get('artist', 'add_alias_note') ?></p>
                <form class="add_form" name="aliases" action="artist.php" method="post">
                    <input type="hidden" name="action" value="add_alias" />
                    <input type="hidden" name="auth" value="<?= $LoggedUser['AuthKey'] ?>" />
                    <input type="hidden" name="artistid" value="<?= $ArtistID ?>" />
                    <div class="field_div">
                        <span class="label"><strong><?= Lang::get('artist', 'name') ?>:</strong></span>
                        <br />
                        <input type="text" name="name" size="20" value="<?= $Name ?>" />
                    </div>
                    <div class="field_div">
                        <span class="label"><strong><?= Lang::get('artist', 'redirect_to') ?>:</strong></span>
                        <select name="redirect">
                            <option value="0"><?= Lang::get('artist', 'non_redirecting_alias') ?></option>
                            <? foreach ($NonRedirectingAliases as $AliasID => $AliasName) { ?>
                                <option value="<?= $AliasID ?>" <?= $AliasID == $DefaultRedirectID ? " selected" : "" ?>><?= $AliasName ?></option>
                            <?  } ?>
                        </select><br />
                    </div>
                    <div class="submit_div">
                        <input type="submit" value="Add alias" />
                    </div>
                </form>
            </div>
        </div>
    <? } ?>
</div>
<? View::show_footer() ?>