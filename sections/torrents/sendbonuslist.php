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
                FromUserID,
                sum(Bonus) Count
			FROM torrents_send_bonus
            WHERE TorrentID = '$TorrentID'
            group by FromUserID
            ORDER BY Count DESC
			LIMIT $Limit");
$Results = $DB->to_array('FromUserID', MYSQLI_ASSOC);

$DB->query('SELECT FOUND_ROWS()');
list($NumResults) = $DB->next_record();

?>
<h4 class="tooltip" title="<?= Lang::get('torrents', 'list_of_giver_title') ?>"><?= Lang::get('torrents', 'list_of_giver') ?></h4>

<? if ($NumResults > 100) { ?>
    <div class="linkbox"><?= js_pages('show_snatches', $_GET['torrentid'], $NumResults, $Page) ?></div>
<? } ?>
<div class="table_container border torrentdetails">
    <table>
        <tr class="colhead_dark">
            <td><?= Lang::get('torrents', 'user') ?></td>
            <td><?= Lang::get('torrents', 'gift_points_pre_tax') ?></td>

            <td><?= Lang::get('torrents', 'user') ?></td>
            <td><?= Lang::get('torrents', 'gift_points_pre_tax') ?></td>
        </tr>
        <tr>
            <?
            $i = 0;

            foreach ($Results as $ID => $Data) {
                list($GiverID, $Bonus) = array_values($Data);
                if (!$GiverID && !$Bonus) continue;
                if ($i % 2 == 0 && $i > 0) {
            ?>
        </tr>
        <tr>
        <?
                }
        ?>
        <td><?= Users::format_username($GiverID, true, true, true, true) ?></td>
        <td><?= $Bonus ?></td>
    <?
                $i++;
            }
    ?>
        </tr>
    </table>
</div>
<? if ($NumResults > 100) { ?>
    <div class="linkbox"><?= js_pages('show_giver', $_GET['torrentid'], $NumResults, $Page) ?></div>
<? } ?>