<?php
if (!isset($_GET['torrentid']) || !is_number($_GET['torrentid'])) {
    error(404);
}
$TorrentID = $_GET['torrentid'];

if (!empty($_GET['page']) && is_number($_GET['page'])) {
    $Page = $_GET['page'];
    $Limit = (string)(($Page - 1) * 100) . ', 100';
} else {
    $Page = 1;
    $Limit = 100;
}

$Result = $DB->query("
	SELECT
		SQL_CALC_FOUND_ROWS
		xu.uid,
		t.Size,
		xu.active,
		xu.connectable,
		xu.uploaded,
		xu.remaining,
		xu.useragent
	FROM xbt_files_users AS xu
		LEFT JOIN users_main AS um ON um.ID = xu.uid
		JOIN torrents AS t ON t.ID = xu.fid
	WHERE xu.fid = '$TorrentID'
		AND um.Visible = '1'
	ORDER BY xu.uid = '$LoggedUser[ID]' DESC, xu.uploaded DESC
	LIMIT $Limit");
$DB->query('SELECT FOUND_ROWS()');
list($NumResults) = $DB->next_record();
$DB->set_query_id($Result);

?>
<h4><?= Lang::get('torrents', 'peer_list') ?></h4>
<? if ($NumResults > 100) { ?>
    <div class="linkbox"><?= js_pages('show_peers', $_GET['torrentid'], $NumResults, $Page) ?></div>
<? } ?>
<div class="table_container border torrentdetails">
    <table>
        <tr class="colhead_dark">
            <td><?= Lang::get('torrents', 'user') ?></td>
            <td><?= Lang::get('torrents', 'active') ?></td>
            <td><?= Lang::get('torrents', 'connectable') ?></td>
            <td class="number_column"><?= Lang::get('torrents', 'up_this_session') ?></td>
            <td class="number_column">%</td>
            <td><?= Lang::get('torrents', 'client') ?></td>
        </tr>
        <?
        while (list($PeerUserID, $Size, $Active, $Connectable, $Uploaded, $Remaining, $UserAgent) = $DB->next_record()) {
        ?>
            <tr>
                <?
                if (check_perms('users_mod') || $PeerUserID == G::$LoggedUser['ID']) {
                ?>
                    <td><?= Users::format_username($PeerUserID, false, false, false) ?></td>
                <?  } else {
                ?>
                    <td><?= Lang::get('torrents', 'peer') ?></td>
                <?  }
                ?>
                <td><?= ($Active) ? '<span style="color: green;">Yes</span>' : '<span style="color: red;">No</span>' ?></td>
                <td><?= ($Connectable) ? '<span style="color: green;">Yes</span>' : '<span style="color: red;">No</span>' ?></td>
                <td class="number_column"><?= Format::get_size($Uploaded) ?></td>
                <td class="number_column"><?= number_format(($Size - $Remaining) / $Size * 100, 2) ?></td>
                <td><?= display_str($UserAgent) ?></td>
            </tr>
        <?
        }
        ?>
    </table>
</div>
<? if ($NumResults > 100) { ?>
    <div class="linkbox"><?= js_pages('show_peers', $_GET['torrentid'], $NumResults, $Page) ?></div>
<? } ?>