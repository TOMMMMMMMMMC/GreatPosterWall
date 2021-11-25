<?php
if (!check_perms('users_mod')) {
	error(403);
}

$TorrentID = intval($_GET['torrentid']);
$LogID = intval($_GET['logid']);

$DB->query("SELECT GroupID, FileList FROM torrents WHERE ID='{$TorrentID}'");
if (!$DB->has_results()) {
	error(404);
}
list($GroupID, $FileList) = $DB->next_record();
$DB->query("SELECT * FROM torrents_logs WHERE LogID='{$LogID}' AND TorrentID='{$TorrentID}'");
if (!$DB->has_results()) {
	error(404);
}
$Log = $DB->next_record(MYSQLI_ASSOC);

$RangeRip = 0;
$IsFLAC = stripos($FileList, 'flac') !== false;

$LogPath = SERVER_ROOT . "/logs/{$TorrentID}_{$LogID}.log";
$Log = new Logchecker();
$Log->new_file($LogPath);
list($Score, $Details, $Checksum, $LogText) = $Log->parse();
$Details = trim(implode("\r\n", $Details));
if (strpos($Details, '$range_rip_detected') !== false) {
	$RangeRip = 1;
}
$DetailsArray[] = $Details;
$LogChecksum = min(intval($Checksum), $LogChecksum);
$DB->query("UPDATE torrents_logs SET Log='" . db_string($LogText) . "', Details='" . db_string($Details) . "', Score='{$Score}', `Checksum`='{$Checksum}', Adjusted='0' WHERE LogID='{$LogID}' AND TorrentID='{$TorrentID}'");

$DB->query("
UPDATE torrents AS t
JOIN (
	SELECT
		TorrentID,
		MIN(CASE WHEN Adjusted = '1' THEN AdjustedScore ELSE Score END) AS Score,
		MIN(CASE WHEN Adjusted = '1' THEN AdjustedChecksum ELSE Checksum END) AS Checksum
	FROM torrents_logs
	GROUP BY TorrentID
 ) AS tl ON t.ID = tl.TorrentID
SET t.LogScore = tl.Score, t.LogChecksum=tl.Checksum
WHERE t.ID = {$TorrentID}");

if ($IsFLAC && $RangeRip) {
	$DB->query("
		INSERT IGNORE INTO torrents_custom_trumpable
		VALUES
			($TorrentID, " . $LoggedUser['ID'] . ", '" . sqltime() . "', '" . db_string("EAC 整轨方式抓取的分轨资源") . "')");
}

$Cache->delete_value("torrent_group_{$GroupID}");
$Cache->delete_value("torrents_details_{$GroupID}");

header("Location: torrents.php?torrentid={$TorrentID}");
