<?php

// todo by qwerty temp code
if (!ENABLE_HNR) {
    die();
}

use Gazelle\Util\Time;

$Orders = array('Time' => Lang::get('torrents', 'add_time'), 'Name' => Lang::get('torrents', 'name'), 'Seeders' => Lang::get('torrents', 'seeders'), 'Leechers' => Lang::get('torrents', 'leechers'), 'Snatched' => Lang::get('torrents', 'snatched'), 'Size' => Lang::get('torrents', 'size'));
$Ways = array('ASC' => Lang::get('torrents', 'asc'), 'DESC' => Lang::get('torrents', 'desc'));
$UserVotes = Votes::get_user_votes($LoggedUser['ID']);

// The "order by x" links on columns headers
function header_link($SortKey, $DefaultWay = 'DESC') {
    global $Order, $Way;
    if ($SortKey == $Order) {
        if ($Way == 'DESC') {
            $NewWay = 'ASC';
        } else {
            $NewWay = 'DESC';
        }
    } else {
        $NewWay = $DefaultWay;
    }

    return "torrents.php?way=$NewWay&amp;order=$SortKey&amp;" . Format::get_url(array('way', 'order'));
}

if (!isset($_GET['userid'])) {
    header("Location: torrents.php?type={$_GET['type']}&userid={$LoggedUser['ID']}");
}

$UserID = $_GET['userid'];
$View = $_GET['view'];
if (!is_number($UserID)) {
    error(0);
}

if (!empty($_GET['page']) && is_number($_GET['page']) && $_GET['page'] > 0) {
    $Page = $_GET['page'];
    $Limit = ($Page - 1) * TORRENTS_PER_PAGE . ', ' . TORRENTS_PER_PAGE;
} else {
    $Page = 1;
    $Limit = TORRENTS_PER_PAGE;
}

if (!empty($_GET['order']) && array_key_exists($_GET['order'], $Orders)) {
    $Order = $_GET['order'];
} else {
    $Order = 'Time';
}

if (!empty($_GET['way']) && array_key_exists($_GET['way'], $Ways)) {
    $Way = $_GET['way'];
} else {
    $Way = 'DESC';
}

$SearchWhere = array();
if (!empty($_GET['source'])) {
    if (in_array($_GET['source'], $Sources)) {
        $SearchWhere[] = "t.Source = '" . db_string($_GET['source']) . "'";
    }
}

if (!empty($_GET['codec']) && in_array($_GET['codec'], $Codecs)) {
    $SearchWhere[] = "t.Codec = '" . db_string($_GET['codec']) . "'";
}

if (!empty($_GET['container']) && in_array($_GET['container'], $Containers)) {
    $SearchWhere[] = "t.Container = '" . db_string($_GET['container']) . "'";
}

if (!empty($_GET['resolution']) && in_array($_GET['resolution'], $Resolutions)) {
    $SearchWhere[] = "t.Resolution = '" . db_string($_GET['resolution']) . "'";
}

if (!empty($_GET['processing']) && in_array($_GET['processing'], $Processings)) {
    $SearchWhere[] = "t.Processing = '" . db_string($_GET['processing']) . "'";
}

if (!empty($_GET['releasetype']) && array_key_exists($_GET['releasetype'], $ReleaseTypes)) {
    $SearchWhere[] = "tg.ReleaseType = '" . db_string($_GET['releasetype']) . "'";
}

if (!empty($_GET['categories'])) {
    $Cats = array();
    foreach (array_keys($_GET['categories']) as $Cat) {
        if (!is_number($Cat)) {
            error(0);
        }
        $Cats[] = "tg.CategoryID = '" . db_string($Cat) . "'";
    }
    $SearchWhere[] = '(' . implode(' OR ', $Cats) . ')';
}

if (!isset($_GET['tags_type'])) {
    $_GET['tags_type'] = '1';
}

if (!empty($_GET['tags'])) {
    $Tags = explode(',', $_GET['tags']);
    $TagList = array();
    foreach ($Tags as $Tag) {
        $Tag = trim(str_replace('.', '_', $Tag));
        if (empty($Tag)) {
            continue;
        }
        if ($Tag[0] == '!') {
            $Tag = ltrim(substr($Tag, 1));
            if (empty($Tag)) {
                continue;
            }
            $TagList[] = "CONCAT(' ', tg.TagList, ' ') NOT LIKE '% " . db_string($Tag) . " %'";
        } else {
            $TagList[] = "CONCAT(' ', tg.TagList, ' ') LIKE '% " . db_string($Tag) . " %'";
        }
    }
    if (!empty($TagList)) {
        if (isset($_GET['tags_type']) && $_GET['tags_type'] !== '1') {
            $_GET['tags_type'] = '0';
            $SearchWhere[] = '(' . implode(' OR ', $TagList) . ')';
        } else {
            $_GET['tags_type'] = '1';
            $SearchWhere[] = '(' . implode(' AND ', $TagList) . ')';
        }
    }
}

$SearchWhere = implode(' AND ', $SearchWhere);
if (!empty($SearchWhere)) {
    $SearchWhere = " AND $SearchWhere";
}

$User = Users::user_info($UserID);
$Perms = Permissions::get_permissions($User['PermissionID']);
$UserClass = $Perms['Class'];

if (!check_perms('site_view_torrent_snatchlist')) {
    error(403);
}
$Time = 'unix_timestamp(ud.Time)';


if (!empty($_GET['filter'])) {
    if ($_GET['filter'] === 'perfectflac') {
        if (!check_paranoia('perfectflacs', $User['Paranoia'], $UserClass, $UserID) && !check_perms("users_view_uploaded")) {
            error(403);
        }
        $ExtraWhere .= " AND t.Format = 'FLAC'";
        if (empty($_GET['media'])) {
            $ExtraWhere .= "
				AND (
					(t.LogScore = 100 AND t.LogChecksum = '1' AND t.HasLogDB = '1') OR
					t.Media IN ('Vinyl', 'WEB', 'DVD', 'Soundboard', 'Cassette', 'SACD', 'Blu-ray', 'DAT')
					)";
        } elseif (strtoupper($_GET['media']) === 'CD' && empty($_GET['log'])) {
            $ExtraWhere .= "
				AND t.LogScore = 100";
        }
    } elseif ($_GET['filter'] === 'uniquegroup') {
        if (!check_paranoia('uniquegroups', $User['Paranoia'], $UserClass, $UserID) && !check_perms("users_view_uploaded")) {
            error(403);
        }
        $GroupBy = 'tg.ID';
    } elseif ($_GET['filter'] === 'perfectwav') {
        if (!check_paranoia('perfectwavs', $User['Paranoia'], $UserClass, $UserID) && !check_perms("users_view_uploaded")) {
            error(403);
        }
        $ExtraWhere .= " AND t.Format IN ('FLAC','WAV')";
        if (empty($_GET['media'])) {
            $ExtraWhere .= "
				AND (
					(t.LogScore = 100 AND t.LogChecksum = '1' AND t.HasLogDB = '1') OR
					t.Media IN ('Vinyl', 'WEB', 'DVD', 'Soundboard', 'Cassette', 'SACD', 'Blu-ray', 'DAT')
					)";
        } elseif (strtoupper($_GET['media']) === 'CD' && empty($_GET['log'])) {
            $ExtraWhere .= "
				AND t.LogScore = 100";
        }
    } elseif ($_GET['filter'] === 'lossless') {
        if (!check_paranoia('lossless', $User['Paranoia'], $UserClass, $UserID) && !check_perms("users_view_uploaded")) {
            error(403);
        }
        $ExtraWhere .= " AND t.Format IN ('FLAC','WAV','DSD')";
    } elseif ($_GET['filter'] === 'original') {
        if (!check_paranoia('original', $User['Paranoia'], $UserClass, $UserID) && !check_perms("users_view_uploaded")) {
            error(403);
        }
        $ExtraWhere .= " AND (t.Buy='1' or t.Diy='1')";
    } elseif ($_GET['filter'] === 'original_buy') {
        if (!check_paranoia('original', $User['Paranoia'], $UserClass, $UserID) && !check_perms("users_view_uploaded")) {
            error(403);
        }
        $ExtraWhere .= " AND t.Buy='1'";
    } elseif ($_GET['filter'] === 'original_diy') {
        if (!check_paranoia('original', $User['Paranoia'], $UserClass, $UserID) && !check_perms("users_view_uploaded")) {
            error(403);
        }
        $ExtraWhere .= " AND t.Diy='1'";
    }
}

if (empty($GroupBy)) {
    $GroupBy = 't.ID';
}

$Time = '(xfu.mtime - xfu.timespent)';
$UserField = 'xfu.uid';
$ExtraWhere = '
    AND xfu.active = 1
    AND xfu.Remaining = 0';
$From = "
    xbt_files_users AS xfu
        JOIN torrents AS t ON t.ID = xfu.fid";

$HNR_INTERVAL = HNR_INTERVAL;
$HNR_MIN_MIN_RATIO = HNR_MIN_MIN_RATIO;
$HNR_MIN_SIZE_PERCENT = HNR_MIN_SIZE_PERCENT;
$HNR_MIN_SEEEDING_TIME = HNR_MIN_SEEEDING_TIME;
if ($View == 1) {
    $SearchWhere .= " AND ut.real_downloaded > t.Size * $HNR_MIN_SIZE_PERCENT
    AND (ut.seedtime < $HNR_MIN_SEEEDING_TIME or ut.real_uploaded <= 0 or ut.real_uploaded / ut.real_downloaded < $HNR_MIN_MIN_RATIO)
    AND unix_timestamp(now()) - unix_timestamp(ud.Time) > $HNR_INTERVAL
    AND th.torrent_id is null";
}
if ((empty($_GET['search']) || trim($_GET['search']) === '') && $Order != 'Name') {
    $SQL = "
		SELECT
			SQL_CALC_FOUND_ROWS
			t.GroupID,
			t.ID AS TorrentID,
			UNIX_TIMESTAMP(ud.Time) AS Time,
			tg.CategoryID,
            ut.seedtime,
            t.Seeders,
			t.Leechers,
            ut.real_downloaded,
            ut.real_uploaded,
            ut.snatched,
            xfu.active,
            xfu.remaining,
            th.torrent_id as eliminate
		FROM users_downloads AS ud 
            LEFT JOIN users_torrents AS ut on ut.uid = ud.UserID and ut.fid = ud.TorrentID
            LEFT JOIN xbt_files_users AS xfu on xfu.uid = ud.UserID and xfu.fid = ud.TorrentID
            JOIN torrents AS t ON t.ID = ud.TorrentID
			JOIN torrents_group AS tg ON tg.ID = t.GroupID
            LEFT JOIN torrents_hnr as th ON th.user_id= ud.UserID and th.torrent_id = ud.TorrentID
		WHERE ud.UserID = '$UserID'
			$SearchWhere
		GROUP BY $GroupBy
		ORDER BY $Order $Way
		LIMIT $Limit";
} else {
    $DB->query("
		CREATE TEMPORARY TABLE temp_sections_torrents_user (
			GroupID int(10) unsigned not null,
			TorrentID int(10) unsigned not null,
			Time int(12) unsigned not null,
			CategoryID int(3) unsigned,
			Seeders int(6) unsigned,
			Leechers int(6) unsigned,
			Name mediumtext,
			Size bigint(12) unsigned,
            seedtime bigint(20) unsigned,
            real_downloaded bigint(20) unsigned,
            real_uploaded bigint(20) unsigned,
            snatched bigint(20) unsigned,
            active tinyint(1),
            remaining bigint(20) unsigned,
		PRIMARY KEY (TorrentID)) CHARSET=utf8");
    $DB->query("
		INSERT IGNORE INTO temp_sections_torrents_user
			SELECT
				t.GroupID,
				t.ID AS TorrentID,
				ud.Time AS Time,
				tg.CategoryID,
				t.Seeders,
				t.Leechers,
				CONCAT_WS(' ', GROUP_CONCAT(aa.Name SEPARATOR ' '), ' ', tg.Name, ' ', tg.Year, ' ', tg.SubName, ' ', tg.IMDBID, ' ') AS Name,
				t.Size,
                ut.seedtime,
                ut.real_downloaded,
                ut.real_uploaded,
                ut.snatched,
                xfu.active,
                xfu.remaining,
                th.torrent_id as eliminate
			FROM users_downloads AS ud 
                JOIN torrents AS t ON t.ID = ud.TorrentID
                LEFT JOIN users_torrents AS ut on ut.uid = ud.UserID and ut.fid = ud.TorrentID
                LEFT JOIN xbt_files_users AS xfu on xfu.uid = ud.UserID and xfu.fid = ud.TorrentID
				JOIN torrents_group AS tg ON tg.ID = t.GroupID
				LEFT JOIN torrents_artists AS ta ON ta.GroupID = tg.ID
				LEFT JOIN artists_alias AS aa ON aa.AliasID = ta.AliasID
                LEFT JOIN torrents_hnr as th ON th.user_id= ud.UserID and th.torrent_id = ud.TorrentID
			WHERE ud.UserID = '$UserID'
				$SearchWhere
			GROUP BY TorrentID, Time");

    if (!empty($_GET['search']) && trim($_GET['search']) !== '') {
        $Words = array_unique(explode(' ', db_string($_GET['search'])));
    }

    $SQL = "
		SELECT
			SQL_CALC_FOUND_ROWS
			GroupID,
			TorrentID,
			UNIX_TIMESTAMP(Time),
			CategoryID,
            Seeders,
			Leechers,
            seedtime,
            real_downloaded,
            real_uploaded,
            snatched,
            active,
            remaining,
            eliminate      
		FROM temp_sections_torrents_user";
    if (!empty($Words)) {
        $SQL .= "
		WHERE Name LIKE '%" . implode("%' AND Name LIKE '%", $Words) . "%'";
    }
    $SQL .= "
		ORDER BY $Order $Way
		LIMIT $Limit";
}

$DB->query($SQL);
$GroupIDs = $DB->collect('GroupID');
$TorrentsInfo = $DB->to_array('TorrentID', MYSQLI_ASSOC);

$DB->query('SELECT FOUND_ROWS()');
list($TorrentCount) = $DB->next_record();

$Results = Torrents::get_groups($GroupIDs);

$Action = display_str($_GET['type']);
$User = Users::user_info($UserID);

View::show_header($User['Username'] . Lang::get('torrents', 'user_s') . Lang::get('torrents', 'action_' . $Action) . Lang::get('torrents', 'action_torrents'), 'voting');

$Pages = Format::get_pages($Page, $TorrentCount, TORRENTS_PER_PAGE);


?>
<div class="thin">
    <div class="header">
        <h2><a href="user.php?id=<?= $UserID ?>"><?= $User['Username'] ?></a><?= Lang::get('torrents', 'user_s') . Lang::get('torrents', 'action_' . $Action) . Lang::get('torrents', 'action_torrents') ?></h2>
    </div>
    <div>
        <form class="search_form" name="torrents" action="" method="get">
            <table class="layout">
                <tr>
                    <td class="label"><strong><?= Lang::get('torrents', 'search_for') ?>:</strong></td>
                    <td>
                        <input type="hidden" name="type" value="<?= $_GET['type'] ?>" />
                        <input type="hidden" name="userid" value="<?= $UserID ?>" />
                        <input type="search" name="search" size="60" value="<? Format::form('search') ?>" />
                    </td>
                </tr>
                <tr>
                    <td class="label"><strong><?= Lang::get('torrents', 'ft_ripspecifics') ?>:</strong></td>
                    <td class="nobr" colspan="3">
                        <select id="source" name="source" class="ft_source fti_advanced">
                            <option value=""><?= Lang::get('torrents', 'source') ?></option>
                            <? foreach ($Sources as $SourceName) { ?>
                                <option value="<?= display_str($SourceName); ?>" <? Format::selected('source', $SourceName) ?>><?= display_str($SourceName); ?></option>
                            <?  } ?>
                        </select>

                        <select name="codec" class="ft_codec fti_advanced">
                            <option value=""><?= Lang::get('torrents', 'codec') ?></option>
                            <? foreach ($Codecs as $CodecName) { ?>
                                <option value="<?= display_str($CodecName); ?>" <? Format::selected('codec', $CodecName) ?>><?= display_str($CodecName); ?></option>
                            <?  } ?>
                        </select>
                        <select name="container" class="ft_container fti_advanced">
                            <option value=""><?= Lang::get('torrents', 'container') ?></option>
                            <? foreach ($Containers as $ContainerName) { ?>
                                <option value="<?= display_str($ContainerName); ?>" <? Format::selected('container', $ContainerName) ?>><?= display_str($ContainerName); ?></option>
                            <?  } ?>
                        </select>
                        <select name="resolution" class="ft_resolution fti_advanced">
                            <option value=""><?= Lang::get('torrents', 'resolution') ?></option>
                            <? foreach ($Resolutions as $ResolutionName) { ?>
                                <option value="<?= display_str($ResolutionName); ?>" <? Format::selected('resolution', $ResolutionName) ?>><?= display_str($ResolutionName); ?></option>
                            <?  } ?>
                        </select>
                        <select name="processing" class="ft_container fti_advanced">
                            <option value=""><?= Lang::get('torrents', 'processing') ?></option>
                            <? foreach ($Processings as $ProcessingName) { ?>
                                <option value="<?= display_str($ProcessingName); ?>" <? Format::selected('processing', $ProcessingName) ?>><?= display_str($ProcessingName); ?></option>
                            <?  } ?>
                        </select>
                        <select name="releasetype" class="ft_releasetype fti_advanced">
                            <option value=""><?= Lang::get('torrents', 'ft_releasetype') ?></option>
                            <? foreach ($ReleaseTypes as $ID => $Type) { ?>
                                <option value="<?= display_str($ID); ?>" <? Format::selected('releasetype', $ID) ?>><?= display_str(Lang::get('torrents', 'release_types')[$ID]); ?></option>
                            <?  } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="label"><strong><?= Lang::get('torrents', 'misc') ?>:</strong></td>
                    <td class="nobr" colspan="3">
                        <input type="checkbox" class="ft_hnr" name="view" <?= $View == 1 ? "checked" : "" ?> id="only_hnr" value="1"><label><?= Lang::get('torrents', 'only_hnr') ?></label>
                        <select name="scene" class="ft_scene">
                            <option value=""><?= Lang::get('torrents', 'scene') ?></option>
                            <option value="1" <? Format::selected('scene', 1) ?>><?= Lang::get('torrents', 'yes') ?></option>
                            <option value="0" <? Format::selected('scene', 0) ?>><?= Lang::get('torrents', 'no') ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="label"><strong><?= Lang::get('torrents', 'tags') ?>:</strong></td>
                    <td>
                        <input type="search" name="tags" size="60" class="tooltip" title="Use !tag to exclude tag" value="<? Format::form('tags') ?>" />&nbsp;
                        <label for="tags_type0"><input type="radio" name="tags_type" id="tags_type0" value="0" <? Format::selected('tags_type', 0, 'checked') ?> /> <?= Lang::get('torrents', 'any') ?></label>&nbsp;&nbsp;
                        <label for="tags_type1"><input type="radio" name="tags_type" id="tags_type1" value="1" <? Format::selected('tags_type', 1, 'checked') ?> /> <?= Lang::get('torrents', 'all') ?></label>
                    </td>
                </tr>

                <tr>
                    <td class="label"><strong><?= Lang::get('torrents', 'ft_order') ?>:</strong></td>
                    <td>
                        <select name="order" class="ft_order_by">
                            <? foreach ($Orders as $OrderKey => $OrderText) { ?>
                                <option value="<?= $OrderKey ?>" <? Format::selected('order', $OrderKey) ?>><?= $OrderText ?></option>
                            <?    } ?>
                        </select>
                        <select name="way" class="ft_order_way">
                            <? foreach ($Ways as $WayKey => $WayText) { ?>
                                <option value="<?= $WayKey ?>" <? Format::selected('way', $WayKey) ?>><?= $WayText ?></option>
                            <?    } ?>
                        </select>
                    </td>
                </tr>
            </table>

            <table class="layout cat_list">
                <?
                $x = 0;
                reset($Categories);
                foreach ($Categories as $CatKey => $CatName) {
                    if ($x % 7 === 0) {
                        if ($x > 0) {
                ?>
                            </tr>
                        <?        } ?>
                        <tr>
                        <?
                    }
                    $x++;
                        ?>
                        <td>
                            <input type="checkbox" name="categories[<?= ($CatKey + 1) ?>]" id="cat_<?= ($CatKey + 1) ?>" value="1" <? if (isset($_GET['categories'][$CatKey + 1])) { ?> checked="checked" <? } ?> />
                            <label for="cat_<?= ($CatKey + 1) ?>"><?= $CatName ?></label>
                        </td>
                    <?
                }
                    ?>
                        </tr>
            </table>
            <div class="submit">
                <input type="submit" value="Search torrents" />
            </div>
        </form>
    </div>
    <? if (count($GroupIDs) === 0) { ?>
        <div class="center"><?= Lang::get('torrents', 'nothing_found') ?></div>
    <?    } else { ?>
        <div class="linkbox"><?= $Pages ?></div>
        <table id="user_seeding_torrents" class="torrent_table cats m_table" width="100%">
            <tr class="colhead">
                <td class="m_th_left"><a href="<?= header_link('Name', 'ASC') ?>"><?= Lang::get('torrents', 'torrent') ?></a></td>
                <td><i class="fas fa-hdd tooltip" title="<?= Lang::get('torrents', 'size') ?>"></i></td>
                <td class="number_column"><i class="fas fa-arrow-up tooltip" title="<?= Lang::get('global', 'uploaded') ?>"></i></td>
                <td class="number_column"><i class="fas fa-arrow-down tooltip" title="<?= Lang::get('global', 'downloaded') ?>"></i></td>
                <td class="number_column"><i class="fas fa-percentage tooltip" title="<?= Lang::get('global', 'ratio') ?>"></i></td>
                <td class="number_column sign seeders m_th_right">
                    <a href="<?= header_link('Seeders') ?>">
                        <i class="fa fa-upload tooltip" aria-hidden="true" alt="Seeders" title="<?= Lang::get('torrents', 'seeders') ?>"></i>
                    </a>
                </td>
                <td class="number_column sign leechers m_th_right">
                    <a href="<?= header_link('Leechers') ?>">
                        <i class="fa fa-download tooltip" aria-hidden="true" alt="Leechers" title="<?= Lang::get('torrents', 'leechers') ?>"></i>
                    </a>
                </td>
                <td class="center"><i class="fas fa-seedling tooltip" title="<?= Lang::get('torrents', 'seeding_status') ?>"></i></td>
                <td class="center"><i class="fas fa-clock tooltip" title="<?= Lang::get('torrents', 'seeding_time') ?>"></i></td>
                <? if (ENABLE_HNR) { ?>
                    <td class="center"><i class="fas fa-running tooltip" title="<?= Lang::get('torrents', 'hit_and_run') ?>"></i></td>
                <? } ?>
            </tr>
            <?
            $PageSize = 0;
            foreach ($TorrentsInfo as $TorrentID => $Info) {
                list($GroupID,, $Time, $CategoryID, $Seeders, $Leechers, $SeedTime, $RealDownloaded, $RealUploaded, $Snatched, $Active, $Remaining, $Eliminate) = array_values($Info);

                $Seeding = $Remaining == 0 && $Active;

                extract(Torrents::array_group($Results[$GroupID]));
                $Torrent = $Torrents[$TorrentID];
                $Torrent['Name'] = $GroupName;
                $Torrent['SubName'] = $GroupSubName;
                $Torrent['Year'] = $GroupYear;
                $Size = $Torrent['Size'];
                $TorrentName = Torrents::torrent_group_name($Torrent, false, true, false);
                $RealUploaded = $RealUploaded ? $RealUploaded : 0;
                $RealDownloaded = $RealDownloaded ? $RealDownloaded : 0;
                $HNR = false;
                if ($RealDownloaded > $Size * HNR_MIN_SIZE_PERCENT  &&  time() - intval($Time) > HNR_INTERVAL && ($SeedTime < HNR_MIN_SEEEDING_TIME || $RealUploaded == 0 || $RealDownloaded / $RealUploaded < HNR_MIN_MIN_RATIO)) {
                    $HNR = true;
                }
                // 被消除
                if ($Eliminate && $HNR = true) {
                    $HNR = false;
                }

            ?>
                <tr>
                    <td class="td_torrent">
                        <?= $TorrentName ?>
                    </td>
                    <td class="td_size">
                        <?= Format::get_size($Torrent['Size']) ?>
                    </td>
                    <td class="td_uploaded m_td_right number_column">
                        <?= $RealUploaded ? Format::get_size($RealUploaded) : '--' ?>
                    </td>
                    <td class="td_downloaded m_td_right number_column">
                        <?= $RealDownloaded ? Format::get_size($RealDownloaded) : '--' ?>
                    </td>
                    <td class="td_ratio m_td_right number_column">
                        <?= (!$RealDownloaded && !$RealUploaded) ? '--' : round($RealUploaded / $RealDownloaded, 2) ?>
                    </td>
                    <td class="td_seeders m_td_right number_column<?= (($Torrent['Seeders'] == 0) ? ' r00' : '') ?>"><?= number_format($Torrent['Seeders']) ?></td>
                    <td class="td_leechers m_td_right number_column"><?= number_format($Torrent['Leechers']) ?></td>
                    <td class="td_seeding_status center">
                        <span class="important_text_alt"><?= $Seeding ? Lang::get('torrents', 'yes') : Lang::get('torrents', 'no') ?></span>
                    </td>
                    <td class="td_seeding_time center">
                        <?= Time::convertMinutes($SeedTime / 60) ?>
                    </td>
                    <? if (ENABLE_HNR) { ?>
                        <td class="td_hnr center">
                            <span class="important_text"><?= $HNR ? Lang::get('torrents', 'yes') : Lang::get('torrents', 'no') ?></span>
                        </td>
                    <? } ?>
                </tr>
            <?        } ?>
        </table>
    <?    } ?>
    <div class="linkbox"><?= $Pages ?></div>
</div>
<? View::show_footer(); ?>