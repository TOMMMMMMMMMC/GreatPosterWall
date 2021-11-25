<?


function get_group_info($GroupID, $Return = true, $RevisionID = 0, $PersonalProperties = true, $ApiCall = false) {
	global $Cache, $DB;
	if (!$RevisionID) {
		$TorrentCache = $Cache->get_value("torrents_details_$GroupID");
	}
	if ($RevisionID || !is_array($TorrentCache)) {
		// Fetch the group details

		$SQL = 'SELECT ';

		if (!$RevisionID) {
			$SQL .= '
				g.WikiBody,
				g.WikiImage, 
				g.IMDBID,
				g.IMDBRating, 
				g.Duration,
				g.ReleaseDate,
				g.Region, 
				g.Language, 
				g.RTRating, 
				g.DoubanRating,
				g.IMDBVote, 
				g.DoubanVote, 
				g.DoubanID, 
				g.RTTitle,';
		} else {
			$SQL .= '
				w.Body,
				w.Image,
				w.IMDBID,
				w.IMDBRating, 
				w.Duration,
				w.ReleaseDate,
				w.Region, 
				w.Language,
				w.RTRating, 
				w.DoubanRating, 
				g.IMDBVote, 
				g.DoubanVote, 
				g.DoubanID, 
				g.RTTitle,';
		}

		$SQL .= "
				g.ID,
				g.Name,
				g.Year,
				g.RecordLabel,
				g.CatalogueNumber,
				g.ReleaseType,
				g.CategoryID,
				g.Time,
				g.VanityHouse,
				GROUP_CONCAT(DISTINCT tags.Name ORDER BY `TagID` SEPARATOR '|'),
				GROUP_CONCAT(DISTINCT tags.ID SEPARATOR '|'),
				GROUP_CONCAT(tt.UserID SEPARATOR '|'),
				GROUP_CONCAT(tt.PositiveVotes SEPARATOR '|'),
				GROUP_CONCAT(tt.NegativeVotes SEPARATOR '|'),
				g.SubName
			FROM torrents_group AS g
				LEFT JOIN torrents_tags AS tt ON tt.GroupID = g.ID
				LEFT JOIN tags ON tags.ID = tt.TagID";

		if ($RevisionID) {
			$SQL .= "
				LEFT JOIN wiki_torrents AS w ON w.PageID = '" . db_string($GroupID) . "'
						AND w.RevisionID = '" . db_string($RevisionID) . "' ";
		}

		$SQL .= "
			WHERE g.ID = '" . db_string($GroupID) . "'
			GROUP BY NULL";

		$DB->query($SQL);
		$TorrentDetails = $DB->next_record(MYSQLI_ASSOC);
		// Fetch the individual torrents

		// TODO by qwerty $PersonalFL, $IsSnatched,
		$DB->query("
			SELECT
				t.ID,
				t.Media,
				t.Format,
				t.Encoding,
				t.Remastered,
				t.RemasterYear,
				t.RemasterTitle,
                		t.RemasterCustomTitle,
				t.RemasterRecordLabel,
				t.RemasterCatalogueNumber,
				t.Scene,
				t.Jinzhuan,
				t.Diy,
				t.Buy,
				t.Allow,
				t.HasLog,
				t.HasCue,
				t.HasLogDB,
				t.LogScore,
				t.LogChecksum,
				t.FileCount,
				t.Size,
				t.Seeders,
				t.Leechers,
				t.Snatched,
				t.FreeTorrent,
				t.Time,
				t.NotMainMovie,
				t.Source,
				t.Codec,
				t.Container,
				t.Resolution,
                		t.Processing,
				t.ChineseDubbed,
				t.SpecialSub,
				t.Subtitles,
				group_concat(sub.languages separator '|') as ExternalSubtitles,
				group_concat(sub.id separator '|') as ExternalSubtitleIDs,
				t.Makers,
				t.Description,
				t.MediaInfo,
				t.Note,
				t.SubtitleType,
				t.FileList,
				t.FilePath,
				t.UserID,
				t.last_action,
				HEX(t.info_hash) AS InfoHash,
				tbt.TorrentID AS BadTags,
				tbf.TorrentID AS BadFolders,
				tbi.TorrentID AS BadImg,
				tfi.TorrentID AS BadFiles,
				tbc.TorrentID AS BadCompress,
				tns.TorrentID AS NoSub,
				ths.TorrentID AS HardSub,
				ml.TorrentID AS MissingLineage,
				tct.CustomTrumpable as CustomTrumpable,
				ca.TorrentID AS CassetteApproved,
				lma.TorrentID AS LossymasterApproved,
				lwa.TorrentID AS LossywebApproved,
				t.LastReseedRequest,
				t.ID AS HasFile,
				COUNT(tl.LogID) AS LogCount,
				fttd.EndTime as FreeEndTime,
				t.Slot,
				t.IsExtraSlot
			FROM torrents AS t
				LEFT JOIN torrents_bad_tags AS tbt ON tbt.TorrentID = t.ID
				LEFT JOIN torrents_bad_folders AS tbf ON tbf.TorrentID = t.ID
				LEFT JOIN torrents_bad_img AS tbi ON tbi.TorrentID = t.ID
				LEFT JOIN torrents_bad_files AS tfi ON tfi.TorrentID = t.ID
				LEFT JOIN torrents_bad_compress AS tbc ON tbc.TorrentID = t.ID
				LEFT JOIN torrents_no_sub AS tns ON tns.TorrentID = t.ID
				LEFT JOIN torrents_hard_sub AS ths ON ths.TorrentID = t.ID
				LEFT JOIN torrents_missing_lineage AS ml ON ml.TorrentID = t.ID
				LEFT JOIN torrents_custom_trumpable AS tct ON tct.TorrentID = t.ID
				LEFT JOIN torrents_cassette_approved AS ca ON ca.TorrentID = t.ID
				LEFT JOIN torrents_lossymaster_approved AS lma ON lma.TorrentID = t.ID
				LEFT JOIN torrents_lossyweb_approved AS lwa ON lwa.TorrentID = t.ID
				LEFT JOIN torrents_logs AS tl ON tl.TorrentID = t.ID
				LEFT JOIN freetorrents_timed as fttd on fttd.TorrentID = t.id
				LEFT JOIN subtitles as sub on sub.torrent_id = t.id
			WHERE t.GroupID = '" . db_string($GroupID) . "'
			GROUP BY t.ID
			ORDER BY t.Source ASC,
                     t.Codec ASC,
                     t.Container ASC, 
                     t.ID");

		$TorrentList = $DB->to_array('ID', MYSQLI_ASSOC, ['MediaInfo']);
		uasort($TorrentList, 'Torrents::sort_torrent');
		if (count($TorrentList) === 0 && $ApiCall == false) {
			header('Location: log.php?search=' . (empty($_GET['torrentid']) ? "Group+$GroupID" : "Torrent+$_GET[torrentid]"));
			die();
		} elseif (count($TorrentList) === 0 && $ApiCall == true) {
			return null;
		}
		if (in_array(0, $DB->collect('Seeders'))) {
			$CacheTime = 600;
		} else {
			$CacheTime = 3600;
		}
		// Store it all in cache
		if (!$RevisionID) {
			$Cache->cache_value("torrents_details_$GroupID", array($TorrentDetails, $TorrentList), $CacheTime);
		}
	} else { // If we're reading from cache
		$TorrentDetails = $TorrentCache[0];
		$TorrentList = $TorrentCache[1];
	}

	if ($PersonalProperties) {
		// Fetch all user specific torrent and group properties
		$TorrentDetails['Flags'] = array('IsSnatched' => false);
		foreach ($TorrentList as &$Torrent) {
			Torrents::torrent_properties($Torrent, $TorrentDetails['Flags']);
		}
	}

	if ($Return) {
		return array($TorrentDetails, $TorrentList);
	}
}

function get_torrent_info($TorrentID, $Return = true, $RevisionID = 0, $PersonalProperties = true, $ApiCall = false) {
	$GroupID = (int)torrentid_to_groupid($TorrentID);
	$GroupInfo = get_group_info($GroupID, $Return, $RevisionID, $PersonalProperties, $ApiCall);
	if ($GroupInfo) {
		foreach ($GroupInfo[1] as &$Torrent) {
			//Remove unneeded entries
			if ($Torrent['ID'] != $TorrentID) {
				unset($GroupInfo[1][$Torrent['ID']]);
			}
			if ($Return) {
				return $GroupInfo;
			}
		}
	} else {
		if ($Return) {
			return null;
		}
	}
}

//Check if a givin string can be validated as a torrenthash
function is_valid_torrenthash($Str) {
	//6C19FF4C 6C1DD265 3B25832C 0F6228B2 52D743D5
	$Str = str_replace(' ', '', $Str);
	if (preg_match('/^[0-9a-fA-F]{40}$/', $Str))
		return $Str;
	return false;
}

//Functionality for the API to resolve input into other data.

function torrenthash_to_torrentid($Str) {
	global $Cache, $DB;
	$DB->query("
		SELECT ID
		FROM torrents
		WHERE HEX(info_hash) = '" . db_string($Str) . "'");
	$TorrentID = (int)array_pop($DB->next_record(MYSQLI_ASSOC));
	if ($TorrentID) {
		return $TorrentID;
	}
	return null;
}

function torrenthash_to_groupid($Str) {
	global $Cache, $DB;
	$DB->query("
		SELECT GroupID
		FROM torrents
		WHERE HEX(info_hash) = '" . db_string($Str) . "'");
	$GroupID = (int)array_pop($DB->next_record(MYSQLI_ASSOC));
	if ($GroupID) {
		return $GroupID;
	}
	return null;
}

function torrentid_to_groupid($TorrentID) {
	global $Cache, $DB;
	$DB->query("
		SELECT GroupID
		FROM torrents
		WHERE ID = '" . db_string($TorrentID) . "'");
	$GroupID = (int)array_pop($DB->next_record(MYSQLI_ASSOC));
	if ($GroupID) {
		return $GroupID;
	}
	return null;
}

//After adjusting / deleting logs, recalculate the score for the torrent.
function set_torrent_logscore($TorrentID) {
	global $DB;
	$DB->query("
		UPDATE torrents
		SET LogScore = (
				SELECT FLOOR(AVG(Score))
				FROM torrents_logs
				WHERE TorrentID = $TorrentID
				)
		WHERE ID = $TorrentID");
}

function get_group_requests($GroupID) {
	if (empty($GroupID) || !is_number($GroupID)) {
		return array();
	}
	global $DB, $Cache;

	$Requests = $Cache->get_value("requests_group_$GroupID");
	if ($Requests === false) {
		$DB->query("
			SELECT ID
			FROM requests
			WHERE GroupID = $GroupID
				AND TimeFilled = '0000-00-00 00:00:00'");
		$Requests = $DB->collect('ID');
		$Cache->cache_value("requests_group_$GroupID", $Requests, 0);
	}
	return Requests::get_requests($Requests);
}

function canCheckTorrent($TorrentID) {
	global $CheckAllTorrents, $CheckSelfTorrents, $LoggedUser;
	if ($CheckAllTorrents) {
		return true;
	} else if ($CheckSelfTorrents) {
		G::$DB->query("select 1 from torrents where userid=" . $LoggedUser['ID'] . " and id=$TorrentID");
		return G::$DB->has_results();
	} else {
		return false;
	}
}
