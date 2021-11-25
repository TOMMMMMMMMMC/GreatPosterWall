<?

enforce_login();

if (empty($_POST['torrentid'])) {
    error('No torrent is selected.');
}
$TorrentID = intval($_POST['torrentid']) ?? null;
// Some browsers will report an empty file when you submit, prune those out
$_FILES['logfiles']['name'] = array_filter($_FILES['logfiles']['name'], function ($Name) {
    return !empty($Name);
});
$FileCount = count($_FILES['logfiles']['name']);
$Action = in_array($_POST['from_action'], ['upload', 'update']) ? $_POST['from_action'] : 'upload';

$LogScore = 100;
$LogChecksum = 1;

$Extra = check_perms('users_mod') ? '' : " AND t.UserID = '{$LoggedUser['ID']}'";
$DB->query("
	SELECT t.ID, t.GroupID, t.FileList
	FROM torrents t
	WHERE t.ID = {$TorrentID} AND t.HasLog='1'" . $Extra);

$DetailsArray = array();
$Logchecker = new Logchecker();
if ($TorrentID != 0 && $DB->has_results() && $FileCount > 0) {
    list($TorrentID, $GroupID, $FileList) = $DB->next_record(MYSQLI_BOTH);
    $RangeRip = 0;
    $IsFLAC = stripos($FileList, 'flac') !== false;
    $DB->query("SELECT LogID FROM torrents_logs WHERE TorrentID='{$TorrentID}'");
    while (list($LogID) = $DB->next_record(MYSQLI_NUM)) {
        @unlink(SERVER_ROOT . "/logs/{$TorrentID}_{$LogID}.log");
    }
    $DB->query("DELETE FROM torrents_logs WHERE TorrentID='{$TorrentID}'");
    ini_set('upload_max_filesize', 1000000);
    foreach ($_FILES['logfiles']['name'] as $Pos => $File) {
        if (!$_FILES['logfiles']['size'][$Pos]) {
            break;
        }
        $FileName = $_FILES['logfiles']['name'][$Pos];
        $LogPath = $_FILES['logfiles']['tmp_name'][$Pos];
        $Logchecker->new_file($LogPath);
        list($Score, $Details, $Checksum, $LogText) = $Logchecker->parse();
        $Details = trim(implode("\r\n", $Details));
        if (strpos($Details, '$range_rip_detected') !== false) {
            $RangeRip = 1;
        }
        $DetailsArray[] = $Details;
        $LogScore = min($LogScore, $Score);
        $LogChecksum = min(intval($Checksum), $LogChecksum);
        $Logs[] = array($Details, $LogText);
        $DB->query("INSERT INTO torrents_logs (TorrentID, Log, Details, Score, `Checksum`, `FileName`) VALUES ($TorrentID, '" . db_string($LogText) . "', '" . db_string($Details) . "', $Score, '" . enum_boolean($Checksum) . "', '" . db_string($FileName) . "')");
        $LogID = $DB->inserted_id();
        if (move_uploaded_file($LogPath, SERVER_ROOT . "/logs/{$TorrentID}_{$LogID}.log") === false) {
            die("Could not copy logfile to the server.");
        }
    }

    $DB->query("UPDATE torrents SET HasLogDB='1', LogScore={$LogScore}, LogChecksum='" . enum_boolean($LogChecksum) . "' WHERE ID='{$TorrentID}'");
    if ($IsFLAC && $RangeRip) {
        $DB->query("
			INSERT IGNORE INTO torrents_custom_trumpable
			VALUES
				($TorrentID, " . $LoggedUser['ID'] . ", '" . sqltime() . "', '" . db_string("EAC 整轨方式抓取的分轨资源") . "')");
    }
    $Cache->delete_value("torrent_group_{$GroupID}");
    $Cache->delete_value("torrents_details_{$GroupID}");
} else {
    error('No log file uploaded or invalid torrent id was selected.');
}

View::show_header();
?>
<div class="thin center">
    <br><a href="logchecker.php?action=<?= $Action ?>">Upload another log file</a>
</div>
<div class="thin">
    <?

    if ($LogScore == 100) {
        $Color = '#418B00';
    } elseif ($LogScore > 90) {
        $Color = '#74C42E';
    } elseif ($LogScore > 75) {
        $Color = '#FFAA00';
    } elseif ($LogScore > 50) {
        $Color = '#FF5E00';
    } else {
        $Color = '#FF0000';
    }

    echo "<blockquote><strong>" . Lang::get('logchecker', 'log_score') . ":</strong> <span style=\"color:$Color\">$LogScore</span> (" . Lang::get('logchecker', 'out_of_100') . ")</blockquote>";
    /*
if ($LogChecksum === 0) {
    echo <<<HTML
    <blockquote>
        <strong>Trumpable For:</strong>
        <br /><br />
        Bad/No Checksum(s)
    </blockquote>
HTML;
}
*/
    foreach ($Logs as $Log) {
        list($Details, $Text) = $Log;
        $Details = Logchecker::translateDetail($Details);
        if (!empty($Details)) {
            $Details = explode("\r\n", $Details);
    ?>
            <blockquote>
                <h3><?= Lang::get('logchecker', 'log_validation_report') ?>:</h3>
                <ul>
                    <?
                    foreach ($Details as $Property) {
                        print "\t\t<li>{$Property}</li>";
                    }
                    ?>
                </ul>
            </blockquote>
        <?
        }

        ?>
        <blockquote>
            <pre><?= $Text ?></pre>
        </blockquote>
</div>
<?

    }

    View::show_footer();
