<?php

$DB->query("SELECT 
	ID, Processing, Resolution, Codec, SpecialSub, ChineseDubbed, SubtitleType, Subtitles
	FROM 
	torrents where Slot <> 1");
foreach ($DB->to_array('ID', MYSQLI_ASSOC) as $ID => $Torrent) {
	$Slot = Torrents::CalSlot($Torrent);
	$DB->query("UPDATE torrents SET Slot=$Slot WHERE ID=$ID");
}
