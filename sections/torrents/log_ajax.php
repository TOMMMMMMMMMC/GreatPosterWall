<?
enforce_login();
$TorrentID = (int) $_GET['torrentid'];
if (!isset($TorrentID) || empty($TorrentID)) {
    error(403);
}
$LogScore = isset($_GET['logscore']) ? intval($_GET['logscore']) : 0;
$DB->query("SELECT LogID, Log, Details, Score, `Checksum`, Adjusted, AdjustedBy, AdjustedScore, AdjustedChecksum, AdjustmentReason, AdjustmentDetails FROM torrents_logs WHERE TorrentID = '$TorrentID'");
if ($DB->record_count() > 0) {
    ob_start();
    echo '<table><tr class=\'colhead_dark\' style=\'font-weight: bold;\'><td>' . Lang::get('torrents', 'this_torrent_has_n_logs_with_score_1') . $DB->record_count() . ' ' . ($DB->record_count() > 1 ? Lang::get('torrents', 'this_torrent_has_n_logs_with_score_logs') : Lang::get('torrents', 'this_torrent_has_n_logs_with_score_log')) . Lang::get('torrents', 'this_torrent_has_n_logs_with_score_2') . $LogScore . ' (' . Lang::get('torrents', 'this_torrent_has_n_logs_with_score_3') . '):</td></tr>';

    if (check_perms('torrents_delete')) {
        echo "<tr class=\'colhead_dark\' style=\'font-weight: bold;\'><td style='text-align:right;'>
			<a onclick=\"return confirm('" . Lang::get('torrents', 'remove_all_logs_confirm') . "');\" href='torrents.php?action=removelogs&amp;torrentid=" . $TorrentID . "'>" . Lang::get('torrents', 'remove_all_logs') . "</a>
	    </td></tr>";
    }
    $StyleDisplay = '';
    $LogNo = $DB->record_count() > 1 ? 1 : 0;
    while ($Log = $DB->next_record(MYSQLI_ASSOC, array('AdjustmentDetails'))) {
        echo "<tr class='log_section'><td>" . ($LogNo ? $LogNo . "&nbsp;&nbsp;&nbsp;" : "");
        if (check_perms('users_mod')) {
            echo "<a class='brackets' href='torrents.php?action=editlog&torrentid={$TorrentID}&logid={$Log['LogID']}'>" . Lang::get('torrents', 'edit_log') . "</a>&nbsp;&nbsp;&nbsp;";
            echo "<a class='brackets' onclick=\"return confirm('" . Lang::get('torrents', 'delete_log_confirm') . "');\" href='torrents.php?action=deletelog&torrentid={$TorrentID}&logid={$Log['LogID']}'>" . Lang::get('torrents', 'delete_log') . "</a>&nbsp;&nbsp;&nbsp;";
        }
        if (file_exists(SERVER_ROOT . "/logs/{$TorrentID}_{$Log['LogID']}.log")) {
            echo "<a class='brackets' href='logs/{$TorrentID}_{$Log['LogID']}.log' target='_blank'>" . Lang::get('torrents', 'view_raw_log') . "</a>&nbsp;&nbsp;&nbsp;";
        }
        echo "<a class='brackets' style=\"float: right;\" href='javascript:void()' onclick='$(\".log_" . $Log['LogID'] . "\").toggle()'>" . Lang::get('torrents', 'toggle_log') . "</a>";
        /*
        if (($Log['Adjusted'] === '0' && $Log['Checksum'] === '0') || ($Log['Adjusted'] === '1' && $Log['AdjustedChecksum'] === '0')) {
            echo <<<HTML
    <blockquote>
        <strong>Trumpable For:</strong>
        <br /><br />
        Bad/No Checksum(s)
    </blockquote>
HTML;
        }
*/

        if ($Log['Adjusted'] === '1') {
            echo '<blockquote>' . Lang::get('torrents', 'log_adjusted_by_user_1') . Users::format_username($Log['AdjustedBy']) . Lang::get('torrents', 'log_adjusted_by_user_2') . "{$Log['Score']}" . Lang::get('torrents', 'log_adjusted_by_user_3') . "{$Log['AdjustedScore']}" . Lang::get('torrents', 'log_adjusted_by_user_4');
            if (!empty($Log['AdjustmentReason'])) {
                echo "<br />" . Lang::get('torrents', 'reason') . ": {$Log['AdjustmentReason']}";
            }
            $AdjustmentDetails = unserialize($Log['AdjustmentDetails']);
            unset($AdjustmentDetails['tracks']);
            if (!empty($AdjustmentDetails)) {
                echo '<br /><strong>' . Lang::get('torrents', 'adjustment_details') . ':</strong><ul>';
                foreach ($AdjustmentDetails as $Entry) {
                    echo '<li>' . $Entry . '</li>';
                }
                echo '</ul>';
            }
            echo '</blockquote>';
        }

        $Log['Details'] = (!empty($Log['Details'])) ? explode("\r\n", trim(Logchecker::translateDetail($Log['Details']))) : array();
        if ($Log['Adjusted'] === '1' && $Log['Checksum'] !== $Log['AdjustedChecksum']) {
            $Log['Details'][] = 'Bad/No Checksum(s)';
        }
        if ($Log['Score'] == 100) {
            $Color = '#418B00';
        } elseif ($Log['Score'] > 90) {
            $Color = '#74C42E';
        } elseif ($Log['Score'] > 75) {
            $Color = '#FFAA00';
        } elseif ($Log['Score'] > 50) {
            $Color = '#FF5E00';
        } else {
            $Color = '#FF0000';
        }
        if (!empty($Log['Details'])) {
            $Extra = ($Log['Adjusted'] === '1') ? 'Original ' : '';
            echo '<blockquote><strong>' . Lang::get('torrents', 'log_score') . ": <span style=\"color: $Color;\">" . $Log['Score'] . '</span></strong>' .
                "<hr$StyleDisplay class=\"divide_score_reasons log_" . $Log['LogID'] . "\"><strong$StyleDisplay class=\"log_" . $Log['LogID'] . "\">" . $Extra . Lang::get('torrents', 'log_validation_report') . ":</strong><ul$StyleDisplay class=\"log_" . $Log['LogID'] . "\">";
            foreach ($Log['Details'] as $Entry) {
                echo '<li>' . $Entry . '</li>';
            }
            echo '</ul></blockquote>';
        } else {
            echo '<blockquote><strong>' . Lang::get('torrents', 'log_score') . ": <span style=\"color: $Color;\">" . $Log['Score'] . '</span></strong></blockquote>';
        }

        echo "<blockquote$StyleDisplay class=\"log_" . $Log['LogID'] . "\"><pre style='white-space:pre-wrap;'>" . html_entity_decode($Log['Log']) . "</pre></blockquote>";
        echo '</td></tr>';
        if (!$StyleDisplay) {
            $StyleDisplay = ' style="display:none;"';
        }
        $LogNo += $LogNo ? 1 : 0;
    }
    echo '</table>';
    echo ob_get_clean();
} else {
    echo '';
}
