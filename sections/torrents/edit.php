<?
//**********************************************************************//
//~~~~~~~~~~~~~~~~~~~~~~~~~~~~ Edit form ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~//
// This page relies on the TORRENT_FORM class. All it does is call      //
// the necessary functions.                                             //
//----------------------------------------------------------------------//
// At the bottom, there are grouping functions which are off limits to  //
// most members.                                                        //
//**********************************************************************//
require(SERVER_ROOT . '/classes/torrent_form.class.php');
if (!is_number($_GET['id']) || !$_GET['id']) {
    error(0);
}

$TorrentID = $_GET['id'];

$DB->query("
	SELECT
		t.Media,
		t.Format,
		t.Encoding AS Bitrate,
		t.RemasterYear,
		t.Remastered,
		t.RemasterTitle,
		t.RemasterCatalogueNumber,
		t.RemasterRecordLabel,
        t.RemasterCustomTitle,
		t.Scene,
		t.Jinzhuan,
		t.Diy,
		t.Buy,
		t.Allow,
		t.FreeTorrent,
		t.FreeLeechType,
		t.NotMainMovie,
		t.Source,
		t.Codec,
		t.Container,
		t.Resolution,
		t.Subtitles,
		t.Makers,
        t.Processing,
        t.SpecialSub,
        t.ChineseDubbed,
        t.MediaInfo,
        t.Note,
        t.SubtitleType,
		t.Description AS TorrentDescription,
		tg.CategoryID,
		tg.Name AS Title,
		tg.Year,
		tg.IMDBID,
		tg.ArtistID,
		tg.VanityHouse,
		ag.Name AS ArtistName,
		t.GroupID,
		t.UserID,
		t.HasLog,
		t.HasCue,
		t.LogScore,
		bt.TorrentID AS BadTags,
		bf.TorrentID AS BadFolders,
		bi.TorrentID AS BadImg,
		bfi.TorrentID AS BadFiles,
		tbc.TorrentID AS BadCompress,
		ml.TorrentID AS MissingLineage,
		bns.TorrentID AS NoSub,
		bhs.TorrentID AS HardSub,
		tct.CustomTrumpable as CustomTrumpable,
		ca.TorrentID AS CassetteApproved,
		lma.TorrentID AS LossymasterApproved,
		lwa.TorrentID AS LossywebApproved,
		fttd.EndTime as FreeEndTime
	FROM torrents AS t
		LEFT JOIN torrents_group AS tg ON tg.ID = t.GroupID
		LEFT JOIN artists_group AS ag ON ag.ArtistID = tg.ArtistID
		LEFT JOIN torrents_bad_tags AS bt ON bt.TorrentID = t.ID
		LEFT JOIN torrents_no_sub AS bns ON bns.TorrentID = t.ID
		LEFT JOIN torrents_hard_sub AS bhs ON bhs.TorrentID = t.ID
		LEFT JOIN torrents_bad_folders AS bf ON bf.TorrentID = t.ID
		LEFT JOIN torrents_bad_img AS bi ON bi.TorrentID = t.ID
		LEFT JOIN torrents_bad_files AS bfi ON bfi.TorrentID = t.ID
		LEFT JOIN torrents_bad_compress AS tbc ON tbc.TorrentID = t.ID
		LEFT JOIN torrents_missing_lineage AS ml ON ml.TorrentID = t.ID
		LEFT JOIN torrents_custom_trumpable AS tct ON tct.TorrentID = t.ID
		LEFT JOIN torrents_cassette_approved AS ca ON ca.TorrentID = t.ID
		LEFT JOIN torrents_lossymaster_approved AS lma ON lma.TorrentID = t.ID
		LEFT JOIN torrents_lossyweb_approved AS lwa ON lwa.TorrentID = t.id
		LEFT JOIN freetorrents_timed as fttd on fttd.TorrentID = t.id
	WHERE t.ID = '$TorrentID'");

list($Properties) = $DB->to_array(false, MYSQLI_BOTH, false);
if (!$Properties) {
    error(404);
}

$GenreTags = $Cache->get_value('genre_tags');
if (!$GenreTags) {
    $DB->query('
		SELECT Name
		FROM tags
		WHERE TagType=\'genre\'
		ORDER BY Name');
    $GenreTags = $DB->collect('Name');
    $Cache->cache_value('genre_tags', $GenreTags, 3600 * 24);
}

$UploadForm = $Categories[$Properties['CategoryID'] - 1];

if (($LoggedUser['ID'] != $Properties['UserID'] && !check_perms('torrents_edit')) || $LoggedUser['DisableWiki']) {
    error(403);
}

View::show_header(Lang::get('torrents', 'browser_edit_torrent'), 'torrent');

if (check_perms('torrents_edit') && (check_perms('users_mod') || $Properties['CategoryID'] == 1)) {
    if ($Properties['CategoryID'] == 1) {
?>
        <div class="linkbox">
            <a class="brackets" href="#group-change"><?= Lang::get('torrents', 'change_group') ?></a>
            <a class="brackets" href="#group-split"><?= Lang::get('torrents', 'split_off_into_new_group') ?></a>
            <? if (check_perms('users_mod')) { ?>
                <a class="brackets" href="#category-change"><?= Lang::get('torrents', 'change_category') ?></a>
            <?      } ?>
        </div>
    <?  }
}

if (!($Properties['Remastered'] && !$Properties['RemasterYear']) || check_perms('edit_unknowns')) {
    if (!isset($Err)) {
        $Err = false;
    }
    $TorrentForm = new TORRENT_FORM($Properties, $Err, false);

    $TorrentForm->head();
    switch ($UploadForm) {
        case 'Movies':
            $TorrentForm->movie_form($GenreTags);
            break;
        default:
            $TorrentForm->movie_form($GenreTags);
    }
    $TorrentForm->foot();
}
if (check_perms('torrents_edit') && (check_perms('users_mod') || $Properties['CategoryID'] == 1)) {
    ?>
    <div class="thin">
        <?
        if ($Properties['CategoryID'] == 1) {
        ?>
            <div class="header">
                <h2><a name="group-change"><?= Lang::get('torrents', 'change_group') ?></a></h2>
            </div>
            <form class="edit_form form-validation" name="torrent_group" action="torrents.php" method="post">
                <input type="hidden" name="action" value="editgroupid" />
                <input type="hidden" name="auth" value="<?= $LoggedUser['AuthKey'] ?>" />
                <input type="hidden" name="torrentid" value="<?= $TorrentID ?>" />
                <input type="hidden" name="oldgroupid" value="<?= $Properties['GroupID'] ?>" />
                <table class="layout">
                    <tr>
                        <td class="label"><?= Lang::get('torrents', 'group_id') ?>:</td>
                        <td>
                            <input type="text" name="groupid" value="<?= $Properties['GroupID'] ?>" size="10" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="center">
                            <input type="submit" value="Change group ID" />
                        </td>
                    </tr>
                </table>
            </form>
            <?
            if (false) {
            ?>
                <div class="header">
                    <h2><a name="group-split"><?= Lang::get('torrents', 'split_off_into_new_group') ?></a></h2>
                </div>
                <form class="split_form" name="torrent_group" action="torrents.php" method="post">
                    <input type="hidden" name="action" value="newgroup" />
                    <input type="hidden" name="auth" value="<?= $LoggedUser['AuthKey'] ?>" />
                    <input type="hidden" name="torrentid" value="<?= $TorrentID ?>" />
                    <input type="hidden" name="oldgroupid" value="<?= $Properties['GroupID'] ?>" />
                    <table class="layout">
                        <tr>
                            <td class="label"><?= Lang::get('torrents', 'director') ?>:</td>
                            <td>
                                <input type="text" name="artist" value="<?= $Properties['ArtistName'] ?>" size="50" />
                            </td>
                        </tr>
                        <tr>
                            <td class="label"><?= Lang::get('torrents', 'title') ?>:</td>
                            <td>
                                <input type="text" name="title" value="<?= $Properties['Title'] ?>" size="50" />
                            </td>
                        </tr>
                        <tr>
                            <td class="label"><?= Lang::get('torrents', 'year') ?>:</td>
                            <td>
                                <input type="text" name="year" value="<?= $Properties['Year'] ?>" size="10" />
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="center">
                                <input type="submit" value="Split into new group" />
                            </td>
                        </tr>
                    </table>
                </form>
            <?
            }
            ?>
            <br />
        <?
        }
        ?>
    </div>
<?
}

View::show_footer([], 'upload/index'); ?>