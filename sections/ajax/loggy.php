<?php
if (!check_perms('site_archive_ajax')) {
    ajax_json_error('insufficient permissions to view page');
}

$where = ["t.HasLog='1'", "t.HasLogDB='0'"];

if ($_GET['type'] === 'active') {
    $where[] = 'tls.last_action > now() - INTERVAL 14 DAY';
} else if ($_GET['type'] === 'unseeded') {
    $where = ['tls.Seeders = 0'];
} else {
    $where[] = 'tls.Seeders > 0';
}

$where = implode(' AND ', $where);
$DB->prepared_query("SELECT t.ID FROM torrents t INNER JOIN torrents_leech_stats tls ON (tls.TorrentID = t.ID) WHERE {$where}");

ajax_json_success(['IDs' => $DB->collect('ID', false)]);
