<?php
View::show_header(Lang::get('better', 'torrents_with_no_artwork'));

$DB->query("SELECT COUNT(*) as count FROM torrents_group WHERE CategoryID = 1 AND WikiImage = ''");
$row = $DB->next_record();
$total = $row['count'];
$total_str = number_format($total);
$page = !empty($_GET['page']) ? intval($_GET['page']) : 1;
$page = max(1, $page);
$limit = TORRENTS_PER_PAGE;
$offset = TORRENTS_PER_PAGE * ($page - 1);
$DB->query("
SELECT ID, Name
FROM torrents_group
WHERE CategoryID = 1 AND WikiImage = ''
ORDER BY Name
LIMIT {$limit} OFFSET {$offset}");
$torrents = $DB->to_array('ID', MYSQLI_ASSOC);
foreach (Artists::get_artists(array_keys($torrents)) as $group_id => $data) {
    $torrents[$group_id]['Artists'] = array();
    $torrents[$group_id]['ExtendedArtists'] = array();
    foreach (array(1, 4, 6) as $importance) {
        if (isset($data[$importance])) {
            $torrents[$group_id]['Artists'] = array_merge($torrents[$group_id]['Artists'], $data[$importance]);
        }
    }
}
$pages = Format::get_pages($page, $total, TORRENTS_PER_PAGE);
?>
<div class="header">
    <h2><?= Lang::get('better', 'torrents_groups_that_are_missing_artwork') ?></h2>

    <div class="linkbox">
        <a href="better.php" class="brackets"><?= Lang::get('better', 'back_to_better_php_list') ?></a>
    </div>
    <div class="linkbox"><?= $pages ?></div>
</div>

<div class="thin box pad">
    <h3><?= Lang::get('better', 'there_are_torrent_groups_remaining_before') ?> <?= $total_str ?> <?= Lang::get('better', 'there_are_torrent_groups_remaining_after') ?></h3>
    <div class="table_container border">
        <table class="torrent_table">
            <?

            foreach ($torrents as $id => $torrent) {
                if (count($torrent['Artists']) > 1) {
                    $artist = "Various Artists";
                } else {
                    $artist = "<a href='artist.php?id={$torrent['Artists'][0]['id']}' target='_blank'>{$torrent['Artists'][0]['name']}</a>";
                }
            ?>
                <tr class="torrent torrent_row">
                    <td><?= $artist ?> - <a href="torrents.php?id=<?= $id ?>" target="_blank"><?= $torrent['Name'] ?></a></td>
                </tr>
            <?
            }
            ?>
        </table>
    </div>
</div>
<?

View::show_footer();
