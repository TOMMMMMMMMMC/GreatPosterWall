<?php

/************************************************************************
||------------------|| User token history page ||-----------------------||
This page lists the torrents a user has spent his tokens on. It
gets called if $_GET['action'] == 'token_history'.

Using $_GET['userid'] allows a mod to see any user's token history.
Nonmods and empty userid show $LoggedUser['ID']'s history
 ************************************************************************/

if (isset($_GET['userid'])) {
    $UserID = $_GET['userid'];
} else {
    $UserID = $LoggedUser['ID'];
}
if (!is_number($UserID)) {
    error(404);
}

$UserInfo = Users::user_info($UserID);
$Perms = Permissions::get_permissions($UserInfo['PermissionID']);
$UserClass = $Perms['Class'];

if (!check_perms('users_mod')) {
    if ($LoggedUser['ID'] != $UserID && !check_paranoia(false, $User['Paranoia'], $UserClass, $UserID)) {
        error(403);
    }
}

if (isset($_GET['expire'])) {
    if (!check_perms('users_mod')) {
        error(403);
    }
    $UserID = $_GET['userid'];
    $TorrentID = $_GET['torrentid'];

    if (!is_number($UserID) || !is_number($TorrentID)) {
        error(403);
    }
    $DB->query("
		SELECT info_hash
		FROM torrents
		WHERE ID = $TorrentID");
    if (list($InfoHash) = $DB->next_record(MYSQLI_NUM, FALSE)) {
        $DB->query("
			UPDATE users_freeleeches
			SET Expired = TRUE
			WHERE UserID = $UserID
				AND TorrentID = $TorrentID");
        $Cache->delete_value("users_tokens_$UserID");
        Tracker::update_tracker('remove_token', array('info_hash' => rawurlencode($InfoHash), 'userid' => $UserID));
    }
    header("Location: userhistory.php?action=token_history&userid=$UserID");
}

View::show_header(Lang::get('userhistory', 'fl_token_history'));

list($Page, $Limit) = Format::page_limit(25);

$DB->query("
	SELECT
		SQL_CALC_FOUND_ROWS
		f.TorrentID,
		t.GroupID,
		f.Time,
		f.Expired,
		f.Downloaded,
		f.Uses,
		g.Name,
		t.Format,
		t.Encoding
	FROM users_freeleeches AS f
		LEFT JOIN torrents AS t ON t.ID = f.TorrentID
		LEFT JOIN torrents_group AS g ON g.ID = t.GroupID
	WHERE f.UserID = $UserID
	ORDER BY f.Time DESC
	LIMIT $Limit");
$Tokens = $DB->to_array();

$DB->query('SELECT FOUND_ROWS()');
list($NumResults) = $DB->next_record();
$Pages = Format::get_pages($Page, $NumResults, 25);

?>
<div class="header">
    <h2><?= Lang::get('userhistory', 'fl_token_history_for_before') ?><?= Users::format_username($UserID, false, false, false) ?><?= Lang::get('userhistory', 'fl_token_history_for_after') ?></h2>
</div>
<div class="linkbox"><?= $Pages ?></div>
<div class="table_container border">
    <table class="userhistory_token_history_table">
        <tr class="colhead_dark">
            <td><?= Lang::get('global', 'torrent') ?></td>
            <td><?= Lang::get('userhistory', 'time') ?></td>
            <td><?= Lang::get('userhistory', 'expired') ?></td>
            <? if (check_perms('users_mod')) { ?>
                <td><?= Lang::get('userhistory', 'downloaded') ?></td>
                <td><?= Lang::get('userhistory', 'tokens_used') ?></td>
            <? } ?>
        </tr>
        <?
        foreach ($Tokens as $Token) {
            $GroupIDs[] = $Token['GroupID'];
        }
        $Artists = Artists::get_artists($GroupIDs);

        $i = true;
        foreach ($Tokens as $Token) {
            $i = !$i;
            list($TorrentID, $GroupID, $Time, $Expired, $Downloaded, $Uses, $Name, $Format, $Encoding) = $Token;
            if ($Name != '') {
                $Name = "<a href=\"torrents.php?torrentid=$TorrentID\">$Name</a>";
            } else {
                $Name = "(<i>Deleted torrent <a href=\"log.php?search=Torrent+$TorrentID\">$TorrentID</a></i>)";
            }
            $ArtistName = Artists::display_artists($Artists[$GroupID]);
            if ($ArtistName) {
                $Name = $ArtistName . $Name;
            }
            if ($Format && $Encoding) {
                $Name .= " [$Format / $Encoding]";
            }
        ?>
            <tr class="<?= ($i ? 'rowa' : 'rowb') ?>">
                <td><?= $Name ?></td>
                <td><?= time_diff($Time) ?></td>
                <td><?= ($Expired ? Lang::get('userhistory', 'yes') : Lang::get('userhistory', 'no')) ?><?= (check_perms('users_mod') && !$Expired) ? " <a href=\"userhistory.php?action=token_history&amp;expire=1&amp;userid=$UserID&amp;torrentid=$TorrentID\">" . Lang::get('userhistory', 'expire_button') . "</a>" : ''; ?>
                </td>
                <? if (check_perms('users_mod')) { ?>
                    <td><?= Format::get_size($Downloaded) ?></td>
                    <td><?= $Uses ?></td>
                <?  } ?>
            </tr>
        <?
        }
        ?>
    </table>
</div>
<div class="linkbox"><?= $Pages ?></div>
<?
View::show_footer();
?>