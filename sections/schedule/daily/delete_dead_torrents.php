<?php

//------------- Delete dead torrents ------------------------------------//

sleep(10);
// expire in 28 days
$DB->query("
		SELECT
			t.ID,
			t.GroupID,
			tg.Name,
			t.Format,
			t.Encoding,
			t.UserID,
			t.Media,
			HEX(t.info_hash) AS InfoHash
		FROM torrents AS t
			JOIN torrents_group AS tg ON tg.ID = t.GroupID
		WHERE
			(t.Time < '" . time_minus(3600 * 24 * 2) . "' AND t.last_action = 0)");
$Torrents = $DB->to_array(false, MYSQLI_NUM, false);
echo 'Found ' . count($Torrents) . " inactive torrents to be deleted.\n";

$LogEntries = $DeleteNotes = array();

// Exceptions for inactivity deletion
$InactivityExceptionsMade = array(
    //UserID => expiry time of exception
);
$i = 0;
foreach ($Torrents as $Torrent) {
    list($ID, $GroupID, $Name, $Format, $Encoding, $UserID, $Media, $InfoHash) = $Torrent;
    if (array_key_exists($UserID, $InactivityExceptionsMade) && (time() < $InactivityExceptionsMade[$UserID])) {
        // don't delete the torrent!
        continue;
    }
    $ArtistName = Artists::display_artists(Artists::get_artist($GroupID), false, false, false, $UserID);
    if ($ArtistName) {
        $Name = "$ArtistName - $Name";
    }
    if ($Format && $Encoding) {
        $Name .= ' [' . (empty($Media) ? '' : "$Media / ") . "$Format / $Encoding]";
    }
    Torrents::delete_torrent($ID, $GroupID);
    $LogEntries[] = db_string("Torrent $ID ($Name) (" . strtoupper($InfoHash) . ") was deleted for inactivity (unseeded)");

    if (!array_key_exists($UserID, $DeleteNotes)) {
        $DeleteNotes[$UserID] = array('Count' => 0, 'Msg' => '');
    }

    $DeleteNotes[$UserID]['Msg'] .= "\n$Name";
    $DeleteNotes[$UserID]['Count']++;

    ++$i;
    if ($i % 500 == 0) {
        echo "$i inactive torrents removed.\n";
    }
}
echo "$i torrents deleted for inactivity.\n";

foreach ($DeleteNotes as $UserID => $MessageInfo) {
    $Singular = (($MessageInfo['Count'] == 1) ? true : false);
    Misc::send_pm($UserID, 0, '你有 ' . $MessageInfo['Count'] . ' 个种子因不活跃而被删除 | ' . $MessageInfo['Count'] . ' of your torrents ' . ($Singular ? 'has' : 'have') . ' been deleted for inactivity', '你发布的种子里有' . ($Singular ? '一个' : '一些') . '因未做种而被删除。由于' . ($Singular ? '它' : '它们') . '并未违反任何规则（但愿如此），你可以重新发布' . ($Singular ? '它' : '它们') . '。下面是删种名录。\n\n[hr]\n' . ($Singular ? 'One' : 'Some') . ' of your uploads ' . ($Singular ? 'has' : 'have') . ' been deleted for being unseeded. Since ' . ($Singular ? 'it' : 'they') . ' didn\'t break any rules (we hope), please feel free to re-upload ' . ($Singular ? 'it' : 'them') . ".\n\nThe following torrent" . ($Singular ? ' was' : 's were') . ' deleted:' . $MessageInfo['Msg']);
}
unset($DeleteNotes);

if (count($LogEntries) > 0) {
    $Values = "('" . implode("', '$sqltime'), ('", $LogEntries) . "', '$sqltime')";
    $DB->query("
			INSERT INTO log (Message, Time)
			VALUES $Values");
    echo "\nDeleted $i torrents for inactivity\n";
}

$DB->query("
		SELECT SimilarID
		FROM artists_similar_scores
		WHERE Score <= 0");
$SimilarIDs = implode(',', $DB->collect('SimilarID'));

if ($SimilarIDs) {
    $DB->query("
			DELETE FROM artists_similar
			WHERE SimilarID IN($SimilarIDs)");
    $DB->query("
			DELETE FROM artists_similar_scores
			WHERE SimilarID IN($SimilarIDs)");
    $DB->query("
			DELETE FROM artists_similar_votes
			WHERE SimilarID IN($SimilarIDs)");
}
