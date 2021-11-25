<?
class TorrentSlotGroupStatus {
    const Full = 1; // 满
    const Free = 2; // 有空闲
    const Empty = 3; // 空
}

class TorrentSlotResolution {
    const None = 0;
    const SD = 1;
    const HD720P = 2;
    const HD1080P = 3;
    const UHD = 4;
}

class TorrentSlotType {
    const None = 0;
    const Quality = 1;
    const NTSCUntouched = 2;
    const PALUntouched = 3;
    const Retention = 4;
    const Feature = 5;
    const ChineseQuality = 6;
    const EnglishQuality = 7;
    const X265ChineseQuality = 8;
    const X265EnglishQuality = 9;
    const Remux = 10;
    const DIY = 11;
    const Untouched = 12;
}

class TorrentSlotGroup {
    const SDEncode = 1;
    const SDUntouched = 2;
    const HDEncode = 3;
    const HDUntouched = 4;
    const UHDEncode = 5;
    const UHDUntouched = 6;
}

class Torrents {
    const FILELIST_DELIM = 0xF7; // Hex for &divide; Must be the same as phrase_boundary in sphinx.conf!
    const SNATCHED_UPDATE_INTERVAL = 3600; // How often we want to update users' snatch lists
    const SNATCHED_UPDATE_AFTERDL = 300; // How long after a torrent download we want to update a user's snatch lists

    const SDEncodeSlots = [
        TorrentSlotType::Quality,
        TorrentSlotType::ChineseQuality,
    ];
    const SDUntouchedSlots = [
        TorrentSlotType::NTSCUntouched,
        TorrentSlotType::PALUntouched,
    ];
    const HD720PEncodeSlots = [
        TorrentSlotType::Retention,
        TorrentSlotType::Feature,
        TorrentSlotType::ChineseQuality,
        TorrentSlotType::EnglishQuality,
    ];
    const HD1080PEncodeSlots = [
        TorrentSlotType::Retention,
        TorrentSlotType::Feature,
        TorrentSlotType::ChineseQuality,
        TorrentSlotType::EnglishQuality,
        TorrentSlotType::X265ChineseQuality,
        TorrentSlotType::X265EnglishQuality,
    ];
    const HD720PUntouchedSlots = [
        TorrentSlotType::Remux,
        TorrentSlotType::Untouched,
    ];

    const HD1080PUntouchedSlots = [
        TorrentSlotType::Remux,
        TorrentSlotType::Untouched,
        TorrentSlotType::DIY,
    ];
    const UHDEncodeSlots = [
        TorrentSlotType::Retention,
        TorrentSlotType::Feature,
        TorrentSlotType::ChineseQuality,
        TorrentSlotType::EnglishQuality,
    ];
    const UHDUntouchedSlots = [
        TorrentSlotType::Remux,
        TorrentSlotType::DIY,
        TorrentSlotType::Untouched,
    ];



    const SDSlots = [
        TorrentSlotType::None,
        TorrentSlotType::Quality,
        TorrentSlotType::ChineseQuality,
        TorrentSlotType::NTSCUntouched,
        TorrentSlotType::PALUntouched,
    ];
    const HD720PSlots = [
        TorrentSlotType::None,
        TorrentSlotType::Retention,
        TorrentSlotType::Feature,
        TorrentSlotType::ChineseQuality,
        TorrentSlotType::EnglishQuality,
        TorrentSlotType::Remux,
        TorrentSlotType::Untouched,
    ];
    const HD1080PSlots = [
        TorrentSlotType::None,
        TorrentSlotType::Retention,
        TorrentSlotType::Feature,
        TorrentSlotType::ChineseQuality,
        TorrentSlotType::EnglishQuality,
        TorrentSlotType::X265ChineseQuality,
        TorrentSlotType::X265EnglishQuality,
        TorrentSlotType::Remux,
        TorrentSlotType::DIY,
        TorrentSlotType::Untouched,
    ];
    const UHDSlots = [
        TorrentSlotType::None,
        TorrentSlotType::Retention,
        TorrentSlotType::Feature,
        TorrentSlotType::ChineseQuality,
        TorrentSlotType::EnglishQuality,
        TorrentSlotType::Remux,
        TorrentSlotType::DIY,
        TorrentSlotType::Untouched,
    ];

    const Slots = [
        TorrentSlotResolution::SD => self::SDSlots,
        TorrentSlotResolution::HD720P => self::HD720PSlots,
        TorrentSlotResolution::HD1080P => self::HD1080PSlots,
        TorrentSlotResolution::UHD => self::UHDSlots,
    ];

    const MaxSlotCount = [TorrentSlotType::Quality => 2];

    /**
     * Function to get data and torrents for an array of GroupIDs. Order of keys doesn't matter
     *
     * @param array $GroupIDs
     * @param boolean $Return if false, nothing is returned. For priming cache.
     * @param boolean $GetArtists if true, each group will contain the result of
     *  Artists::get_artists($GroupID), in result[$GroupID]['ExtendedArtists']
     * @param boolean $Torrents if true, each group contains a list of torrents, in result[$GroupID]['Torrents']
     *
     * @return array each row of the following format:
     * GroupID => (
     *  ID
     *  Name
     *  Year
     *  RecordLabel
     *  CatalogueNumber
     *  TagList
     *  ReleaseType
     *  VanityHouse
     *  WikiImage
     *  CategoryID
     *  Torrents => {
     *      ID => {
     *          GroupID, Media, Format, Encoding, RemasterYear, Remastered,
     *          RemasterTitle, RemasterRecordLabel, RemasterCatalogueNumber, Scene, Jinzhuan,
     *          HasLog, HasCue, LogScore, FileCount, FreeTorrent, Size, Leechers,
     *          Seeders, Snatched, Time, HasFile, PersonalFL, IsSnatched
     *      }
     *  }
     *  Artists => {
     *      {
     *          id, name, aliasid // Only main artists
     *      }
     *  }
     *  ExtendedArtists => {
     *      [1-6] => { // See documentation on Artists::get_artists
     *          id, name, aliasid
     *      }
     *  }
     *  Flags => {
     *      IsSnatched
     *  }
     */
    public static function get_groups($GroupIDs, $Return = true, $GetArtists = true, $Torrents = true) {
        $Found = $NotFound = array_fill_keys($GroupIDs, false);
        $Key = $Torrents ? 'torrent_group_' : 'torrent_group_light_';

        foreach ($GroupIDs as $i => $GroupID) {
            if (!is_number($GroupID)) {
                unset($GroupIDs[$i], $Found[$GroupID], $NotFound[$GroupID]);
                continue;
            }
            $Data = G::$Cache->get_value($Key . $GroupID, true);
            if (!empty($Data) && is_array($Data) && $Data['ver'] == CACHE::GROUP_VERSION) {
                unset($NotFound[$GroupID]);
                $Found[$GroupID] = $Data['d'];
            }
        }
        // Make sure there's something in $GroupIDs, otherwise the SQL will break
        if (count($GroupIDs) === 0) {
            return array();
        }

        /*
        Changing any of these attributes returned will cause very large, very dramatic site-wide chaos.
        Do not change what is returned or the order thereof without updating:
            torrents, artists, collages, bookmarks, better, the front page,
        and anywhere else the get_groups function is used.
        Update self::array_group(), too
        */

        if (count($NotFound) > 0) {
            $IDs = implode(',', array_keys($NotFound));
            $NotFound = array();
            $QueryID = G::$DB->get_query_id();
            G::$DB->query("
				SELECT
					ID, CategoryID
				FROM torrents_group
				WHERE ID IN ($IDs)");
            $DiffCategoryGroups = G::$DB->to_array();
            $MusicWayIDs = [];
            $MovieWayIDs = [];
            foreach ($DiffCategoryGroups as $Record) {
                switch ($Record['CategoryID']) {
                    case 1:
                    case 2:
                        //$MusicWayIDs[] = $Record['ID'];
                        break;
                    case 3:
                        //$MovieWayIDs[] = $Record['ID'];
                        break;
                    default:
                        //$MusicWayIDs[] = $Record['ID'];
                }
                $MovieWayIDs[] = $Record['ID'];
            }
            $MusicWayIDs = implode(',', $MusicWayIDs);
            $MovieWayIDs = implode(',', $MovieWayIDs);
            if ($MovieWayIDs) {
                G::$DB->query("
					SELECT
						ID, Name, Year, TagList, ReleaseType, WikiImage, CategoryID, SubName, IMDBID, TrailerLink, IMDBRating, DoubanRating, RTRating, DoubanVote, IMDBVote, DoubanID, RTTitle, Region
					FROM torrents_group
					WHERE ID IN ($MovieWayIDs)");
                while ($Group = G::$DB->next_record(MYSQLI_ASSOC, true)) {
                    $NotFound[$Group['ID']] = $Group;
                    $NotFound[$Group['ID']]['Torrents'] = array();
                    $NotFound[$Group['ID']]['Artists'] = array();
                }
            }

            G::$DB->set_query_id($QueryID);

            if ($Torrents) {
                $QueryID = G::$DB->get_query_id();
                if ($MovieWayIDs) {
                    G::$DB->query("
						SELECT
							t.ID,
							t.GroupID,
							t.Media,
							t.Format,
							t.Encoding,
                            t.Processing,
							t.RemasterYear,
							t.Remastered,
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
							t.LogScore,
							t.FileCount,
							t.FreeTorrent,
							t.Size,
							t.Leechers,
							t.Seeders,
							t.Snatched,
							t.Time,
							t.ID AS HasFile,
							t.HasLogDB,
							t.LogChecksum,
							t.Checked,
							t.NotMainMovie,
							t.Source,
							t.Codec,
							t.Container,
							t.Resolution,
							t.Subtitles,
                            t.ChineseDubbed,
                            t.SpecialSub,
							t.Makers,
							tbt.TorrentID AS BadTags,
							tbf.TorrentID AS BadFolders,
							tbi.TorrentID AS BadImg,
							tfi.TorrentID AS BadFiles,
							tbc.TorrentID AS BadCompress,
							ml.TorrentID AS MissingLineage,
							tct.CustomTrumpable as CustomTrumpable,
							fttd.EndTime as FreeEndTime,
                            t.Slot
						FROM torrents t
							LEFT JOIN torrents_bad_tags AS tbt ON tbt.TorrentID = t.ID
							LEFT JOIN torrents_bad_folders AS tbf ON tbf.TorrentID = t.ID
							LEFT JOIN torrents_bad_img AS tbi ON tbi.TorrentID = t.ID
							LEFT JOIN torrents_bad_files AS tfi ON tfi.TorrentID = t.ID
							LEFT JOIN torrents_bad_compress AS tbc ON tbc.TorrentID = t.ID
							LEFT JOIN torrents_missing_lineage AS ml ON ml.TorrentID = t.ID
							LEFT JOIN torrents_custom_trumpable AS tct ON tct.TorrentID = t.ID
							LEFT JOIN freetorrents_timed as fttd on fttd.TorrentID = t.id
						WHERE t.GroupID IN ($IDs)
						ORDER BY t.ID");
                    while ($Torrent = G::$DB->next_record(MYSQLI_ASSOC, true)) {
                        $NotFound[$Torrent['GroupID']]['Torrents'][$Torrent['ID']] = $Torrent;
                    }
                }
                G::$DB->set_query_id($QueryID);
            }

            foreach ($NotFound as $GroupID => $GroupInfo) {
                G::$Cache->cache_value($Key . $GroupID, array('ver' => CACHE::GROUP_VERSION, 'd' => $GroupInfo), 0);
            }

            $Found = $NotFound + $Found;
        }

        // Filter out orphans (elements that are == false)
        $Found = array_filter($Found);

        if ($GetArtists) {
            $Artists = Artists::get_artists($GroupIDs);
        } else {
            $Artists = array();
        }

        if ($Return) { // If we're interested in the data, and not just caching it
            foreach ($Artists as $GroupID => $Data) {
                if (!isset($Found[$GroupID])) {
                    continue;
                }
                if (array_key_exists(1, $Data) || array_key_exists(4, $Data) || array_key_exists(6, $Data)) {
                    $Found[$GroupID]['Artists'] = isset($Data[1]) ? $Data[1] : null; // Only use main artists (legacy)
                    // TODO: find a better solution than this crap / rewrite the artist system
                    for ($i = 1; $i <= 7; $i++) {
                        $Found[$GroupID]['ExtendedArtists'][$i] = isset($Data[$i]) ? $Data[$i] : null;
                    }
                } else {
                    $Found[$GroupID]['ExtendedArtists'] = false;
                }
            }
            // Fetch all user specific torrent properties
            if ($Torrents) {
                foreach ($Found as &$Group) {
                    $Group['Flags'] = array('IsSnatched' => false);
                    if (!empty($Group['Torrents'])) {
                        foreach ($Group['Torrents'] as &$Torrent) {
                            self::torrent_properties($Torrent, $Group['Flags']);
                        }
                    }
                }
            }
            return $Found;
        }
    }

    /**
     * Returns a reconfigured array from a Torrent Group
     *
     * Use this with extract() instead of the volatile list($GroupID, ...)
     * Then use the variables $GroupID, $GroupName, etc
     *
     * @example  extract(Torrents::array_group($SomeGroup));
     * @param array $Group torrent group
     * @return array Re-key'd array
     */
    public static function array_group(array &$Group) {
        return array(
            'GroupID' => $Group['ID'],
            'GroupName' => $Group['Name'],
            'GroupYear' => $Group['Year'],
            'GroupSubName' => $Group['SubName'],
            'GroupCategoryID' => $Group['CategoryID'],
            'GroupFlags' => isset($Group['Flags']) ? $Group['Flags'] : array('IsSnatched' => false),
            'TagList' => $Group['TagList'],
            'ReleaseType' => $Group['ReleaseType'],
            'WikiImage' => $Group['WikiImage'],
            'Torrents' => isset($Group['Torrents']) ? $Group['Torrents'] : array(),
            'Artists' => $Group['Artists'],
            'ExtendedArtists' => $Group['ExtendedArtists']
        );
    }



    public static function parse_container($Container) {
        global $Containers;
        if (!in_array($Container, $Containers)) {
            return 'Other';
        }
        return $Container;
    }

    public static function parse_codec($Codec) {
        global $Codecs;
        if (!in_array($Codec, $Codecs)) {
            return 'Other';
        }
        return $Codec;
    }

    public static function parse_resolution($Resolution) {
        global $Resolutions;
        if (!in_array($Resolution, $Resolutions)) {
            return 'Other';
        }
        return $Resolution;
    }

    public static function parse_source($Source) {
        global $Sources;
        if (!in_array($Source, $Sources)) {
            return 'Other';
        }
        return $Source;
    }

    /**
     * Supplements a torrent array with information that only concerns certain users and therefore cannot be cached
     *
     * @param array $Torrent torrent array preferably in the form used by Torrents::get_groups() or get_group_info()
     * @param int $TorrentID
     */
    public static function torrent_properties(&$Torrent, &$Flags) {
        $Torrent['PersonalFL'] = in_array($Torrent['FreeTorrent'], ['0', '11', '12', '13']) && self::has_token($Torrent['ID']);
        if ($Torrent['IsSnatched'] = self::has_snatched($Torrent['ID'])) {
            $Flags['IsSnatched'] = true;
        }
    }


    /*
     * Write to the group log.
     *
     * @param int $GroupID
     * @param int $TorrentID
     * @param int $UserID
     * @param string $Message
     * @param boolean $Hidden Currently does fuck all. TODO: Fix that.
     */
    public static function write_group_log($GroupID, $TorrentID, $UserID, $Message, $Hidden) {
        $QueryID = G::$DB->get_query_id();
        G::$DB->query("
			INSERT INTO group_log
				(GroupID, TorrentID, UserID, Info, Time, Hidden)
			VALUES
				($GroupID, $TorrentID, $UserID, '" . db_string($Message) . "', '" . sqltime() . "', $Hidden)");
        G::$DB->set_query_id($QueryID);
    }


    /**
     * Delete a torrent.
     *
     * @param int $ID The ID of the torrent to delete.
     * @param int $GroupID Set it if you have it handy, to save a query. Otherwise, it will be found.
     * @param string $OcelotReason The deletion reason for ocelot to report to users.
     */
    public static function delete_torrent($ID, $GroupID = 0, $OcelotReason = -1) {
        $QueryID = G::$DB->get_query_id();
        if (!$GroupID) {
            G::$DB->query("
				SELECT GroupID, UserID
				FROM torrents
				WHERE ID = '$ID'");
            list($GroupID, $UserID) = G::$DB->next_record();
        }
        if (empty($UserID)) {
            G::$DB->query("
				SELECT UserID
				FROM torrents
				WHERE ID = '$ID'");
            list($UserID) = G::$DB->next_record();
        }

        $RecentUploads = G::$Cache->get_value("recent_uploads_$UserID");
        if (is_array($RecentUploads)) {
            foreach ($RecentUploads as $Key => $Recent) {
                if ($Recent['ID'] == $GroupID) {
                    G::$Cache->delete_value("recent_uploads_$UserID");
                }
            }
        }


        G::$DB->query("
			SELECT info_hash
			FROM torrents
			WHERE ID = $ID");
        list($InfoHash) = G::$DB->next_record(MYSQLI_BOTH, false);
        G::$DB->query("
			DELETE FROM torrents
			WHERE ID = $ID");
        Tracker::update_tracker('delete_torrent', array('info_hash' => rawurlencode($InfoHash), 'id' => $ID, 'reason' => $OcelotReason));

        G::$Cache->decrement('stats_torrent_count');

        G::$DB->query("
			SELECT COUNT(ID)
			FROM torrents
			WHERE GroupID = '$GroupID'");
        list($Count) = G::$DB->next_record();

        if ($Count == 0) {
            Torrents::delete_group($GroupID);
        } else {
            Torrents::update_hash($GroupID);
        }

        // Torrent notifications
        G::$DB->query("
			SELECT UserID
			FROM users_notify_torrents
			WHERE TorrentID = '$ID'");
        while (list($UserID) = G::$DB->next_record()) {
            G::$Cache->delete_value("notifications_new_$UserID");
        }
        G::$DB->query("
			DELETE FROM users_notify_torrents
			WHERE TorrentID = '$ID'");

        G::$DB->query("
			UPDATE reportsv2
			SET
				Status = 'Resolved',
				LastChangeTime = '" . sqltime() . "',
				ModComment = 'Report already dealt with (torrent deleted)'
			WHERE TorrentID = $ID
				AND Status != 'Resolved'");
        $Reports = G::$DB->affected_rows();
        if ($Reports) {
            G::$Cache->decrement('num_torrent_reportsv2', $Reports);
        }

        G::$DB->query("
			DELETE FROM torrents_files
			WHERE TorrentID = '$ID'");
        G::$DB->query("
			DELETE FROM torrents_bad_tags
			WHERE TorrentID = $ID");
        G::$DB->query("
			DELETE FROM torrents_bad_folders
			WHERE TorrentID = $ID");
        G::$DB->query("
			DELETE FROM torrents_bad_files
			WHERE TorrentID = $ID");
        G::$DB->query("
			DELETE FROM torrents_bad_compress
			WHERE TorrentID = $ID");
        G::$DB->query("
			DELETE FROM torrents_missing_lineage
			WHERE TorrentID = $ID");
        G::$DB->query("
			DELETE FROM torrents_cassette_approved
			WHERE TorrentID = $ID");
        G::$DB->query("
			DELETE FROM torrents_lossymaster_approved
			WHERE TorrentID = $ID");
        G::$DB->query("
			DELETE FROM torrents_lossyweb_approved
			WHERE TorrentID = $ID");

        // Tells Sphinx that the group is removed
        G::$DB->query("
			REPLACE INTO sphinx_delta (ID, Time)
			VALUES ($ID, UNIX_TIMESTAMP())");

        G::$Cache->delete_value("torrent_download_$ID");
        G::$Cache->delete_value("torrent_group_$GroupID");
        G::$Cache->delete_value("torrents_details_$GroupID");
        G::$DB->set_query_id($QueryID);
    }

    public static function send_pm($TorrentID, $UploaderID, $Name, $Log, $TrumpID = 0, $PMUploader = false) {
        global $DB;

        $Variable = ['TorrentID' => $TorrentID, 'SiteURL' => site_url(false), 'Name' => $Name, 'Log' => $Log, 'TrumpID' => $TrumpID];
        // Uploader
        if ($PMUploader) {
            $Variable['Action'] = 'Uploaded';
            Misc::send_pm_with_tpl($UploaderID, 'torrent_delete', $Variable);
        }
        $PMedUsers = [$UploaderID];
        // Seeders
        $Extra = implode(',', array_fill(0, count($PMedUsers), '?'));
        $DB->prepared_query("
SELECT DISTINCT(xfu.uid) 
FROM 
	xbt_files_users AS xfu
	JOIN users_info AS ui ON xfu.uid = ui.UserID
WHERE xfu.fid = ? 
	AND ui.NotifyOnDeleteSeeding='1' 
	AND xfu.uid NOT IN ({$Extra})", $TorrentID, ...$PMedUsers);
        $UserIDs = $DB->collect('uid');
        foreach ($UserIDs as $UserID) {
            $Variable['Action'] = 'Seeding';
            Misc::send_pm_with_tpl($UserID, 'torrent_delete', $Variable);
        }
        $PMedUsers = array_merge($PMedUsers, $UserIDs);

        // Snatchers
        $Extra = implode(',', array_fill(0, count($PMedUsers), '?'));
        $DB->prepared_query("
SELECT DISTINCT(xs.uid) 
FROM xbt_snatched AS xs JOIN users_info AS ui ON xs.uid = ui.UserID 
WHERE xs.fid=? AND ui.NotifyOnDeleteSnatched='1' AND xs.uid NOT IN ({$Extra})", $TorrentID, ...$PMedUsers);
        $UserIDs = $DB->collect('uid');
        foreach ($UserIDs as $UserID) {
            $Variable['Action'] = 'Snatched';
            Misc::send_pm_with_tpl($UserID, 'torrent_delete', $Variable);
        }
        $PMedUsers = array_merge($PMedUsers, $UserIDs);

        // Downloaders
        $Extra = implode(',', array_fill(0, count($PMedUsers), '?'));
        $DB->prepared_query("
SELECT DISTINCT(ud.UserID)
FROM users_downloads AS ud JOIN users_info AS ui ON ud.UserID = ui.UserID
WHERE ud.TorrentID=? AND ui.NotifyOnDeleteDownloaded='1' AND ud.UserID NOT IN ({$Extra})", $TorrentID, ...$PMedUsers);
        $UserIDs = $DB->collect('UserID');
        foreach ($UserIDs as $UserID) {
            $Variable['Action'] = 'Downloaded';
            Misc::send_pm_with_tpl($UserID, 'torrent_delete', $Variable);
        }
    }


    /**
     * Delete a group, called after all of its torrents have been deleted.
     * IMPORTANT: Never call this unless you're certain the group is no longer used by any torrents
     *
     * @param int $GroupID
     */
    public static function delete_group($GroupID) {
        $QueryID = G::$DB->get_query_id();

        Misc::write_log("Group $GroupID automatically deleted (No torrents have this group).");

        G::$DB->query("
			SELECT CategoryID
			FROM torrents_group
			WHERE ID = '$GroupID'");
        list($Category) = G::$DB->next_record();
        if ($Category == 1) {
            G::$Cache->decrement('stats_album_count');
        }
        if ($Category == 2) {
            G::$Cache->decrement('stats_drama_count');
        }
        G::$Cache->decrement('stats_group_count');



        // Collages
        G::$DB->query("
			SELECT CollageID
			FROM collages_torrents
			WHERE GroupID = '$GroupID'");
        if (G::$DB->has_results()) {
            $CollageIDs = G::$DB->collect('CollageID');
            G::$DB->query("
				UPDATE collages
				SET NumTorrents = NumTorrents - 1
				WHERE ID IN (" . implode(', ', $CollageIDs) . ')');
            G::$DB->query("
				DELETE FROM collages_torrents
				WHERE GroupID = '$GroupID'");

            foreach ($CollageIDs as $CollageID) {
                G::$Cache->delete_value("collage_$CollageID");
            }
            G::$Cache->delete_value("torrent_collages_$GroupID");
        }

        // Artists
        // Collect the artist IDs and then wipe the torrents_artist entry
        G::$DB->query("
			SELECT ArtistID
			FROM torrents_artists
			WHERE GroupID = $GroupID");
        $Artists = G::$DB->collect('ArtistID');

        G::$DB->query("
			DELETE FROM torrents_artists
			WHERE GroupID = '$GroupID'");

        foreach ($Artists as $ArtistID) {
            if (empty($ArtistID)) {
                continue;
            }
            // Get a count of how many groups or requests use the artist ID
            G::$DB->query("
				SELECT COUNT(ag.ArtistID)
				FROM artists_group AS ag
					LEFT JOIN requests_artists AS ra ON ag.ArtistID = ra.ArtistID
				WHERE ra.ArtistID IS NOT NULL
					AND ag.ArtistID = '$ArtistID'");
            list($ReqCount) = G::$DB->next_record();
            G::$DB->query("
				SELECT COUNT(ag.ArtistID)
				FROM artists_group AS ag
					LEFT JOIN torrents_artists AS ta ON ag.ArtistID = ta.ArtistID
				WHERE ta.ArtistID IS NOT NULL
					AND ag.ArtistID = '$ArtistID'");
            list($GroupCount) = G::$DB->next_record();
            if (($ReqCount + $GroupCount) == 0) {
                //The only group to use this artist
                Artists::delete_artist($ArtistID);
            } else {
                //Not the only group, still need to clear cache
                G::$Cache->delete_value("artist_groups_$ArtistID");
            }
        }

        // Requests
        G::$DB->query("
			SELECT ID
			FROM requests
			WHERE GroupID = '$GroupID'");
        $Requests = G::$DB->collect('ID');
        G::$DB->query("
			UPDATE requests
			SET GroupID = NULL
			WHERE GroupID = '$GroupID'");
        foreach ($Requests as $RequestID) {
            G::$Cache->delete_value("request_$RequestID");
        }

        // comments
        Comments::delete_page('torrents', $GroupID);

        G::$DB->query("
			DELETE FROM torrents_group
			WHERE ID = '$GroupID'");
        G::$DB->query("
			DELETE FROM torrents_tags
			WHERE GroupID = '$GroupID'");
        G::$DB->query("
			DELETE FROM torrents_tags_votes
			WHERE GroupID = '$GroupID'");
        G::$DB->query("
			DELETE FROM bookmarks_torrents
			WHERE GroupID = '$GroupID'");
        G::$DB->query("
			DELETE FROM wiki_torrents
			WHERE PageID = '$GroupID'");

        G::$Cache->delete_value("torrents_details_$GroupID");
        G::$Cache->delete_value("torrent_group_$GroupID");
        G::$Cache->delete_value("groups_artists_$GroupID");
        G::$DB->set_query_id($QueryID);
    }


    /**
     * Update the cache and sphinx delta index to keep everything up-to-date.
     *
     * @param int $GroupID
     */
    public static function update_hash($GroupID) {
        $QueryID = G::$DB->get_query_id();

        G::$DB->query("
			UPDATE torrents_group
			SET TagList = (
					SELECT REPLACE(GROUP_CONCAT(tags.Name SEPARATOR ' '), '.', '_')
					FROM torrents_tags AS t
						INNER JOIN tags ON tags.ID = t.TagID
					WHERE t.GroupID = '$GroupID'
					GROUP BY t.GroupID
					)
			WHERE ID = '$GroupID'");

        // Fetch album vote score
        G::$DB->query("
			SELECT Score
			FROM torrents_votes
			WHERE GroupID = $GroupID");
        if (G::$DB->has_results()) {
            list($VoteScore) = G::$DB->next_record();
        } else {
            $VoteScore = 0;
        }

        // Fetch album artists
        G::$DB->query("
			SELECT GROUP_CONCAT(aa.Name separator ' ')
			FROM torrents_artists AS ta
				JOIN artists_alias AS aa ON aa.AliasID = ta.AliasID
			WHERE ta.GroupID = $GroupID
				AND ta.Importance IN ('1', '4', '5', '6')
			GROUP BY ta.GroupID");
        if (G::$DB->has_results()) {
            list($ArtistName) = G::$DB->next_record(MYSQLI_NUM, false);
        } else {
            $ArtistName = '';
        }

        G::$DB->query("
			REPLACE INTO sphinx_delta
				(ID, GroupID, GroupName, TagList, Year, CategoryID, Time, ReleaseType, Size, Snatched, Seeders, Leechers, Scene, Jinzhuan, Diy, Buy, Allow,
				FreeTorrent,Description, FileList, VoteScore, ArtistName, 
				IMDBRating, DoubanRating, Region, Language, IMDBID, Resolution, Container, Source, Codec, SubTitles)
			SELECT
				t.ID, g.ID, CONCAT_WS(' ', g.Name, g.SubName), TagList, Year, CategoryID, UNIX_TIMESTAMP(t.Time), ReleaseType,
				Size, Snatched, Seeders,
				Leechers, CAST(Scene AS CHAR), CAST(Jinzhuan AS CHAR),  CAST(Diy AS CHAR),  CAST(Buy AS CHAR),  CAST(Allow AS CHAR), 
				CAST(FreeTorrent AS CHAR),Description,
				REPLACE(REPLACE(FileList, '_', ' '), '/', ' ') AS FileList, $VoteScore, '" . db_string($ArtistName) . "',
				IMDBRating, DoubanRating, Region, Language, IMDBID, Resolution, Container, Source, Codec, Subtitles
			FROM torrents AS t
				JOIN torrents_group AS g ON g.ID = t.GroupID
			WHERE g.ID = $GroupID");

        G::$Cache->delete_value("torrents_details_$GroupID");
        G::$Cache->delete_value("torrent_group_$GroupID");
        G::$Cache->delete_value("torrent_group_light_$GroupID");

        $ArtistInfo = Artists::get_artist($GroupID);
        foreach ($ArtistInfo as $Importances => $Importance) {
            foreach ($Importance as $Artist) {
                G::$Cache->delete_value('artist_groups_' . $Artist['id']); //Needed for at least freeleech change, if not others.
            }
        }

        G::$Cache->delete_value("groups_artists_$GroupID");
        G::$DB->set_query_id($QueryID);
    }

    public static function update_slots($TorrentIDs, $TorrentSlots, $GroupID) {
        $SQLArray = [];
        G::$DB->query("SELECT ID, Slot, IsExtraSlot FROM torrents WHERE ID in (" . implode(',', $TorrentIDs) . ")");
        $TorrentOldSlots = G::$DB->to_array('ID', MYSQLI_ASSOC);
        $TorrentNewSlots = array_combine($TorrentIDs, $TorrentSlots);
        $Messages = [];
        foreach ($TorrentNewSlots as $TorrentID => $Slot) {
            $torrentSlot = explode('*', $Slot)[0];
            $isExtraSlot = false;
            if (strpos($Slot, '*')) {
                $isExtraSlot = true;
            }
            $oldExtraSlot = false;
            if (!empty($TorrentOldSlots[$TorrentID]['IsExtraSlot'])) {
                $oldExtraSlot = true;
            }
            if ($TorrentOldSlots[$TorrentID]['Slot'] == $torrentSlot && $oldExtraSlot == $isExtraSlot) {
                continue;
            }
            $SQLArray[] = " ($TorrentID, $torrentSlot, '$isExtraSlot') ";
            $Messages[] = [$TorrentID, $TorrentOldSlots[$TorrentID]['Slot'], $torrentSlot];
        }
        if (count($SQLArray) == 0) {
            return;
        }
        $SQL = implode(',', $SQLArray);
        $SQL = "
        insert into torrents(ID, Slot, IsExtraSlot) VALUES $SQL on duplicate key update Slot=values(Slot), IsExtraSlot=values(IsExtraSlot);";
        G::$DB->query($SQL);
        foreach ($Messages as $Message) {
            $TorrentID = $Message[0];
            $TorrentOldSlot = $Message[1];
            $TorrentNewSlot = $Message[2];
            Misc::write_log("Torrent $TorrentID was edited by " . G::$LoggedUser['Username'] . " (Slot: $TorrentOldSlot -> $TorrentNewSlot)");
        }
        G::$Cache->delete_value("torrents_details_$GroupID");
        G::$Cache->delete_value("torrent_group_$GroupID");
    }

    /**
     * Regenerate a torrent's file list from its meta data,
     * update the database record and clear relevant cache keys
     *
     * @param int $TorrentID
     */
    public static function regenerate_filelist($TorrentID) {
        $QueryID = G::$DB->get_query_id();

        G::$DB->query("
			SELECT tg.ID,
				tf.File
			FROM torrents_files AS tf
				JOIN torrents AS t ON t.ID = tf.TorrentID
				JOIN torrents_group AS tg ON tg.ID = t.GroupID
			WHERE tf.TorrentID = $TorrentID");
        if (G::$DB->has_results()) {
            list($GroupID, $Contents) = G::$DB->next_record(MYSQLI_NUM, false);
            if (Misc::is_new_torrent($Contents)) {
                $Tor = new BencodeTorrent($Contents);
                $FilePath = (isset($Tor->Dec['info']['files']) ? Format::make_utf8($Tor->get_name()) : '');
            } else {
                $Tor = new TORRENT(unserialize(base64_decode($Contents)), true);
                $FilePath = (isset($Tor->Val['info']->Val['files']) ? Format::make_utf8($Tor->get_name()) : '');
            }
            list($TotalSize, $FileList) = $Tor->file_list();
            foreach ($FileList as $File) {
                $TmpFileList[] = self::filelist_format_file($File);
            }
            $FileString = implode("\n", $TmpFileList);
            G::$DB->query("
				UPDATE torrents
				SET Size = $TotalSize, FilePath = '" . db_string($FilePath) . "', FileList = '" . db_string($FileString) . "'
				WHERE ID = $TorrentID");
            G::$Cache->delete_value("torrents_details_$GroupID");
        }
        G::$DB->set_query_id($QueryID);
    }

    /**
     * Return UTF-8 encoded string to use as file delimiter in torrent file lists
     */
    public static function filelist_delim() {
        static $FilelistDelimUTF8;
        if (isset($FilelistDelimUTF8)) {
            return $FilelistDelimUTF8;
        }
        return $FilelistDelimUTF8 = utf8_encode(chr(self::FILELIST_DELIM));
    }

    /**
     * Create a string that contains file info in a format that's easy to use for Sphinx
     *
     * @param array $File (File size, File name)
     * @return string with the format .EXT sSIZEs NAME DELIMITER
     */
    public static function filelist_format_file($File) {
        list($Size, $Name) = $File;
        $Name = Format::make_utf8(strtr($Name, "\n\r\t", '   '));
        $ExtPos = strrpos($Name, '.');
        // Should not be $ExtPos !== false. Extensionless files that start with a . should not get extensions
        $Ext = ($ExtPos ? trim(substr($Name, $ExtPos + 1)) : '');
        return sprintf("%s s%ds %s %s", ".$Ext", $Size, $Name, self::filelist_delim());
    }

    /**
     * Create a string that contains file info in the old format for the API
     *
     * @param string $File string with the format .EXT sSIZEs NAME DELIMITER
     * @return string with the format NAME{{{SIZE}}}
     */
    public static function filelist_old_format($File) {
        $File = self::filelist_get_file($File);
        return $File['name'] . '{{{' . $File['size'] . '}}}';
    }

    /**
     * Translate a formatted file info string into a more useful array structure
     *
     * @param string $File string with the format .EXT sSIZEs NAME DELIMITER
     * @return file info array with the keys 'ext', 'size' and 'name'
     */
    public static function filelist_get_file($File) {
        // Need this hack because filelists are always display_str()ed
        $DelimLen = strlen(display_str(self::filelist_delim())) + 1;
        list($FileExt, $Size, $Name) = explode(' ', $File, 3);
        if ($Spaces = strspn($Name, ' ')) {
            $Name = str_replace(' ', '&nbsp;', substr($Name, 0, $Spaces)) . substr($Name, $Spaces);
        }
        return array(
            'ext' => $FileExt,
            'size' => substr($Size, 1, -1),
            'name' => substr($Name, 0, -$DelimLen)
        );
    }

    public static function display_edition_info($EditionInfo) {
        $t = explode(' / ', $EditionInfo);
        $t = array_map(
            function ($item) {
                return Lang::get('upload', trim($item));
            },
            $t
        );
        return implode(' / ', $t);
    }
    public static function torrent_group_name($Data, $GroupOnly = false, $Link = false, $WithSize = true) {
        $GroupName = !empty($Data['SubName']) ? $Data['SubName'] : $Data['Name'];
        $Year = $Data['Year'];
        if ($GroupOnly) {
            $Ret = $GroupName . ' (' . $Year . ')';
            if ($Link) {
                $GroupID = $Data['GroupID'] ? $Data['GroupID'] : $Data['ID'];
                $Ret = "<a href='torrents.php?id=$GroupID'>$Ret</a>";
            }
        } else {
            $Size = Format::get_size($Data['Size']);
            $Info = self::torrent_media_info($Data);
            $Ret = $GroupName . ' (' . $Year . ') - ' . implode(' / ', $Info) . ($WithSize ? ' - ' . $Size : '');
            if ($Link) {
                $TorrentID = $Data['ID'];
                $Ret = "<a href='torrents.php?torrentid=$TorrentID#$TorrentID'>$Ret</a>";
            }
        }
        // TODO by ljz multiple language support
        return  $Ret;
    }

    private static function torrent_media_info($Data, $Style = false) {
        $Info = array();
        if (!empty($Data['Codec'])) {
            if ($Style) {
                $Info[] = "<span class='codec'>" .  $Data['Codec'] . "</span>";
            } else {
                $Info[] = $Data['Codec'];
            }
        }
        if (!empty($Data['Source'])) {
            if ($Style) {
                $Info[] = "<span class='source'>" .  $Data['Source'] . "</span>";
            } else {
                $Info[] = $Data['Source'];
            }
        }
        if (!empty($Data['Resolution'])) {
            if ($Style) {
                $Info[] = "<span class='resolution'>" .  $Data['Resolution'] . "</span>";
            } else {
                $Info[] = $Data['Resolution'];
            }
        }
        if (!empty($Data['Container'])) {
            if ($Style) {
                $Info[] = "<span class='container'>" .  $Data['Container'] . "</span>";
            } else {
                $Info[] = $Data['Container'];
            }
        }
        if (!empty($Data['Processing']) && $Data['Processing'] != 'Encode' && $Data['Processing'] != '---') {
            if ($Style) {
                $Info[] = "<span class='processing'>" .  $Data['Processing'] . "</span>";
            } else {
                $Info[] = $Data['Processing'];
            }
        }
        return $Info;
    }

    /**
     * Format the information about a torrent.
     * @param array $Data an array a subset of the following keys:
     *  Format, Encoding, HasLog, LogScore, HasCue, c Media, Scene, Jinzhuan, RemasterYear
     *  RemasterTitle, FreeTorrent, PersonalFL
     * @param boolean $ShowMedia if false, Media key will be omitted
     * @param boolean $ShowEdition if false, RemasterYear/RemasterTitle will be omitted
     * @return string
     */
    public static function torrent_info($Data, $ShowMedia = true, $ShowEdition = false, $ShowSize = false) {
        $Info = array();
        if ($ShowMedia) {
            $Info = self::torrent_media_info($Data, true);
        }
        $RemasterYearInfo = '';
        if (!empty($Data['RemasterYear'])) {
            $RemasterYearInfo = " <sup class='remaster_year'><b>" . $Data['RemasterYear'] . "</b></sup>";
        }
        $EditionInfo = array();
        if (!empty($Data['RemasterTitle'])) {
            $EditionInfo = explode(' / ', $Data['RemasterTitle']);
            $showed_labels = ['masters_of_cinema', 'the_criterion_collection', 'warner_archive_collection', 'director_s_cut', 'extended_edition', 'rifftrax', 'theatrical_cut', 'uncut', 'unrated', '2_disc_set', '2_in_1', '2d_3d_edition', '3d_anaglyph', '3d_full_sbs', '3d_half_ou', '3d_half_sbs', '4k_restoration', '4k_remaster', 'remaster', '10_bit', 'dts_x', 'dolby_atmos', 'dolby_vision', 'dual_audio', 'english_dub', 'extras', 'hdr10', 'hdr10plus', 'with_commentary'];
            $EditionInfo = array_map(
                function ($label) {
                    return "<span class='remaster_$label'>" . Lang::get('upload', $label) . "</span>";
                },
                array_intersect($showed_labels, $EditionInfo)
            );
            if (count($EditionInfo)) {
                array_push($EditionInfo, array_pop($EditionInfo) . $RemasterYearInfo);
            }
        }
        if (count($EditionInfo)) {
            $Info[] = implode(' / ', $EditionInfo);
        }
        if (!empty($Data['RemasterCustomTitle'])) {
            $CustomTitle = "<span class='remaster_custom_title'>" . $Data['RemasterCustomTitle'] . "</span>";
            if (!count($EditionInfo)) {
                $CustomTitle .= $RemasterYearInfo;
            }
            $Info[] = $CustomTitle;
        }

        if (!empty($Data['Scene'])) {
            $Info[] = 'Scene';
        }
        if (!empty($Data['Subtitles'])) {
            $Subtitles = explode(',', $Data['Subtitles']);
            if (in_array('chinese_simplified', $Subtitles)) {
                $Info[] = Format::torrent_label(Lang::get('torrents', 'chi'), 'tl_chi');
            } else if (in_array('chinese_traditional', $Subtitles)) {
                $Info[] = Format::torrent_label(Lang::get('torrents', 'chi'), 'tl_chi');
            }
        }
        if ($Data['ChineseDubbed']) {
            $Info[] = Format::torrent_label(Lang::get('torrents', 'cn_dub'), 'tl_diy');
        }
        if ($Data['SpecialSub']) {
            $Info[] = Format::torrent_label(Lang::get('torrents', 'se_sub'), 'tl_diy');
        }
        if ($Data['Buy'] == '1' &&  $Data['Diy'] == '0') {
            $Info[] = Format::torrent_label(Lang::get('torrents', 'buy'), 'tl_diy');
        }
        if ($Data['Diy'] == '1') {
            $Info[] = Format::torrent_label(Lang::get('torrents', 'diy'), 'tl_diy');
        }
        if ($Data['Jinzhuan'] == '1' && $Data['Allow'] == '0') {
            $Info[] = Format::torrent_label(Lang::get('torrents', 'jinzhuan'), 'tl_diy');
        }
        if ($Data['Allow'] == '1') {
            $Info[] = Format::torrent_label(Lang::get('torrents', 'allow'), 'tl_diy');
        }
        if (
            (!empty($Data['BadTags'])) ||
            (!empty($Data['BadFiles'])) ||
            (!empty($Data['BadFolders'])) ||
            (!empty($Data['BadImg'])) ||
            (!empty($Data['BadCompress'])) ||
            (!empty($Data['MissingLineage'])) ||
            (!empty($Data['NoSub'])) ||
            (!empty($Data['HardSub'])) ||
            (!empty($Data['CustomTrumpable'])) ||
            self::is_torrent_dead($Data)
        ) {
            $Info[] = Format::torrent_label(Lang::get('torrents', 'trump'), 'tl_trumpable');
        }




        if (isset($Data['FreeTorrent'])) {
            if ($Data['FreeTorrent'] == '1') {
                $Info[] = Format::torrent_label(Lang::get('torrents', 'fld'), 'tl_free torrent_discount free', ($Data['FreeEndTime'] ? Lang::get('torrents', 'free_left', false, time_diff($Data['FreeEndTime'], 2, false)) : ""));
            } else if ($Data['FreeTorrent'] == '2') {
                $Info[] = Format::torrent_label('Neutral Leech!', 'torrent_discount neutral');
            } else if ($Data['FreeTorrent'] == '11') {
                $Info[] = Format::torrent_label('-25%', 'torrent_discount one_fourth_off', ($Data['FreeEndTime'] ? Lang::get('torrents', 'free_left', false, time_diff($Data['FreeEndTime'], 2, false)) : ""));
            } else if ($Data['FreeTorrent'] == '12') {
                $Info[] = Format::torrent_label('-50%', 'torrent_discount two_fourth_off', ($Data['FreeEndTime'] ? Lang::get('torrents', 'free_left', false, time_diff($Data['FreeEndTime'], 2, false)) : ""));
            } else if ($Data['FreeTorrent'] == '13') {
                $Info[] = Format::torrent_label('-75%', 'torrent_discount three_fourth_off', ($Data['FreeEndTime'] ? Lang::get('torrents', 'free_left', false, time_diff($Data['FreeEndTime'], 2, false)) : ""));
            }
        }
        $Reports = Torrents::get_reports($Data['ID']);
        if (count($Reports) > 0) {
            $Info[] = Format::torrent_label(Lang::get('torrents', 'reported'), 'tl_reported tips-reported');
        }
        if (!empty($Data['PersonalFL'])) {
            $Info[] = Format::torrent_label(Lang::get('torrents', 'pfl'), 'tl_free');
        }
        if (!empty($Data['IsSnatched'])) {
            $Info[] = Format::torrent_label(Lang::get('torrents', 'snatched'));
        }
        return implode(' / ', $Info);
    }


    /**
     * Will freeleech / neutral leech / normalise a set of torrents
     *
     * @param array $TorrentIDs An array of torrent IDs to iterate over
     * @param int $FreeNeutral 0 = normal, 1 = fl, 2 = nl
     * @param int $FreeLeechType 0 = Unknown, 1 = Staff picks, 2 = Perma-FL (Toolbox, etc.), 3 = Vanity House
     */
    public static function freeleech_torrents($TorrentIDs, $FreeNeutral = 1, $FreeLeechType = 0, $Schedule = false) {
        if (!is_array($TorrentIDs)) {
            $TorrentIDs = array($TorrentIDs);
        }

        $QueryID = G::$DB->get_query_id();
        G::$DB->query("
			UPDATE torrents
			SET FreeTorrent = '$FreeNeutral', FreeLeechType = '$FreeLeechType'
			WHERE ID IN (" . implode(', ', $TorrentIDs) . ')');

        G::$DB->query('
			SELECT ID, GroupID, info_hash
			FROM torrents
			WHERE ID IN (' . implode(', ', $TorrentIDs) . ')
			ORDER BY GroupID ASC');
        $Torrents = G::$DB->to_array(false, MYSQLI_NUM, false);
        $GroupIDs = G::$DB->collect('GroupID');
        G::$DB->set_query_id($QueryID);

        foreach ($Torrents as $Torrent) {
            list($TorrentID, $GroupID, $InfoHash) = $Torrent;
            Tracker::update_tracker('update_torrent', array('info_hash' => rawurlencode($InfoHash), 'freetorrent' => $FreeNeutral));
            G::$Cache->delete_value("torrent_download_$TorrentID");
            Misc::write_log(($Schedule ? "Schedule" : G::$LoggedUser['Username']) . " marked torrent $TorrentID freeleech $FreeNeutral type $FreeLeechType!");
            Torrents::write_group_log($GroupID, $TorrentID, $Schedule ? 0 : G::$LoggedUser['ID'], "marked as freeleech $FreeNeutral type $FreeLeechType!", 0);
        }

        foreach ($GroupIDs as $GroupID) {
            Torrents::update_hash($GroupID);
        }
    }


    /**
     * Convenience function to allow for passing groups to Torrents::freeleech_torrents()
     *
     * @param array $GroupIDs the groups in question
     * @param int $FreeNeutral see Torrents::freeleech_torrents()
     * @param int $FreeLeechType see Torrents::freeleech_torrents()
     */
    public static function freeleech_groups($GroupIDs, $FreeNeutral = 1, $FreeLeechType = 0) {
        $QueryID = G::$DB->get_query_id();

        if (!is_array($GroupIDs)) {
            $GroupIDs = array($GroupIDs);
        }

        G::$DB->query('
			SELECT ID
			FROM torrents
			WHERE GroupID IN (' . implode(', ', $GroupIDs) . ')');
        if (G::$DB->has_results()) {
            $TorrentIDs = G::$DB->collect('ID');
            Torrents::freeleech_torrents($TorrentIDs, $FreeNeutral, $FreeLeechType);
        }
        G::$DB->set_query_id($QueryID);
    }


    /**
     * Check if the logged in user has an active freeleech token
     *
     * @param int $TorrentID
     * @return true if an active token exists
     */
    public static function has_token($TorrentID) {
        if (empty(G::$LoggedUser)) {
            return false;
        }

        static $TokenTorrents;
        $UserID = G::$LoggedUser['ID'];
        if (!isset($TokenTorrents)) {
            $TokenTorrents = G::$Cache->get_value("users_tokens_$UserID");
            if ($TokenTorrents === false) {
                $QueryID = G::$DB->get_query_id();
                G::$DB->query("
					SELECT TorrentID
					FROM users_freeleeches
					WHERE UserID = $UserID
						AND Expired = 0");
                $TokenTorrents = array_fill_keys(G::$DB->collect('TorrentID', false), true);
                G::$DB->set_query_id($QueryID);
                G::$Cache->cache_value("users_tokens_$UserID", $TokenTorrents);
            }
        }
        return isset($TokenTorrents[$TorrentID]);
    }


    /**
     * Check if the logged in user can use a freeleech token on this torrent
     *
     * @param int $Torrent
     * @return boolen True if user is allowed to use a token
     */
    public static function can_use_token($Torrent) {
        if (empty(G::$LoggedUser)) {
            return false;
        }

        return (G::$LoggedUser['FLTokens'] >= 0
            && !$Torrent['PersonalFL']
            && (in_array($Torrent['FreeTorrent'], ['11', '12', '13']) || empty($Torrent['FreeTorrent']))
            && G::$LoggedUser['CanLeech'] == '1');
    }

    /**
     * Build snatchlists and check if a torrent has been snatched
     * if a user has the 'ShowSnatched' option enabled
     * @param int $TorrentID
     * @return bool
     */
    public static function has_snatched($TorrentID) {
        if (empty(G::$LoggedUser) || empty(G::$LoggedUser['ShowSnatched'])) {
            return false;
        }

        $UserID = G::$LoggedUser['ID'];
        $Buckets = 64;
        $LastBucket = $Buckets - 1;
        $BucketID = $TorrentID & $LastBucket;
        static $SnatchedTorrents = array(), $UpdateTime = array();

        if (empty($SnatchedTorrents)) {
            $SnatchedTorrents = array_fill(0, $Buckets, false);
            $UpdateTime = G::$Cache->get_value("users_snatched_{$UserID}_time");
            if ($UpdateTime === false) {
                $UpdateTime = array(
                    'last' => 0,
                    'next' => 0
                );
            }
        } elseif (isset($SnatchedTorrents[$BucketID][$TorrentID])) {
            return true;
        }

        // Torrent was not found in the previously inspected snatch lists
        $CurSnatchedTorrents = &$SnatchedTorrents[$BucketID];
        if ($CurSnatchedTorrents === false) {
            $CurTime = time();
            // This bucket hasn't been checked before
            $CurSnatchedTorrents = G::$Cache->get_value("users_snatched_{$UserID}_$BucketID", true);
            if ($CurSnatchedTorrents === false || $CurTime > $UpdateTime['next']) {
                $Updated = array();
                $QueryID = G::$DB->get_query_id();
                if ($CurSnatchedTorrents === false || $UpdateTime['last'] == 0) {
                    for ($i = 0; $i < $Buckets; $i++) {
                        $SnatchedTorrents[$i] = array();
                    }
                    // Not found in cache. Since we don't have a suitable index, it's faster to update everything
                    G::$DB->query("
						SELECT fid
						FROM xbt_snatched
						WHERE uid = '$UserID'");
                    while (list($ID) = G::$DB->next_record(MYSQLI_NUM, false)) {
                        $SnatchedTorrents[$ID & $LastBucket][(int)$ID] = true;
                    }
                    $Updated = array_fill(0, $Buckets, true);
                } elseif (isset($CurSnatchedTorrents[$TorrentID])) {
                    // Old cache, but torrent is snatched, so no need to update
                    return true;
                } else {
                    // Old cache, check if torrent has been snatched recently
                    G::$DB->query("
						SELECT fid
						FROM xbt_snatched
						WHERE uid = '$UserID'
							AND tstamp >= $UpdateTime[last]");
                    while (list($ID) = G::$DB->next_record(MYSQLI_NUM, false)) {
                        $CurBucketID = $ID & $LastBucket;
                        if ($SnatchedTorrents[$CurBucketID] === false) {
                            $SnatchedTorrents[$CurBucketID] = G::$Cache->get_value("users_snatched_{$UserID}_$CurBucketID", true);
                            if ($SnatchedTorrents[$CurBucketID] === false) {
                                $SnatchedTorrents[$CurBucketID] = array();
                            }
                        }
                        $SnatchedTorrents[$CurBucketID][(int)$ID] = true;
                        $Updated[$CurBucketID] = true;
                    }
                }
                G::$DB->set_query_id($QueryID);
                for ($i = 0; $i < $Buckets; $i++) {
                    if (isset($Updated[$i])) {
                        G::$Cache->cache_value("users_snatched_{$UserID}_$i", $SnatchedTorrents[$i], 0);
                    }
                }
                $UpdateTime['last'] = $CurTime;
                $UpdateTime['next'] = $CurTime + self::SNATCHED_UPDATE_INTERVAL;
                G::$Cache->cache_value("users_snatched_{$UserID}_time", $UpdateTime, 0);
            }
        }
        return isset($CurSnatchedTorrents[$TorrentID]);
    }

    /**
     * Change the schedule for when the next update to a user's cached snatch list should be performed.
     * By default, the change will only be made if the new update would happen sooner than the current
     * @param int $Time Seconds until the next update
     * @param bool $Force Whether to accept changes that would push back the update
     */
    public static function set_snatch_update_time($UserID, $Time, $Force = false) {
        if (!$UpdateTime = G::$Cache->get_value("users_snatched_{$UserID}_time")) {
            return;
        }
        $NextTime = time() + $Time;
        if ($Force || $NextTime < $UpdateTime['next']) {
            // Skip if the change would delay the next update
            $UpdateTime['next'] = $NextTime;
            G::$Cache->cache_value("users_snatched_{$UserID}_time", $UpdateTime, 0);
        }
    }

    // Some constants for self::display_string's $Mode parameter
    const DISPLAYSTRING_HTML = 1; // Whether or not to use HTML for the output (e.g. VH tooltip)
    const DISPLAYSTRING_ARTISTS = 2; // Whether or not to display artists
    const DISPLAYSTRING_YEAR = 4; // Whether or not to display the group's year
    const DISPLAYSTRING_VH = 8; // Whether or not to display the VH flag
    const DISPLAYSTRING_RELEASETYPE = 16; // Whether or not to display the release type
    const DISPLAYSTRING_LINKED = 33; // Whether or not to link artists and the group
    // The constant for linking is 32, but because linking only works with HTML, this constant is defined as 32|1 = 33, i.e. LINKED also includes HTML
    // Keep this in mind when defining presets below!

    // Presets to facilitate the use of $Mode
    const DISPLAYSTRING_DEFAULT = 63; // HTML|ARTISTS|YEAR|VH|RELEASETYPE|LINKED = 63
    const DISPLAYSTRING_SHORT = 6; // Very simple format, only artists and year, no linking (e.g. for forum thread titles)

    /**
     * Return the display string for a given torrent group $GroupID.
     * @param int $GroupID
     * @return string
     */
    public static function display_string($GroupID, $Mode = self::DISPLAYSTRING_DEFAULT) {
        global $ReleaseTypes; // I hate this

        $GroupInfo = self::get_groups(array($GroupID), true, true, false)[$GroupID];
        $ExtendedArtists = $GroupInfo['ExtendedArtists'];

        if ($Mode & self::DISPLAYSTRING_ARTISTS) {
            if (
                !empty($ExtendedArtists[1])
                || !empty($ExtendedArtists[4])
                || !empty($ExtendedArtists[5])
                || !empty($ExtendedArtists[6])
            ) {
                unset($ExtendedArtists[2], $ExtendedArtists[3]);
                $DisplayName = Artists::display_artists($ExtendedArtists, ($Mode & self::DISPLAYSTRING_LINKED));
            } else {
                $DisplayName = '';
            }
        }

        if ($Mode & self::DISPLAYSTRING_LINKED) {
            $DisplayName .= "<a href=\"torrents.php?id=$GroupID\" class=\"tooltip\" title=\"" . Lang::get('global', 'view_torrent_group') . "\" dir=\"ltr\">$GroupInfo[Name]</a>";
        } else {
            $DisplayName .= $GroupInfo['Name'];
        }

        if (($Mode & self::DISPLAYSTRING_YEAR) && $GroupInfo['Year'] > 0) {
            $DisplayName .= " [$GroupInfo[Year]]";
        }

        if (($Mode & self::DISPLAYSTRING_VH) && $GroupInfo['VanityHouse']) {
            if ($Mode & self::DISPLAYSTRING_HTML) {
                $DisplayName .= ' [<abbr class="tooltip" title="' . Lang::get('global', 'this_is_vh') . '">VH</abbr>]';
            } else {
                $DisplayName .= ' [VH]';
            }
        }

        if (($Mode & self::DISPLAYSTRING_RELEASETYPE) && $GroupInfo['ReleaseType'] > 0) {
            $DisplayName .= ' [' . $ReleaseTypes[$GroupInfo['ReleaseType']] . ']';
        }

        return $DisplayName;
    }

    public static function update_movie_artist_info($GroupID, $IMDBID, $Refresh = false) {
        G::$DB->query("SELECT 
                        ta.ArtistID,
                        wa.IMDBID,
                        ag.RevisionID as RevisionID,
                        wa.Image as Image,
                        wa.Birthday as Birthday,
                        wa.PlaceOfBirth as PlaceOfBirth,
                        wa.Body as Body
                       FROM 
                        torrents_artists as ta
                       LEFT JOIN 
                        artists_group as ag
                       ON 
                        ag.ArtistID = ta.ArtistID
                       LEFT JOIN
                        wiki_artists as wa
                       ON
                        ag.RevisionID = wa.RevisionID
                       WHERE 
                        GroupID=$GroupID
                       AND
                        wa.IMDBID <> ''");
        $Artists = G::$DB->to_array(false, MYSQLI_ASSOC, false);
        $IMDBIDs = [];
        foreach ($Artists as $Artist) {
            if (empty($Artist['Image'])) {
                $IMDBIDs[] = $Artist['IMDBID'];
            }
        }
        $ArtistInfos = MOVIE::get_artists_seq($IMDBIDs, $IMDBID, $Refresh);
        foreach ($Artists as $Artist) {
            $UpdateSQL = [];
            $ArtistInfo = $ArtistInfos[$Artist['IMDBID']];
            if (empty($Artist['Image']) && $ArtistInfo && $ArtistInfo['Image']) {
                $UpdateSQL[] = "Image = '" . db_string($ArtistInfo['Image']) . "'";
            }
            if (empty($Artist['Body']) && $ArtistInfo && $ArtistInfo['Description']) {
                $UpdateSQL[] = "Body = '" . db_string($ArtistInfo['Description']) . "'";
            }
            if (empty($Artist['PlaceOfBirth']) && $ArtistInfo && $ArtistInfo['PlaceOfBirth']) {
                $UpdateSQL[] = "PlaceOfBirth = '" . db_string($ArtistInfo['PlaceOfBirth']) . "'";
            }
            if (empty($Artist['Birthday']) && $ArtistInfo && $ArtistInfo['Birthday']) {
                $UpdateSQL[] = "Birthday = '" . db_string($ArtistInfo['Birthday']) . "'";
            }
            if (empty($UpdateSQL)) {
                continue;
            }
            $SQL = '
                Update wiki_artists set ' . implode(',', $UpdateSQL) .
                ' WHERE RevisionID = ' . $Artist['RevisionID'] . ' ';
            G::$DB->query($SQL);
            G::$Cache->delete_value('artist_' . $Artist['ArtistID']);
            G::$Cache->delete_value('artist_groups_' . $Artist['ArtistID']);
            G::$Cache->delete_value('groups_artists_' . $GroupID);
            G::$Cache->delete_value("torrent_group_$GroupID");
            G::$Cache->delete_value("torrents_details_$GroupID");
        }
        echo "Update group $GroupID artist info success.\n";
    }

    public static function update_movie_info($GroupID, $IMDBID, $DoubanID = null, $Force = true) {
        if (empty($IMDBID)) {
            return;
        }
        $OMDBData = MOVIE::get_omdb_data($IMDBID, $Force);
        $UpdateSQL = [];
        $UpdateSQL[] = "IMDBID = '" . $IMDBID . "'";
        if ($OMDBData->imdbVotes && $OMDBData->imdbVotes != 'N/A') {
            $UpdateSQL[] = "IMDBVote = " . str_replace(',', '', $OMDBData->imdbVotes);
        }
        if ($OMDBData->imdbRating && $OMDBData->imdbRating != 'N/A') {
            $UpdateSQL[] = "IMDBRating = " . $OMDBData->imdbRating;
        }
        if ($OMDBData->Runtime && $OMDBData->Runtime != 'N/A') {
            $UpdateSQL[] = "Duration = '" . $OMDBData->Runtime . "'";
        }
        if ($OMDBData->Released  && $OMDBData->Released != 'N/A') {
            $UpdateSQL[] = "ReleaseDate = '" . $OMDBData->Released . "'";
        }
        if ($OMDBData->Country  && $OMDBData->Country != 'N/A') {
            $UpdateSQL[] = "Region = '" . $OMDBData->Country . "'";
        }
        if ($OMDBData->Language  && $OMDBData->Language != 'N/A') {
            $UpdateSQL[] = "Language = '" . $OMDBData->Language . "'";
        }
        $RTRating = null;
        foreach ($OMDBData->Ratings as $key => $value) {
            if ($value->Source == "Rotten Tomatoes") {
                $RTRating = $value->Value;
            }
        }
        if ($RTRating) {
            $UpdateSQL[] = "RTRating = '" . $RTRating . "'";
        }
        $DoubanData = null;
        if ($DoubanID) {
            $DoubanData = MOVIE::get_douban_data_by_doubanid($DoubanID, $Force);
        } else {
            $DoubanData = MOVIE::get_douban_data($IMDBID, $Force);
        }
        if ($DoubanData && $DoubanData->rating) {
            $UpdateSQL[] = "DoubanRating = " . $DoubanData->rating;
        }
        if ($DoubanData && $DoubanData->votes) {
            $UpdateSQL[] = "DoubanVote = " . $DoubanData->votes;
        }
        if ($DoubanData && $DoubanData->id) {
            $UpdateSQL[] = "DoubanID = " . $DoubanData->id;
        }
        $SQL = '
        Update torrents_group set ' . implode(',', $UpdateSQL) .
            ' WHERE ID = ' . $GroupID . ' ';
        G::$DB->query($SQL);
        G::$Cache->delete_value("torrent_group_$GroupID");
        G::$Cache->delete_value("torrents_details_$GroupID");
    }

    public static function edition_string(array $Torrent, array $Group = array()) {
        if ($Torrent['Remastered'] && $Torrent['RemasterYear'] != 0) {
            $EditionName = $Torrent['RemasterYear'];
            $AddExtra = ' - ';
            if ($Torrent['RemasterRecordLabel']) {
                $EditionName .= $AddExtra . display_str($Torrent['RemasterRecordLabel']);
                $AddExtra = ' / ';
            }
            if ($Torrent['RemasterCatalogueNumber']) {
                $EditionName .= $AddExtra . display_str($Torrent['RemasterCatalogueNumber']);
                $AddExtra = ' / ';
            }
            if ($Torrent['RemasterTitle']) {
                $EditionName .= $AddExtra . display_str($Torrent['RemasterTitle']);
                $AddExtra = ' / ';
            }
            $EditionName .= $AddExtra . display_str($Torrent['Media']);
        } else {
            $AddExtra = ' / ';
            if (!$Torrent['Remastered']) {
                $EditionName = '原始发行版';
                if ($Group['RecordLabel']) {
                    $EditionName .= $AddExtra . $Group['RecordLabel'];
                    $AddExtra = ' / ';
                }
                if ($Group['CatalogueNumber']) {
                    $EditionName .= $AddExtra . $Group['CatalogueNumber'];
                    $AddExtra = ' / ';
                }
            } else {
                $EditionName = '未知发行';
            }
            $EditionName .= $AddExtra . display_str($Torrent['Media']);
        }
        return $EditionName;
    }

    //Used to get reports info on a unison cache in both browsing pages and torrent pages.
    public static function get_reports($TorrentID) {
        $Reports = G::$Cache->get_value("reports_torrent_$TorrentID");
        if ($Reports === false) {
            $QueryID = G::$DB->get_query_id();
            G::$DB->query("
				SELECT
					ID,
					ReporterID,
					Type,
					UserComment,
					ReportedTime,
					UploaderReply,
					ReplyTime
				FROM reportsv2
				WHERE TorrentID = $TorrentID
					AND Status != 'Resolved'");
            $Reports = G::$DB->to_array(false, MYSQLI_ASSOC, false);
            G::$DB->set_query_id($QueryID);
            G::$Cache->cache_value("reports_torrent_$TorrentID", $Reports, 0);
        }
        if (!check_perms('admin_reports')) {
            $Return = array();
            foreach ($Reports as $Report) {
                if ($Report['Type'] !== 'edited') {
                    $Return[] = $Report;
                }
            }
            return $Return;
        }
        return $Reports;
    }

    private static function resolution_level($a) {
        global $HighDefinition, $StandardDefinition, $UltraDefinition;
        $Resolution = $a['Resolution'];
        if ($a['NotMainMovie']) {
            return SUBGROUP_Extra;
        }
        if (self::is_3d($a['RemasterTitle'])) {
            return SUBGROUP_3D;
        }
        if (in_array($Resolution, $StandardDefinition)) {
            return SUBGROUP_SD;
        } else if (in_array($Resolution, $HighDefinition)) {
            return SUBGROUP_HD;
        } else if (in_array($Resolution, $UltraDefinition)) {
            return SUBGROUP_UHD;
        }
    }
    private static function resolution_value($a) {
        $resolution_val = [
            '480p' => 1,
            'NTSC' => 2,
            '576p' => 3,
            'PAL' => 4,
            '720p' => 5,
            '1080i' => 6,
            '1080p' => 7,
            '2160p' => 8,
        ];
        $value = 0;
        if (!isset($resolution_val[$a['Resolution']])) {
            list($width, $height) = explode('&times;', $a['Resolution']);
            $value = 100000000 + $height * 10000 + $width;
        } else {
            $value = 200000000 + $resolution_val[$a['Resolution']];
        }
        return $value;
    }
    private static function codec_value($a) {
        global $Codecs;
        $v = array_search($a['Codec'], $Codecs);
        if (!$v) {
            $v = 2.5;
        }
        return $v;
    }

    private static function processing_value($a) {
        if (empty($a['Processing']) || $a['Processing'] == '---') {
            return 'Encode';
        }
        if (in_array($a['Processing'], ['BD25', 'BD66', 'BD50', 'BD100', 'DVD9', 'DVD5'])) {
            return 'Untouched';
        }
        return $a['Processing'];
    }
    private static function slot_value($a) {
        $extra = 1;
        if (empty($a['IsSlotExtra'])) {
            $extra = 0;
        }
        return $a['Slot'] * 100 + $extra;
    }


    public static function sort_torrent($a, $b) {
        global $Processings;
        $LevelA = self::resolution_level($a);
        $LevelB = self::resolution_level($b);
        if ($LevelA != $LevelB) {
            return $LevelA < $LevelB ? -1 : 1;
        }
        $ProcessingA = array_search(self::processing_value($a), $Processings);
        $ProcessingB = array_search(self::processing_value($b), $Processings);
        if ($ProcessingA != $ProcessingB) {
            return $ProcessingA < $ProcessingB ? -1 : 1;
        }
        $ResA = self::resolution_value($a);
        $ResB = self::resolution_value($b);
        if ($ResA != $ResB) {
            return $ResA < $ResB ? -1 : 1;
        }

        $SlotA = self::slot_value($a);
        $SlotB = self::slot_value($b);
        if ($SlotA != $SlotB) {
            return $SlotA < $SlotB ? -1 : 1;
        }
        $CodecA = self::codec_value($a);
        $CodecB = self::codec_value($b);
        if ($CodecA != $CodecB) {
            return $CodecA < $CodecB ? -1 : 1;
        }
        $SizeA = $a['Size'];
        $SizeB = $b['Size'];
        if ($SizeA != $SizeB) {
            return $SizeA < $SizeB ? -1 : 1;
        }

        // 不应该走到这里
        return 1;
    }

    public static function get_new_edition_title(
        $LastResolution,
        $LastRemasterTitle,
        $LastRemasterCustomTitle,
        $LastNotMain,
        $Resolution,
        $RemasterTitle,
        $RemasterCustomTitle,
        $NotMain
    ) {
        // TODO bad design
        $lastEdition = self::get_edition($LastResolution, $LastRemasterTitle, $LastRemasterCustomTitle, $LastNotMain);
        $nextEdition = self::get_edition($Resolution, $RemasterTitle, $RemasterCustomTitle, $NotMain);
        if ($lastEdition != $nextEdition) {
            return Lang::get('torrents', $nextEdition);
        }
        return false;
    }

    private static function is_3d($RemasterTitle) {
        foreach (['3d_anaglyph', '3d_full_sbs', '3d_half_ou', '3d_half_sbs'] as $value) {
            if (strstr($RemasterTitle, $value)) {
                return true;
            }
        }
        return false;
    }

    private static function is_extra($RemasterTitle, $RemasterCustomTitle) {

        if (strstr(strtolower($RemasterCustomTitle), "extra")) {
            return true;
        }
        if (strstr($RemasterTitle, "extras")) {
            return true;
        }
        return false;
    }

    private static function get_edition($Resolution, $RemasterTitle, $RemasterCustomTitle, $NotMain) {
        global $HighDefinition, $StandardDefinition, $UltraDefinition;
        if ($NotMain) {
            return "extra_definition";
        }
        if (self::is_3d($RemasterTitle)) {
            return "3d";
        }
        if (in_array($Resolution, $StandardDefinition)) {
            return "group_standard_resolution";
        } else if (in_array($Resolution, $HighDefinition)) {
            return "group_high_resolution";
        } else if (in_array($Resolution, $UltraDefinition)) {
            return "group_ultra_high_resolution";
        } else if (empty($Resolution)) {
            return "";
        }
        return "group_standard_resolution";
    }
    public static function get_slot_resolution($Resolution) {
        global $StandardDefinition, $UltraDefinition;
        if (in_array($Resolution, $StandardDefinition)) {
            return TorrentSlotResolution::SD;
        } else if ($Resolution == '720p') {
            return TorrentSlotResolution::HD720P;
        } else if (in_array($Resolution, ['1080i', '1080p'])) {
            return TorrentSlotResolution::HD1080P;
        } else if (in_array($Resolution, $UltraDefinition)) {
            return TorrentSlotResolution::UHD;
        } else if (empty($Resolution)) {
            return TorrentSlotResolution::None;
        }
        return TorrentSlotResolution::SD;
    }

    public static function get_resolution_slots($Resolution) {
        global $StandardDefinition, $UltraDefinition;
        if (in_array($Resolution, $StandardDefinition)) {
            return self::SDSlots;
        } else if ($Resolution == '720p') {
            return self::HD720PSlots;
        } else if (in_array($Resolution, ['1080i', '1080p'])) {
            return self::HD1080PSlots;
        } else if (in_array($Resolution, $UltraDefinition)) {
            return self::UHDSlots;
        }
        return self::SDSlots;
    }
    public static function display_simple_group_name($GroupInfo, $TorrentID = null, $Style = true) {
        $SubName = $GroupInfo['SubName'];
        $GroupName = $GroupInfo['Name'];
        $GroupYear = $GroupInfo['Year'];
        $DisplayName = '';

        if (!$Style) {
            if ($SubName) {
                $DisplayName .= " [" . $SubName . "] ";
            }
            $DisplayName .= $GroupName;
            if ($GroupYear) {
                $DisplayName .= " ($GroupYear)";
            }
            return $DisplayName;
        }
        $GroupID = $GroupInfo['ID'];
        if ($SubName) {
            $DisplayName .= " [<a href=\"torrents.php?searchstr=" . $SubName . "\">$SubName</a>] ";
        }
        $DisplayName .= "<a href=\"torrents.php?id=$GroupID&amp;torrentid=$TorrentID#torrent$TorrentID\" class=\"tooltip\" title=\"" . Lang::get('global', 'view_torrent') . "\" dir=\"ltr\">$GroupName</a>";
        if ($GroupYear) {
            $DisplayName .= " ($GroupYear)";
        }
        return $DisplayName;
    }

    public static function get_slot_group_status($Torrents) {
        $SDSlotTorrents = [];
        $HD720PSlotTorrents = [];
        $HD1080PSlotTorrents = [];
        $UHDSlotTorrents = [];
        foreach ($Torrents as $Torrent) {
            $Resolution = self::get_slot_resolution($Torrent['Resolution']);
            if ($Resolution == TorrentSlotResolution::SD) {
                if (isset($SDSlotTorrents[$Torrent['Slot']])) {
                    $SDSlotTorrents[$Torrent['Slot']]++;
                } else {
                    $SDSlotTorrents[$Torrent['Slot']] = 1;
                }
            } else if (in_array($Resolution, [TorrentSlotResolution::HD720P])) {
                if (isset($HD720PSlotTorrents[$Torrent['Slot']])) {
                    $HD720PSlotTorrents[$Torrent['Slot']]++;
                } else {
                    $HD720PSlotTorrents[$Torrent['Slot']] = 1;
                }
            } else if (in_array($Resolution, [TorrentSlotResolution::HD1080P])) {
                if (isset($HD1080PSlotTorrents[$Torrent['Slot']])) {
                    $HD1080PSlotTorrents[$Torrent['Slot']]++;
                } else {
                    $HD1080PSlotTorrents[$Torrent['Slot']] = 1;
                }
            } else if ($Resolution == TorrentSlotResolution::UHD) {
                if (isset($UHDSlotTorrents[$Torrent['Slot']])) {
                    $UHDSlotTorrents[$Torrent['Slot']]++;
                } else {
                    $UHDSlotTorrents[$Torrent['Slot']] = 1;
                }
            }
        }
        list($HD720PEncodeStatus, $HD720PEncodeMissSlots) = self::check_slot_status($HD720PSlotTorrents, self::HD720PEncodeSlots);
        list($HD1080PEncodeStatus, $HD10800PEncodeMissSlots) = self::check_slot_status($HD1080PSlotTorrents, self::HD1080PEncodeSlots);
        $HDEncodeStatus = TorrentSlotGroupStatus::Free;
        if ($HD720PEncodeStatus == TorrentSlotGroupStatus::Full && $HD1080PEncodeStatus == TorrentSlotGroupStatus::Full) {
            $HDEncodeStatus = TorrentSlotGroupStatus::Full;
        }
        if ($HD720PEncodeStatus == TorrentSlotGroupStatus::Empty && $HD1080PEncodeStatus == TorrentSlotGroupStatus::Empty) {
            $HDEncodeStatus = TorrentSlotGroupStatus::Empty;
        }
        $HDEncodeMissSlots = $HD720PEncodeMissSlots;
        foreach ($HD10800PEncodeMissSlots as $MissSlot) {
            if (!in_array($MissSlot, $HDEncodeMissSlots)) {
                $HDEncodeMissSlots[] = $MissSlot;
            }
        }

        list($HD720PUntouchedStatus, $HD720PUntouchedMissSlots) = self::check_slot_status($HD720PSlotTorrents, self::HD720PUntouchedSlots);
        list($HD1080PUntouchedStatus, $HD1080PUntouchedMissSlots) = self::check_slot_status($HD1080PSlotTorrents, self::HD1080PUntouchedSlots);
        $HDUntouchedStatus = TorrentSlotGroupStatus::Free;
        if ($HD720PUntouchedStatus == TorrentSlotGroupStatus::Full && $HD1080PUntouchedStatus == TorrentSlotGroupStatus::Full) {
            $HDUntouchedStatus = TorrentSlotGroupStatus::Full;
        }
        if ($HD720PEncodeStatus == TorrentSlotGroupStatus::Empty && $HD1080PUntouchedStatus == TorrentSlotGroupStatus::Empty) {
            $HDUntouchedStatus = TorrentSlotGroupStatus::Empty;
        }
        $HDUntouchedMissSlots = $HD720PUntouchedMissSlots;
        foreach ($HD1080PUntouchedMissSlots as $MissSlot) {
            if (!in_array($MissSlot, $HDEncodeMissSlots)) {
                $HDuntouchedMissSlots[] = $MissSlot;
            }
        }

        return [
            TorrentSlotGroup::SDEncode => self::check_slot_status($SDSlotTorrents, self::SDEncodeSlots),
            TorrentSlotGroup::SDUntouched => self::check_slot_status($SDSlotTorrents, self::SDUntouchedSlots),
            TorrentSlotGroup::HDEncode => [$HDEncodeStatus, $HDEncodeMissSlots],
            TorrentSlotGroup::HDUntouched => [$HDUntouchedStatus, $HDUntouchedMissSlots],
            TorrentSlotGroup::UHDEncode => self::check_slot_status($UHDSlotTorrents, self::UHDEncodeSlots),
            TorrentSlotGroup::UHDUntouched => self::check_slot_status($UHDSlotTorrents, self::UHDUntouchedSlots),
        ];
    }

    private static function check_slot_status($SlotTorrents, $SlotGroup) {
        $allempty = true;
        $allfull = true;
        $MissSlots = [];
        foreach ($SlotGroup as $Slot) {
            $free = false;
            $count = isset($SlotTorrents[$Slot]) ? $SlotTorrents[$Slot] : 0;
            if ($count > 0) {
                $allempty = false;
            }
            if ((isset(self::MaxSlotCount[$Slot]) && $count < self::MaxSlotCount[$Slot]) || $count < 1) {
                $free = true;
                $allfull = false;
            }
            if ($free) {
                $MissSlots[] = $Slot;
            }
        }
        if ($allempty) {
            return [TorrentSlotGroupStatus::Empty, $MissSlots];
        }
        if (!$allfull) {
            return [TorrentSlotGroupStatus::Free, $MissSlots];
        }
        return [TorrentSlotGroupStatus::Full, $MissSlots];
    }

    public static function convert_slot_torrents($Torrents) {
        $SDTorrents = [];
        $HD720PTorrents = [];
        $HD1080PTorrents = [];
        $UHDTorrents = [];
        foreach ($Torrents as $Torrent) {
            $RemasterTitle = $Torrent['RemasterTitle'];
            $RemasterCustomTitle = $Torrent['RemasterCustomTitle'];
            $Resolution = $Torrent['Resolution'];
            $NotMainMovie = $Torrent['NotMainMovie'];
            $Slot = $Torrent['Slot'];
            $IsExtraSlot = $Torrent['IsExtraSlot'];
            if ($IsExtraSlot) {
                $Slot = $Slot . '*';
            }

            if (in_array(self::get_edition($Resolution, $RemasterTitle, $RemasterCustomTitle, $NotMainMovie), ['extra_definition', '3d'])) {
                continue;
            }
            switch (self::get_slot_resolution($Torrent['Resolution'])) {
                case TorrentSlotResolution::SD:
                    $SDTorrents[$Slot][] = $Torrent;
                    break;
                case TorrentSlotResolution::HD720P:
                    $HD720PTorrents[$Slot][] = $Torrent;
                    break;
                case TorrentSlotResolution::HD1080P:
                    $HD1080PTorrents[$Slot][] = $Torrent;
                    break;
                case TorrentSlotResolution::UHD:
                    $UHDTorrents[$Slot][] = $Torrent;
                    break;
            }
        }
        $Ret = [];
        $Missing = [];
        // 720P的任意质量槽如果存在，那么和SD的质量槽就会冲突Dupe
        $Has720PQualitySlot = false;

        $HD720TS = self::filter_slot_torrent(self::HD720PSlots, $HD720PTorrents, '720p');
        foreach ($HD720TS[0] as $T) {
            if (!isset($T['Missing']) && in_array($T['Slot'], [TorrentSlotType::ChineseQuality, TorrentSlotType::EnglishQuality])) {
                $Has720PQualitySlot = true;
            }
        }
        $TS = self::filter_slot_torrent(self::SDSlots, $SDTorrents, 'NTSC');
        foreach ($TS[0] as $T) {
            if ($T['Slot'] == TorrentSlotType::Quality && $Has720PQualitySlot && !isset($T['ExtraSlot'])) {
                $T['Dupe'] = true;
            }
            $Ret[] = $T;
        }
        foreach ($HD720TS[0] as $T) {
            $Ret[] = $T;
        }
        $Missing[TorrentSlotResolution::SD] = $TS[1];
        $Missing[TorrentSlotResolution::HD720P] = $HD720TS[1];
        $TS = self::filter_slot_torrent(self::HD1080PSlots, $HD1080PTorrents, '1080p');
        foreach ($TS[0] as $T) {
            $Ret[] = $T;
        }
        $Missing[TorrentSlotResolution::HD1080P] = $TS[1];
        $TS = self::filter_slot_torrent(self::UHDSlots, $UHDTorrents, '2160p');
        foreach ($TS[0] as $T) {
            $Ret[] = $T;
        }
        $Missing[TorrentSlotResolution::UHD] = $TS[1];
        return [$Ret, $Missing];
    }
    private static function filter_slot_torrent($Slots, $Torrents, $Resolution) {
        $MissingSlot = [];
        foreach ($Slots as $Slot) {
            $SlotTorrents = isset($Torrents[$Slot]) ? $Torrents[$Slot] : [];
            $ExtraSlotTorents = isset($Torrents[$Slot . '*']) ? $Torrents[$Slot . '*'] : [];
            $count = count($SlotTorrents);
            if ($count > 1) {
                if (isset(self::MaxSlotCount[$Slot]) && $count <= self::MaxSlotCount[$Slot]) {
                    foreach ($SlotTorrents as $SlotTorrent) {
                        $Ret[] = $SlotTorrent;
                    }
                } else {
                    foreach ($SlotTorrents as $SlotTorrent) {
                        $SlotTorrent['Dupe'] = true;
                        $Ret[] = $SlotTorrent;
                    }
                }
            } else if ($count <= 0) {
                if ($Slot == TorrentSlotType::None) {
                    continue;
                }
                $MissingSlot[] = $Slot;
                $Ret[] = ['Missing' => true, 'Slot' => $Slot, 'Resolution' => $Resolution];
            } else {
                $Ret[] = $SlotTorrents[0];
            }
            foreach ($ExtraSlotTorents as $ExtraSlotTorrent) {
                $ExtraSlotTorrent['ExtraSlot'] = true;
                $Ret[] = $ExtraSlotTorrent;
            }
        }
        return [$Ret, $MissingSlot];
    }

    public static function CalSlot($Torrent) {
        $Processing = self::processing_value($Torrent);
        $Resolution = $Torrent['Resolution'];
        if (in_array(self::resolution_level($Torrent), [SUBGROUP_3D, SUBGROUP_Extra])) {
            return TorrentSlotType::None;
        }
        $Codec = $Torrent['Codec'];
        $SpecialSub = isset($Torrent['SpecialSub']) && !empty($Torrent['SpecialSub']);
        $ChineseDubbed = isset($Torrent['ChineseDubbed']) && !empty($Torrent['ChineseDubbed']);
        $ChineseSubtitle = isset($Torrent['Subtitles']) && strstr($Torrent['Subtitles'], 'chinese');
        foreach (explode(',', $Torrent['Subtitles']) as $Subtitle) {
            if (!in_array($Subtitle, ['chinese_simplified', 'chinese_traditional', 'english'])) {
                $ChineseSubtitle = false;
                break;
            }
        }
        switch (self::get_slot_resolution($Resolution)) {
            case TorrentSlotResolution::SD:
                if ($Processing == 'Encode') {
                    if ($ChineseSubtitle) {
                        return TorrentSlotType::ChineseQuality;
                    }
                    return TorrentSlotType::Quality;
                }
                if ($Processing == 'Untouched') {
                    if ($Resolution == 'NTSC') {
                        return TorrentSlotType::NTSCUntouched;
                    } else if ($Resolution = 'PAL') {
                        return TorrentSlotType::PALUntouched;
                    }
                }
                return TorrentSlotType::None;
            case TorrentSlotResolution::HD720P:
                if ($Processing == 'Untouched') {
                    return TorrentSlotType::Untouched;
                } else if ($Processing == 'Remux') {
                    return TorrentSlotType::Remux;
                } else {
                    if ($SpecialSub || $ChineseDubbed) {
                        return TorrentSlotType::Feature;
                    }
                    if ($ChineseSubtitle) {
                        return TorrentSlotType::ChineseQuality;
                    } else {
                        return TorrentSlotType::EnglishQuality;
                    }
                }
                return TorrentSlotType::None;
            case TorrentSlotResolution::HD1080P:
                if ($Processing == 'Untouched') {
                    return TorrentSlotType::Untouched;
                } else if ($Processing == 'Remux') {
                    return TorrentSlotType::Remux;
                } else if ($Processing == 'DIY') {
                    return TorrentSlotType::DIY;
                } else {
                    if ($Codec == 'x265' || $Codec == 'H.265') {
                        if ($ChineseSubtitle) {
                            return TorrentSlotType::X265ChineseQuality;
                        } else {
                            return TorrentSlotType::X265EnglishQuality;
                        }
                    } else if ($Codec == 'x264' || $Codec == 'H.264') {
                        if ($SpecialSub || $ChineseDubbed) {
                            return TorrentSlotType::Feature;
                        }
                        if ($ChineseSubtitle) {
                            return TorrentSlotType::ChineseQuality;
                        } else {
                            return TorrentSlotType::EnglishQuality;
                        }
                    }
                }
                return TorrentSlotType::None;
            case TorrentSlotResolution::UHD:
                if ($Processing == 'Untouched') {
                    return TorrentSlotType::Untouched;
                } else if ($Processing == 'Remux') {
                    return TorrentSlotType::Remux;
                } else if ($Processing == 'DIY') {
                    return TorrentSlotType::DIY;
                } else {
                    if ($SpecialSub || $ChineseDubbed) {
                        return TorrentSlotType::Feature;
                    }
                    if ($ChineseSubtitle) {
                        return TorrentSlotType::ChineseQuality;
                    } else {
                        return TorrentSlotType::EnglishQuality;
                    }
                }
                return TorrentSlotType::None;
        }
        return TorrentSlotType::None;
    }


    public static function empty_slot_title($SlotResolution) {
        switch ($SlotResolution) {
            case TorrentSlotResolution::SD:
                return "empty_slots";
            case TorrentSlotResolution::HD720P:
                return "720p_empty_slots";
            case TorrentSlotResolution::HD1080P:
                return "1080p_empty_slots";
            case TorrentSlotResolution::UHD:
                return "empty_slots";
        }
    }


    public static function empty_slot_tooltip($Slot) {
        $str = '';
        switch ($Slot) {
            case TorrentSlotType::Quality:
                $str = 'quality_slot_requirements';
                break;
            case TorrentSlotType::NTSCUntouched:
                $str = 'untouched_slot_requirements';
                break;
            case TorrentSlotType::PALUntouched:
                $str = 'untouched_slot_requirements';
                break;
            case TorrentSlotType::X265ChineseQuality:
                $str = 'cn_quality_slot_requirements';
                break;
            case TorrentSlotType::X265EnglishQuality:
                $str = 'en_quality_slot_requirements';
                break;
            case TorrentSlotType::ChineseQuality:
                $str = 'cn_quality_slot_requirements';
                break;
            case TorrentSlotType::EnglishQuality:
                $str =  'en_quality_slot_requirements';
                break;
            case TorrentSlotType::Retention:
                $str =  'retention_slot_requirements';
                break;
            case TorrentSlotType::Feature:
                $str =  'feature_slot_requirements';
                break;
            case TorrentSlotType::Remux:
                $str =  'remux_slot_requirements';
                break;
            case TorrentSlotType::Untouched:
                $str =  'untouched_slot_requirements';
                break;
            case TorrentSlotType::DIY:
                $str =  'diy_slot_requirements';
                break;
            default:
                return '';
        }
        return Lang::get('torrents', $str);
    }

    public static function slot_option_lang($Slot) {
        switch ($Slot) {
            case TorrentSlotType::Quality:
                return 'quality_slot';
            case TorrentSlotType::NTSCUntouched:
                return 'untouched_slot_ntsc';
            case TorrentSlotType::PALUntouched:
                return 'untouched_slot_pal';
            case TorrentSlotType::X265ChineseQuality:
                return 'cn_quality_slot_x265';
            case TorrentSlotType::X265EnglishQuality:
                return 'en_quality_slot_x265';
            case TorrentSlotType::ChineseQuality:
                return 'cn_quality_slot';
            case TorrentSlotType::EnglishQuality:
                return 'en_quality_slot';
            case TorrentSlotType::Retention:
                return 'retention_slot';
            case TorrentSlotType::Feature:
                return 'feature_slot';
            case TorrentSlotType::Remux:
                return 'remux_slot';
            case TorrentSlotType::Untouched:
                return 'untouched_slot';
            case TorrentSlotType::DIY:
                return 'diy_slot';
        }
        return '';
    }

    public static function slot_option($Slot, $IsExtra, $TorrentSlot, $TorrentIsExtra) {
        $Selected = '';
        if ($Slot == $TorrentSlot && $IsExtra == $TorrentIsExtra) {
            $Selected = 'selected';
        }
        if (empty($Slot)) {
            $text = '---';
        } else {
            $text = Lang::get('torrents', self::slot_option_lang($Slot));
            if ($IsExtra) {
                $text .= '*';
                $Slot .= '*';
            }
        }

        $Ret = "<option $Selected value='$Slot'>$text</option>";
        return $Ret;
    }

    public static function slot_name($Slot) {
        switch ($Slot) {
            case TorrentSlotType::None:
                return "empty";
            case TorrentSlotType::ChineseQuality:
            case TorrentSlotType::X265ChineseQuality:
                return 'cn_quality';
            case TorrentSlotType::Quality:
                return 'quality';
            case TorrentSlotType::EnglishQuality:
            case TorrentSlotType::X265EnglishQuality:
                return 'en_quality';
            case TorrentSlotType::Retention:
                return 'retention';
            case TorrentSlotType::Feature:
                return 'feature';
            case TorrentSlotType::DIY:
                return 'diy';
            case TorrentSlotType::Remux:
                return 'remux';
            case TorrentSlotType::Untouched:
            case TorrentSlotType::NTSCUntouched:
            case TorrentSlotType::PALUntouched:
                return 'untouched';
        }
        return '';
    }


    public static function is_torrent_dead($Torrent) {
        return $Torrent['Seeders'] == 0 && !empty($Torrent['last_action'])  && $Torrent['last_action'] != '0000-00-00 00:00:00' && $Torrent['last_action'] < time_minus(3600 * 24 * 28);
    }
    // New Torrent Name: 安全领域 / Dirty Rotten Scoundrels Year: 1988 Uploader: joey Tags: comedy,documentary Codec: x264 Source: Blu-ray Container: MKV Resolution: 720p Size: 1.1 GB Freeleech: Freeleech! Link: https://greatposterwall.com/torrents.php?id=375
    public static function build_irc_msg($UploaderName, $Torrent) {
        $Freeleech = '';
        switch ($Torrent['FreeTorrent']) {
            case 1:
                $Freeleech = 'Freeleech!';
                break;
            case 11:
                $Freeleech = '25% off!';
                break;
            case 12:
                $Freeleech = '50% off!';
                break;
            case 13:
                $Freeleech = '75% off!';
                break;
        }
        $Link = site_url() . "torrents.php?torrentid={$Torrent['ID']}";
        $Size = Format::get_size($Torrent['Size']);
        return "\002New Torrent\002 \00303Name: " . $Torrent['SubName'] . ' / ' . $Torrent['Name'] . ' Year: ' . $Torrent['Year'] . ' Uploader: ' . $UploaderName . ' Tags: ' . $Torrent['TagList'] . "\003 \00312Codec: " . $Torrent['Codec'] . ' Source: ' . $Torrent['Source'] . ' Container: ' . $Torrent['Container'] . ' Resolution: ' . $Torrent['Resolution'] . ' Size: ' . $Size . ' Freeleech: ' . $Freeleech . "\003 \00304Link: " . $Link . "\003";
    }
}
