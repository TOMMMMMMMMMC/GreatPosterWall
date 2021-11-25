<?

class TorrentTableScene {
    const Notify = 1;
    const Bookmarks = 2;
    const Top10 = 3;
    const Main = 4;
    const Upload = 5;
}
function print_torrent_table_header($Scene, $Params = []) {
    $CheckAllTorrents = $Params['CheckAllTorrents'];
    $PageUncheckedCnt  = $Params['PageUncheckedCnt'];
    $AllUncheckedCnt = $Params['AllUncheckedCnt'];
    $CntColor = $Params['CntColor'];
    $FilterID = $Params['FilterID'];
    $GroupResults = $Params['GroupResults'];
    $Sort = true;
    if ($Scene == TorrentTableScene::Bookmarks || $Scene == TorrentTableScene::Top10) {
        $Sort = false;
    }
?>
    <tr class="colhead">

        <?
        if ($Scene == TorrentTableScene::Notify) {
        ?>
            <td style="text-align: center;"><input type="checkbox" name="toggle" onclick="toggleChecks('notificationform_<?= $FilterID ?>', this, '.notify_box')" /></td>
        <?
        }
        ?>
        <td class="small">
            <?
            if ($CheckAllTorrents) {
            ?>
                <a href="javascript:void(-1)" class="fas fa-circle" id="torrents_all"></a>
                <a href="javascript:void(-1)" class="far fa-circle" id="torrents_unchecked" style="display:none"></a>
                <a href="javascript:void(-1)" class="far fa-check-circle" id="torrents_checked" style="display:none"></a>
        </td>
    <?
            }

            if ($GroupResults) {
    ?>
        <td class="td_info"></td>
    <?
            }
    ?>
    </td>
    <td class="m_th_left m_th_left_collapsable" width="100%"><?= Lang::get('torrents', 'name') ?>&nbsp;/&nbsp;<?= header_elem(Lang::get('torrents', 'year'), $Sort, 'year') ?>
        <?
        if ($CheckAllTorrents) {
        ?><span id="unchecked_statistic"><?= Lang::get('torrents', 'unchecked_torrents') ?>:&nbsp;<?= $PageUncheckedCnt ?>&nbsp;/&nbsp;<span style="color: <?= $CntColor ?>;font-weight: bold;"><?= $AllUncheckedCnt ?></span></span>
        <? } ?></td>
        <td>
            <a class="tooltip" title="<?=Lang::get('torrents', 'filelist')?>">
                <?=ICONS['torrent-files']?>
            </a>
        </td>
        <td>
            <?= header_elem('<span class="tooltip" aria-hidden="true" title="' . Lang::get('torrents', 'time') . '">' . ICONS['torrent-time'] . '</span>', $Sort, 'time') ?>
        </td>
        <td class="number_column">
            <?= header_elem('<span class=" tooltip" aria-hidden="true" title="' . Lang::get('global', 'size') . '">' . ICONS['torrent-size'] . '</i>', $Sort, 'size') ?>
        </td>
        <td class="number_column sign snatches">
            <?= header_elem('<i class="tooltip" aria-hidden="true" title="' . Lang::get('global', 'snatched') . '">' . ICONS['torrent-snatches'] . '</i>', $Sort, 'snatched') ?>
        </td>
        <td class="number_column sign seeders">
            <?= header_elem('<i class="tooltip" aria-hidden="true" title="' . Lang::get('global', 'seeders') . '">' . ICONS['torrent-seeders'] . '</i>', $Sort, 'sseeders') ?>
        </td>
        <td class="number_column sign leechers">
            <?= header_elem('<i class="tooltip" aria-hidden="true" title="' . Lang::get('global', 'leechers') . '">' . ICONS['torrent-leechers'] . '</i>', $Sort, 'leechers') ?>
        </td>
    </tr>
    <?
}

function print_all_group($GroupIDs, $TorrentList, $CollageDataList) {
    foreach ($GroupIDs as $Idx => $GroupID) {
        $Group = $TorrentList[$GroupID];
        extract(Torrents::array_group($Group));
        /**
         * @var int $GroupID
         * @var string $GroupName
         * @var string $GroupYear
         * @var int $GroupCategoryID
         * @var string $GroupRecordLabel
         * @var bool   $GroupVanityHouse
         * @var array  $GroupFlags
         * @var array  $Artists
         * @var array  $ExtendedArtists
         * @var string $TagList
         * @var string $WikiImage
         */

        list(, $Sort, $AddedTime) = array_values($CollageDataList[$GroupID]);

        if ($Artists) {
            foreach ($Artists as $Artist) {
                if (!isset($ArtistCount[$Artist['id']])) {
                    $ArtistCount[$Artist['id']] = array('artist' => $Artist, 'count' => 1);
                } else {
                    $ArtistCount[$Artist['id']]['count']++;
                }
            }
        }

        $TorrentTags = new Tags($TagList, false);
        $Director = Artists::get_first_directors($ExtendedArtists);
        $SnatchedGroupClass = $GroupFlags['IsSnatched'] ? ' snatched_group' : '';

        // Start an output buffer, so we can store this output in $TorrentTable
        ob_start();
        // Grouped torrents
        $ShowGroups = !(!empty($LoggedUser['TorrentGrouping']) && $LoggedUser['TorrentGrouping'] === 1);
    ?>
        <tr class="group discog<?= $SnatchedGroupClass ?>" id="group_<?= $GroupID ?>">
            <td class="td_collapse m_td_left center">
                <div id="showimg_<?= $GroupID ?>" class="<?= ($ShowGroups ? 'hide' : 'show') ?>_torrents">
                    <a href="#" class="tooltip show_torrents_link" onclick="toggle_group(<?= $GroupID ?>, this, event);" title="<?= Lang::get('global', 'collapse_this_group_title') ?>"></a>
                </div>
            </td>
            <td class="td_info" colspan="5">
                <div class="movie-info">
                <div class="movie-info-content group_info clear">
                    <? pinrt_group_name($Group, false); ?>
                    <? print_group_movie_info($Group, $Director) ?>
                    <span class="movie-info-created-at float_right">
                        <? if (!$Sneaky) { ?>

                            <br />
                        <? } ?>
                        <?= time_diff($AddedTime); ?>
                    </span>
                    <div class="movie-info-tags tags"><?= $TorrentTags->format() ?></div>
                </div>
                </div>
            </td>
        </tr>
        <?
        $LastRemasterTitle = '';
        $LastRemasterCustomTitle = '';
        $LastResolution = '';
        $LastNotMain = '';

        $EditionID = 0;
        unset($FirstUnknown);

        foreach ($Torrents as $TorrentID => $Torrent) {

            if ($Torrent['Remastered'] && !$Torrent['RemasterYear']) {
                $FirstUnknown = !isset($FirstUnknown);
            }
            $SnatchedTorrentClass = $Torrent['IsSnatched'] ? ' snatched_torrent' : '';

            if ($GroupCategoryID == 1) {
                $NewEdition = Torrents::get_new_edition_title($LastResolution, $LastRemasterTitle, $LastRemasterCustomTitle, $LastNotMain, $Torrent['Resolution'], $Torrent['RemasterTitle'], $Torrent['RemasterCustomTitle'], $Torrent['NotMainMovie']);
                if ($NewEdition) {
                    $EditionID++;
        ?>
                    <tr class="group_torrent groupid_<?= $GroupID ?> edition<?= (!empty($LoggedUser['TorrentGrouping']) && $LoggedUser['TorrentGrouping'] === 1 ? ' hidden' : '') ?>">
                        <td colspan="7" class="edition_info"><strong><a href="#" onclick="torrentTable.toggleEdition(event, <?= $GroupID ?>, <?= $EditionID ?>)" class="tooltip" title="<?= Lang::get('global', 'collapse_this_edition_title') ?>">&minus;</a>
                                <?= $NewEdition ?>
                            </strong></td>
                    </tr>
    <?
                }
            }

            $LastRemasterTitle = $Torrent['RemasterTitle'];;
            $LastRemasterCustomTitle = $Torrent['RemasterCustomTitle'];
            $LastResolution = $Torrent['Resolution'];
            $LastNotMain = $Torrent['NotMainMovie'];
            print_torrent_info($Torrent, $GroupID, $EditionID, $SnatchedTorrentClass, $SnatchedGroupClass);
        }
        echo ob_get_clean();
    }
}

function print_torrent_info($Torrent, $GroupID, $EditionID, $SnatchedTorrentClass, $SnatchedGroupClass) {
    $TorrentID = $Torrent['ID'];
    global $LoggedUser;
    ?>
    <tr class="group_torrent torrent_row groupid_<?= $GroupID ?> edition_<?= $EditionID ?><?= $SnatchedTorrentClass . $SnatchedGroupClass . (!empty($LoggedUser['TorrentGrouping']) && $LoggedUser['TorrentGrouping'] === 1 ? ' hidden' : '') ?>">
        <td class="td_info" colspan="2">
            <span>[ <a href="torrents.php?action=download&amp;id=<?= $TorrentID ?>&amp;authkey=<?= $LoggedUser['AuthKey'] ?>&amp;torrent_pass=<?= $LoggedUser['torrent_pass'] ?>" class="tooltip" title="Download">DL</a>
                <? if (Torrents::can_use_token($Torrent)) { ?>
                    |
                    <a href="torrents.php?action=download&amp;id=<?= $TorrentID ?>&amp;authkey=<?= $LoggedUser['AuthKey'] ?>&amp;torrent_pass=<?= $LoggedUser['torrent_pass'] ?>&amp;usetoken=1" class="tooltip" title="Use a FL Token" onclick="return confirm('<?= FL_confirmation_msg($Torrent['Seeders'], $Torrent['Size']) ?>');">FL</a>
                <? } ?>
                | <a href="reportsv2.php?action=report&amp;id=<?= $TorrentID ?>" class="tooltip" title="Report">RP</a> ]
            </span>
            &nbsp;&nbsp;&raquo;&nbsp; <a href="torrents.php?id=<?= $GroupID ?>&amp;torrentid=<?= $TorrentID ?>"><?= Torrents::torrent_info($Torrent, true, true) ?></a>
        </td>
        <td class="td_file_count"><?= $Torrent['FileCount'] ?></td>
        <td class="td_time nobr"><?= time_diff($Torrent['Time'], 1) ?></td>
        <td class="td_size number_column nobr"><?= Format::get_size($Torrent['Size']) ?></td>
        <td class="td_snatched m_td_right number_column"><?= number_format($Torrent['Snatched']) ?></td>
        <td class="td_seeders m_td_right number_column<?= (($Torrent['Seeders'] == 0) ? ' r00' : '') ?>">
            <?= number_format($Torrent['Seeders']) ?></td>
        <td class="td_leechers m_td_right number_column"><?= number_format($Torrent['Leechers']) ?></td>
    </tr>
<?
}

function header_elem($Name, $Sort, $SortKey = "") {
    if ($Sort) {
        return "<a href='" . header_link($SortKey) . "'> " . $Name . "</a>";
    }
    return "<i>" . $Name . "</i>";
}


function print_group_movie_info($GroupInfo, $Director) {
?>
    <div class="movie-info-facts movie_info_crumbs">
        <a class="movie-info-facts-item info_crumbs tooltip" title="<?=Lang::get('global', 'imdb_rating')?>, <?=$GroupInfo['IMDBVote'].' '.Lang::get('torrents', 'movie_votes')?>" target="_blank" href="https://www.imdb.com/title/<?=$GroupInfo['IMDBID']?>">
            <?=ICONS['imdb-gray']?>
            <span><?=!empty($GroupInfo['IMDBRating'])?sprintf("%.1f",$GroupInfo['IMDBRating']):'--'?></span>
        </a>
        <a class="movie-info-facts-item info_crumbs tooltip" title="<?=Lang::get('global', 'douban_rating')?>, <?=($GroupInfo['DoubanVote']?$GroupInfo['DoubanVote']:'?') .' '.Lang::get('torrents', 'movie_votes')?>" target="_blank" href="https://movie.douban.com/subject/<?=$GroupInfo['DoubanID']?>/">
            <?=ICONS['douban-gray']?>
            <span><?=!empty($GroupInfo['DoubanRating'])?sprintf("%.1f",$GroupInfo['DoubanRating']):'--'?></span>
        </a>
        <a class="movie-info-facts-item info_crumbs tooltip" title="<?=Lang::get('global', 'rt_rating')?>" target="_blank" href="https://www.rottentomatoes.com/<?=$GroupInfo['RTTitle']?>">
            <?=ICONS['rotten-tomatoes-gray']?>
            <span><?=!empty($GroupInfo['RTRating'])?$GroupInfo['RTRating']:'--'?></span>
        </a>
        <span class="movie-info-facts-item info_crumbs tooltip" title="<?=Lang::get('upload', 'director')?>">
            <?=ICONS['movie-director']?>
            <span><?=Artists::display_artist($Director)?></span>
        </span>
        <span class="movie-info-facts-item info_crumbs tooltip" title="<?=Lang::get('torrents', 'imdb_region')?>">
            <?=ICONS['movie-country']?>
            <span><?print_r(implode(', ',array_slice(explode(',',$GroupInfo['Region']), 0, 2)))?></span>
        </span>
        <span class="movie-info-facts-item info_crumbs tooltip" title="<?=Lang::get('upload', 'movie_type')?>">
            <?=ICONS['movie-type']?>
            <span><?=Lang::get('torrents', 'release_types')[$GroupInfo['ReleaseType']]?></span>
        </span>
    </div>
<?
}

function pinrt_group_name($GroupInfo, $ShowVote) {
    $GroupID = $GroupInfo['ID'];
    $SubName = $GroupInfo['SubName'];
    $GroupName = $GroupInfo['Name'];
    $GroupYear = $GroupInfo['Year'];
?>
    <span class="movie-info-title group_movie_title">
        <a class="group_movie_title_a" href="\torrents.php?id=<?= $GroupID ?>"><?= $GroupName ?></a>&nbsp;
        <small class="movie-info-year"><i>(<? print_r($GroupYear) ?>)</i></small>
    </span>
    <? if (Bookmarks::has_bookmarked('torrent', $GroupID)) { ?>
        <span class="movie-info-action remove_bookmark float_right">
            <a href="#" id="bookmarklink_torrent_<?= $GroupID ?>" onclick="Unbookmark('torrent', <?= $GroupID ?>); return false;">
                <svg class="remove-icon bookmark-active icon" width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="m19 21-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16z" fill="currentColor" fill-rule="evenodd" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                </svg>
                <svg class="add-icon bookmark icon" width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="m19 21-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16z" fill="none" fill-rule="evenodd" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                </svg>
            </a>
        </span>
    <?  } else { ?>
        <span class="movie-info-action add_bookmark float_right">
            <a href="#" id="bookmarklink_torrent_<?= $GroupID ?>" onclick="Bookmark('torrent', <?= $GroupID ?>); return false;">
                <svg class="remove-icon bookmark-active icon" width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="m19 21-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16z" fill="currentColor" fill-rule="evenodd" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                </svg>
                <svg class="add-icon bookmark icon" width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="m19 21-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16z" fill="none" fill-rule="evenodd" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                </svg>
            </a>
        </span>
    <?  }
    if ($ShowVote && ENABLE_VOTES) {
        $VoteType = isset($UserVotes[$GroupID]['Type']) ? $UserVotes[$GroupID]['Type'] : '';
        Votes::vote_link($GroupID, $VoteType);
    }
    ?>

    <div class="movie-info-subtitle movie_cn_title">
        <? if ($SubName) {
            echo " <a href=\"torrents.php?searchstr=" . $SubName . "\">$SubName</a>";
        } ?>
    </div>
<?
}


function print_group_info($GroupInfo, $TorrentTags, $Action, $ShowVote) {
    $GroupID = $GroupInfo['ID'];
    $CategoryID = $GroupInfo['CategoryID'];
    $ExtendedArtists = $GroupInfo['ExtendedArtists'];
    $Director = Artists::get_first_directors($ExtendedArtists);
    $ShowGroups = !(!empty(G::$LoggedUser['TorrentGrouping']) && G::$LoggedUser['TorrentGrouping'] == 1);
?>
    <td class="td_collapse center m_td_left">
        <div id="showimg_<?= $GroupID ?>" class="<?= ($ShowGroups ? 'hide' : 'show') ?>_torrents">
            <a href="#" class="tooltip show_torrents_link" onclick="toggle_group(<?= $GroupID ?>, this, event)" title="<?= Lang::get('global', 'collapse_this_group_title') ?>"></a>
        </div>
    </td>
    <td class="td_info big_info">
        <? if (G::$LoggedUser['CoverArt']) { ?>
            <div class="group_image float_left clear">
                <? ImageTools::cover_thumb($GroupInfo['WikiImage'], $GroupInfo['CategoryID']) ?>
            </div>
        <?  } ?>
    </td>
    <td colspan="7">
        <div class="movie-info">
        <div class="movie-info-content group_info clear">
            <?= pinrt_group_name($GroupInfo, $ShowVote); ?>
            <?= print_group_movie_info($GroupInfo, $Director) ?>
            <div class="movie-info-tags torrent_tags"><?= $TorrentTags->format('torrents.php?' . $Action . '&amp;taglist=') ?></div>
        </div>
        </div>
    </td>
<?
}

function print_ungroup_info($Torrent, $GroupInfo, $CategoryID, $TorrentTags, $Director, $Action = "action=basic", $Number = null, $FilterID = 0) {
    global $LoggedUser;
    $SnatchedGroupClass = $GroupInfo['Flags']['IsSnatched'] ? ' snatched_group' : '';
    if ($GroupInfo['GroupID']) {
        $GroupID = $GroupInfo['GroupID'];
    } else {
        $GroupID = $GroupInfo['ID'];
    }
    if ($Torrent['TorrentID']) {
        $TorrentID = $Torrent['TorrentID'];
    } else {
        $TorrentID = $Torrent['ID'];
    }
    $Torrent['ID'] = $TorrentID;
    $Torrent['TorrentID'] = $TorrentID;
    $GroupInfo['GroupID'] = $GroupID;
    $GroupInfo['ID'] = $GroupID;
    $TorrentID = $Torrent['TorrentID'];
    $Torrent['ID'] = $TorrentID;
    $ExtraInfo = Torrents::torrent_info($Torrent, true, true);
    $SnatchedTorrentClass = $Torrent['IsSnatched'] ? ' snatched_torrent' : '';

?>
    <tr class="torrent<?= $SnatchedTorrentClass . $SnatchedGroupClass ?>">
        <?
        if ($Number) {
        ?>
            <td class="td_info" rowspan="2" style="padding: 8px; text-align: center;" class="td_rank m_td_left"><strong><?= $Number ?></strong></td>
        <?

        }
        if ($FilterID) {
        ?>
            <td class="m_td_left td_checkbox" rowspan="2" style="text-align: center;">
                <input type="checkbox" class="notify_box notify_box_<?= $FilterID ?>" value="<?= $TorrentID ?>" id="clear_<?= $TorrentID ?>" tabindex="1" />
            </td>
        <?
        }
        ?>
        <td class="td_info big_info" rowspan="2">
            <? if ($LoggedUser['CoverArt']) { ?>
                <div class="group_image float_left clear">
                    <?= ImageTools::cover_thumb($GroupInfo['WikiImage'], $CategoryID) ?>
                </div>
            <?      } ?>


        <td colspan="7">
            <div class="movie-info">
            <div class="movie-info-content group_info clear">
                <?= pinrt_group_name($GroupInfo, true); ?>
                <?= print_group_movie_info($GroupInfo, $Director) ?>
                <div class="tags"><?= $TorrentTags->format("torrents.php?$Action&amp;taglist=") ?></div>
            </div>
            </div>
        </td>
        </td>
    </tr>
    <tr>
        <td class="td_info" width="100%">
            <span>
                [ <a href="torrents.php?action=download&amp;id=<?= $TorrentID ?>&amp;authkey=<?= $LoggedUser['AuthKey'] ?>&amp;torrent_pass=<?= $LoggedUser['torrent_pass'] ?>" class="tooltip" title="<?= Lang::get('global', 'download') ?>">DL</a>
                <? if (Torrents::can_use_token($Torrent)) { ?>
                    | <a href="torrents.php?action=download&amp;id=<?= $TorrentID ?>&amp;authkey=<?= $LoggedUser['AuthKey'] ?>&amp;torrent_pass=<?= $LoggedUser['torrent_pass'] ?>&amp;usetoken=1" class="tooltip" title="<?= Lang::get('global', 'use_fl_tokens') ?>" onclick="return confirm('<?= FL_confirmation_msg($Torrent['Seeders'], $Torrent['Size']) ?>');">FL</a>
                <?      } ?>
                | <a href="reportsv2.php?action=report&amp;id=<?= $TorrentID ?>" class="tooltip" title="<?= Lang::get('torrents', 'report') ?>">RP</a> ]
            </span>
            <a class="torrent_specs" href="torrents.php?id=<?= $GroupID ?>&amp;torrentid=<?= $TorrentID ?>">
                <?= $ExtraInfo ?>
            </a>
        </td>

        <td class="td_file_count"><?= $Torrent['FileCount'] ?></td>
        <td class="td_time nobr"><?= time_diff($Torrent['Time'], 1) ?></td>
        <td class="td_size number_column nobr"><?= Format::get_size($Torrent['Size']) ?></td>
        <td class="td_snatched m_td_right number_column"><?= number_format($Torrent['Snatched']) ?></td>
        <td class="td_seeders m_td_right number_column<?= ($Torrent['Seeders'] == 0) ? ' r00' : '' ?>"><?= number_format($Torrent['Seeders']) ?></td>
        <td class="td_leechers m_td_right number_column"><?= number_format($Torrent['Leechers']) ?></td>
        </td>
    </tr>
    <?

}

function print_group($LoggedUser, $GroupID, $GroupName, $GroupCategoryID, $ReleaseType, $TorrentList, $Types, $ShowSlot = true) {
    if (check_perms('torrents_check')) {
        $CheckAllTorrents = !$LoggedUser['DisableCheckAll'];
    } else {
        $CheckAllTorrents = false;
    }
    if (check_perms('self_torrents_check')) {
        $CheckSelfTorrents = !$LoggedUser['DisableCheckSelf'];
    } else {
        $CheckSelfTorrents = false;
    }
    $EditionID = 0;

    $LastRemasterTitle = '';
    $LastRemasterCustomTitle = '';
    $LastResolution = '';
    $LastNotMain = '';
    G::$DB->query("
    select tt.id, 
        count(tb.fromuserid) count, 
        (
            select count(1) 
                from thumb where
                itemid = tt.id
                and fromuserid=" . $LoggedUser['ID'] . "
                and type = 'torrent'
        ) 'on'
    from torrents as tt
    left join thumb as tb 
        on tt.id = tb.itemid 
        and tb.type = 'torrent' 
    WHERE tt.groupid = $GroupID
    group by tt.id");
    $ThumbCounts = G::$DB->to_array('id');
    G::$DB->query("
    SELECT `ID`,
         sum(bonus) Count,
         (select group_concat(bonus)
            from torrents_send_bonus where torrentid=t.id
            and FromUserID = " . $LoggedUser['ID'] . "
         ) Sended
    FROM `torrents` as t
    left join torrents_send_bonus as tsb 
        on t.id = tsb.torrentid
    where t.groupid = $GroupID
    group by id");
    $BonusSended = G::$DB->to_array('ID');
    foreach ($TorrentList as $Torrent) {
        list(
            $TorrentID, $Media, $Format, $Encoding, $Remastered, $RemasterYear,
            $RemasterTitle,  $RemasterCustomTitle, $RemasterRecordLabel, $RemasterCatalogueNumber, $Scene, $Jinzhuan, $Diy, $Buy, $Allow,
            $HasLog, $HasCue, $HasLogDB, $LogScore, $LogChecksum, $FileCount, $Size, $Seeders, $Leechers,
            $Snatched, $FreeTorrent, $TorrentTime, $NotMainMovie, $Source, $Codec,
            $Container, $Resolution, $Processing, $ChineseDubbed, $SpecialSub, $Subtitles, $ExternalSubtitles, $ExternalSubtitleIDs, $Makers, $Description, $MediaInfos, $Note, $SubtitleType, $FileList,
            $FilePath, $UserID, $LastActive, $InfoHash, $BadTags, $BadFolders, $BadImg, $BadFiles, $BadCompress, $NoSub, $HardSub,
            $MissingLineage, $CustomTrumpable, $CassetteApproved, $LossymasterApproved, $LossywebApproved,
            $LastReseedRequest, $HasFile, $LogCount, $FreeEndTime, $Slot
        ) = array_values($Torrent);


        if ($Remastered && !$RemasterYear) {
            $FirstUnknown = !isset($FirstUnknown);
        }

        $Reported = false;
        unset($ReportedTimes);

        $Reports = Torrents::get_reports($TorrentID);
        $NumReports = count($Reports);

        if ($NumReports > 0) {
            $Reported = true;
            //include(SERVER_ROOT.'/sections/reportsv2/array.php');
            include(Lang::getLangfilePath("report_types"));
            $ReportInfo = '
                <div class="table_container border torrentdetails">
            <table class="reportinfo_table">
                <tr class="colhead_dark" style="font-weight: bold;">
                    <td>' . Lang::get('torrents', 'this_torrent_has_active_reports_1') . $NumReports . Lang::get('torrents', 'this_torrent_has_active_reports_2') . ($NumReports === 1 ? Lang::get('torrents', 'this_torrent_has_active_reports_3') : Lang::get('torrents', 'this_torrent_has_active_reports_4')) . ":</td>
                </tr>";
            // <a class='float_right report_reply_btn' href='#'>".Lang::get('torrents', 'reply')."</a> 这个是回复按钮，等开发做完整个报告回复功能后再填进去
            foreach ($Reports as $Report) {
                $ReportID = $Report['ID'];
                if (check_perms('admin_reports')) {
                    $ReporterID = $Report['ReporterID'];
                    $Reporter = Users::user_info($ReporterID);
                    $ReporterName = $Reporter['Username'];
                    $ReportLinks = "<a href=\"user.php?id=$ReporterID\">$ReporterName</a> <a href=\"reportsv2.php?view=report&amp;id=$Report[ID]\">" . Lang::get('torrents', 'reported_it') . "</a>";
                    $UploaderLinks = Users::format_username($UserID, false, false, false) . " " . Lang::get('torrents', 'reply_at');
                } else {
                    $ReportLinks = Lang::get('torrents', 'someone_reported_it');
                    $UploaderLinks = Lang::get('torrents', 'uploader_replied_it');
                }

                if (isset($Types[$GroupCategoryID][$Report['Type']])) {
                    $ReportType = $Types[$GroupCategoryID][$Report['Type']];
                } elseif (isset($Types['master'][$Report['Type']])) {
                    $ReportType = $Types['master'][$Report['Type']];
                } else {
                    //There was a type but it wasn't an option!
                    $ReportType = $Types['master']['other'];
                }
                $CanReply = $UserID == $LoggedUser['ID'] && !$Report['UploaderReply'];
                $ReportInfo .= "
                <tr>
                    <td>$ReportLinks" . Lang::get('torrents', 'at') . " " . time_diff($Report['ReportedTime'], 2, true, true) . Lang::get('torrents', 'for_the_reason') . $ReportType['title'] . '":' . ($CanReply ? ('<a class="float_right report_reply_btn" onclick="$(\'.can_reply_' . $ReportID . '\').toggle()" href="javascript:void(0)">' . Lang::get('torrents', 'reply') . '</a>') : "") . '
                        <blockquote>' . Text::full_format($Report['UserComment']) . ($Report['UploaderReply'] ? ('<hr class="report_inside_line">' . $UploaderLinks . ' ' . time_diff($Report['ReplyTime'], 2, true, true) . ':<br>' . Text::full_format($Report['UploaderReply'])) : '') . '</blockquote>
                    </td>
                </tr>';
                $area = new TEXTAREA_PREVIEW('uploader_reply', '', '', 50, 10, false, false, true, array(
                    'placeholder="' . Lang::get('torrents', 'reply_it_patiently') . '"'
                ), false);
                $ReportInfo .= $CanReply ? '
                <tr class="report_reply_tr can_reply_' . $ReportID . '" style="display: none;">
                    <td class="report_reply_td" align="center">
                        <form action="reportsv2.php?action=takeuploaderreply" method="POST">
                            <input type="hidden" name="reportid" value="' . $ReportID . '">
                            <input type="hidden" name="torrentid" value="' . $TorrentID . '">
                            ' . $area->getBuffer() . '
                            <div class="box vertical_space body hidden preview_wrap" id="preview_wrap_' . $area->getID() . '">
                                <div id="preview_' . $area->getID() . '"></div>
                            </div>
                            <div class="submit_div preview_submit">
                                <input type="button" value="Preview" class="hidden button_preview_' . $area->getID() . '" />
                                <input type="submit">
                            </div>
                        </form>
                    </td>
                </tr>' : "";
            }
            $ReportInfo .= "\n\t\t</table></div>";
        }

        $CanEdit = (check_perms('torrents_edit') || (($UserID == $LoggedUser['ID'] && !$LoggedUser['DisableWiki'])));

        $RegenLink = check_perms('users_mod') ? ' <a href="torrents.php?action=regen_filelist&amp;torrentid=' . $TorrentID . '" class="brackets">' . Lang::get('torrents', 'regenerate') . '</a>' : '';
        $FileTable = '
        <div class="table_container border torrentdetails">
        <table class="filelist_table">
            <tr class="colhead_dark">
                <td>
                    <div class="filelist_title" style="float: left;">' . Lang::get('torrents', 'file_names') . $RegenLink . '</div>
                    <div class="filelist_path" style="float: right;">' . ($FilePath ? "/$FilePath/" : '/') . '</div>
                </td>
                <td class="nobr">
                    <strong>' . Lang::get('torrents', 'size') . '</strong>
                </td>
            </tr>';
        if (substr($FileList, -3) == '}}}') { // Old style
            $FileListSplit = explode('|||', $FileList);
            foreach ($FileListSplit as $File) {
                $NameEnd = strrpos($File, '{{{');
                $Name = substr($File, 0, $NameEnd);
                if ($Spaces = strspn($Name, ' ')) {
                    $Name = str_replace(' ', '&nbsp;', substr($Name, 0, $Spaces)) . substr($Name, $Spaces);
                }
                $FileSize = substr($File, $NameEnd + 3, -3);
                $FileTable .= sprintf("\n<tr><td>%s</td><td class=\"number_column nobr\">%s</td></tr>", $Name, Format::get_size($FileSize));
            }
        } else {
            $FileListSplit = explode("\n", $FileList);
            foreach ($FileListSplit as $File) {
                $FileInfo = Torrents::filelist_get_file($File);
                $FileTable .= sprintf("\n<tr><td>%s</td><td class=\"number_column nobr\">%s</td></tr>", $FileInfo['name'], Format::get_size($FileInfo['size']));
            }
        }
        $FileTable .= '
        </table></div>';

        $ExtraInfo = Torrents::torrent_info($Torrent, true, true);



        if ($GroupCategoryID == 1) {
            $NewEdition = Torrents::get_new_edition_title($LastResolution, $LastRemasterTitle, $LastRemasterCustomTitle, $LastNotMain, $Resolution, $RemasterTitle, $RemasterCustomTitle, $NotMainMovie);
            if ($NewEdition) {
                $EditionID++;
    ?>
                <tr class="group_torrent groupid_<?= $GroupID ?> edition<?= (!empty($LoggedUser['TorrentGrouping']) && $LoggedUser['TorrentGrouping'] === 1 ? ' hidden' : '') ?>">
                    <td colspan="7" class="edition_info"><strong><a href="#" onclick="torrentTable.toggleEdition(event, <?= $GroupID ?>, <?= $EditionID ?>)" class="tooltip" title="<?= Lang::get('global', 'collapse_this_edition_title') ?>">&minus;</a>
                            <?= $NewEdition ?>
                        </strong></td>
                </tr>
        <?
            }
        }

        $LastRemasterTitle = $RemasterTitle;
        $LastRemasterCustomTitle = $RemasterCustomTitle;
        $LastResolution = $Resolution;
        $LastNotMain = $NotMainMovie;
        $TorrentChecked = G::$Cache->get_value("torrent_checked_$TorrentID");
        $TorrentCheckedBy = 'unknown';
        if ($TorrentChecked && $TorrentChecked != 1) {
            G::$DB->query("select Username from users_main where ID=$TorrentChecked");
            list($TorrentCheckedBy) = G::$DB->next_record();
        }
        ?>

        <tr class="torrent_row releases_<?= $ReleaseType ?> groupid_<?= $GroupID ?> edition_<?= $EditionID ?> group_torrent" style="font-weight: normal;" id="torrent<?= $TorrentID ?>" data-slot="<?= Torrents::slot_name($Slot) ?>" data-source="<?= $Source ?>" data-codec="<?= $Codec ?>" data-container="<?= $Container ?>" data-resolution="<?= $Resolution ?>" data-processing="<?= $Processing ?>">
            <?
            if ($ShowSlot) {
            ?>
                <td class="slot-container"></td>
            <?
            }
            ?>
            <td class="td_info">
                <span>[ <a href="torrents.php?action=download&amp;id=<?= $TorrentID ?>&amp;authkey=<?= $LoggedUser['AuthKey'] ?>&amp;torrent_pass=<?= $LoggedUser['torrent_pass'] ?>" class="tooltip" title="<?= Lang::get('global', 'download') ?>"><?= ($HasFile ? 'DL' : 'Missing') ?></a>
                    <? if (Torrents::can_use_token($Torrent)) { ?>
                        | <a href="torrents.php?action=download&amp;id=<?= $TorrentID ?>&amp;authkey=<?= $LoggedUser['AuthKey'] ?>&amp;torrent_pass=<?= $LoggedUser['torrent_pass'] ?>&amp;usetoken=1" class="tooltip" title="<?= Lang::get('global', 'use_fl_tokens') ?>" onclick="return confirm('<?= FL_confirmation_msg($Torrent['Seeders'], $Torrent['Size']) ?>');">FL</a>
                    <?  } ?>
                    | <a href="reportsv2.php?action=report&amp;id=<?= $TorrentID ?>" class="tooltip" title="<?= Lang::get('torrents', 'report') ?>">RP</a>
                    <? if ($CanEdit) { ?>
                        | <a href="torrents.php?action=edit&amp;id=<?= $TorrentID ?>" class="tooltip" title="<?= Lang::get('global', 'edit') ?>">ED</a>
                    <?  }
                    if (check_perms('torrents_delete') || $UserID == $LoggedUser['ID']) { ?>
                        | <a href="torrents.php?action=delete&amp;torrentid=<?= $TorrentID ?>" class="tooltip" title="<?= Lang::get('torrents', 'remove') ?>">RM</a>
                    <?  } ?>
                    | <a href="torrents.php?torrentid=<?= $TorrentID ?>" class="tooltip" title="<?= Lang::get('torrents', 'permalink') ?>">PL</a>

                    ]</span>
                <?
                $TorrentChecked = G::$Cache->get_value("torrent_checked_$TorrentID");
                if ($TorrentChecked === false) {
                    G::$DB->query("select Checked from torrents where ID=$TorrentID");
                    list($TorrentChecked) = G::$DB->next_record();
                    G::$Cache->cache_value("torrent_checked_$TorrentID", $TorrentChecked);
                }

                if (canCheckTorrent($TorrentID)) {
                    if (!$CheckAllTorrents && $CheckSelfTorrents) {
                        if ($TorrentCheckedBy != $LoggedUser['Username']) {
                            $TorrentCheckedBy = "someone";
                        }
                    }
                ?>
                    <script>
                        $(document).ready(function() {
                            $('#torrent<?= $TorrentID ?>_check0').bind('click', {
                                id: <?= $TorrentID ?>,
                                checked: 1,
                            }, torrent_check)
                            $('#torrent<?= $TorrentID ?>_check1').bind('click', {
                                id: <?= $TorrentID ?>,
                                checked: 0,
                            }, torrent_check)
                        })
                    </script>
                    <a href="javascript:void(0)" class="far fa-check-circle tooltip" id="torrent<?= $TorrentID ?>_check1" style="display:<?= $TorrentChecked ? "inline-block" : "none" ?>;color:#649464;" title="<?= Lang::get('torrents', 'checked_by_before') ?><?= $TorrentChecked ? $TorrentCheckedBy : $LoggedUser['Username'] ?><?= Lang::get('torrents', 'checked_by_after') ?>"></a>
                    <a href="javascript:void(0)" class="far fa-circle tooltip" id="torrent<?= $TorrentID ?>_check0" style="display:<?= $TorrentChecked ? "none" : "inline-block" ?>;color:#CF3434;" title="<?= Lang::get('torrents', 'turn_me_green') ?>"></a>
                <?
                } else {

                ?>
                    <i class="tooltip far fa-<?= $TorrentChecked ? "check-" : "" ?>circle" style="color: <?= $TorrentChecked ? "#74B274" : "#A6A6A6" ?>;" title="<?= $TorrentChecked ? Lang::get('torrents', 'has_been_checked') : Lang::get('torrents', 'has_not_been_checked') ?><?= Lang::get('torrents', 'checked_explanation') ?>"></i>
                <?
                }

                ?>
                &nbsp;
                <a class="specs tooltip-html grouped" href="#" onclick="$('#torrent_torrent_<?= $TorrentID ?>').gtoggle(); return false;">
                    <?= $ExtraInfo; ?>
                    <div class="tooltip-content hidden">
                        <div><?= Lang::get('torrents', Torrents::slot_name($Slot) . '_slot') ?></div>
                    </div>
                </a>
            </td>
            <td class="td_size number_column nobr"><?= Format::get_size($Size) ?></td>
            <td class="td_snatched m_td_right number_column"><?= number_format($Snatched) ?></td>
            <td class="td_seeders m_td_right number_column"><?= number_format($Seeders) ?></td>
            <td class="td_leechers m_td_right number_column"><?= number_format($Leechers) ?></td>
        </tr>
        <?
        print_torrent_detail($GroupID, $Torrent, $ReleaseType, $EditionID, $GroupCategoryID, 'torrent');
    }
}

function print_slot_group($LoggedUser, $GroupID, $GroupName, $GroupCategoryID, $ReleaseType, $TorrentList, $Types) {
    $SlotTorrents = Torrents::convert_slot_torrents($TorrentList);
    $MissingSlots = $SlotTorrents[1];
    $Torrents = $SlotTorrents[0];
    if (check_perms('torrents_check')) {
        $CheckAllTorrents = !$LoggedUser['DisableCheckAll'];
    } else {
        $CheckAllTorrents = false;
    }
    if (check_perms('self_torrents_check')) {
        $CheckSelfTorrents = !$LoggedUser['DisableCheckSelf'];
    } else {
        $CheckSelfTorrents = false;
    }
    $LastRemasterTitle = '';
    $LastRemasterCustomTitle = '';
    $LastResolution = '';
    $LastNotMain = '';
    $EditionID = 0;
    foreach ($Torrents as $Torrent) {
        $RemasterTitle = '';
        $RemasterCustomTitle = '';
        $Resolution = $Torrent['Resolution'];
        $NotMainMovie = '';
        $Missing = isset($Torrent['Missing']);
        $Dupe = isset($Torrent['Dupe']);
        if (!$Missing) {
            $TorrentID = $Torrent['ID'];
            $Size = $Torrent['Size'];
            $UserID = $Torrent['UserID'];
            $HasFile = $Torrent['HasFile'];
            $CanEdit = (check_perms('torrents_edit') || (($UserID == $LoggedUser['ID'] && !$LoggedUser['DisableWiki'])));
            $IsExtraSlot = $Torrent['IsExtraSlot'];
            $TorrentInfo = Torrents::torrent_info($Torrent, true, true);
        }
        $Slot = $Torrent['Slot'];
        if ($GroupCategoryID == 1) {
            $NewEdition = Torrents::get_new_edition_title($LastResolution, $LastRemasterTitle, $LastRemasterCustomTitle, $LastNotMain, $Resolution, $RemasterTitle, $RemasterCustomTitle, $NotMainMovie);
            if ($NewEdition) {
                $EditionID++;
        ?>
                <tr class="group_torrent groupid_<?= $GroupID ?> edition<?= (!empty($LoggedUser['TorrentGrouping']) && $LoggedUser['TorrentGrouping'] === 1 ? ' hidden' : '') ?>">
                    <td colspan="7" class="edition_info"><strong><a href="#" onclick="torrentTable.toggleEdition(event, <?= $GroupID ?>, <?= $EditionID ?>)" class="tooltip" title="<?= Lang::get('global', 'collapse_this_edition_title') ?>">&minus;</a>
                            <?= $NewEdition ?>
                        </strong></td>
                </tr>
                <?
            }
            if (Torrents::get_slot_resolution($Resolution) != Torrents::get_slot_resolution($LastResolution)) {
                $MissSlots = $MissingSlots[Torrents::get_slot_resolution($Resolution)];
                $MissSlotNames = [];
                foreach ($MissSlots as $MissingSlot) {
                    if ($MissingSlot == TorrentSlotType::None) {
                        continue;
                    }
                    $SlotTooltip = Torrents::empty_slot_tooltip($MissingSlot);
                    $MissSlotNames[] = "<span class='tooltip' title='$SlotTooltip'><i>" . Lang::get('torrents', Torrents::slot_option_lang($MissingSlot)) . "</i></span>";
                }
                if (count($MissSlotNames) > 0) {
                ?>
                    <tr class="torrent_row releases_<?= $ReleaseType ?> groupid_<?= $GroupID ?> edition_<?= $EditionID ?>">
                        <td class="empty_slot_container" colspan="4"><?= Lang::get('torrents', Torrents::empty_slot_title(Torrents::get_slot_resolution($Resolution))) ?><?= implode(' / ', $MissSlotNames) ?></td>
                    </tr>
                <?
                }
            }
            if (!$Missing) {
                ?>

                <tr class="torrent_row releases_<?= $ReleaseType ?> groupid_<?= $GroupID ?> edition_<?= $EditionID ?>" <?= !$Missing ? "id='torrent$TorrentID'" : '' ?> data-slot="<?= Torrents::slot_name($Slot) ?>">

                    <td class="slot-container tooltip" title="<?= Lang::get('torrents', Torrents::slot_name($Slot) . '_slot') ?>"></td>
                    <td class="td_info">
                        <?
                        $TorrentChecked = G::$Cache->get_value("torrent_checked_$TorrentID");
                        if ($TorrentChecked === false) {
                            G::$DB->query("select Checked from torrents where ID=$TorrentID");
                            list($TorrentChecked) = G::$DB->next_record();
                            G::$Cache->cache_value("torrent_checked_$TorrentID", $TorrentChecked);
                        }
                        $TorrentCheckedBy = 'unknown';
                        if ($TorrentChecked && $TorrentChecked != 1) {
                            G::$DB->query("select Username from users_main where ID=$TorrentChecked");
                            list($TorrentCheckedBy) = G::$DB->next_record();
                        }

                        if (canCheckTorrent($TorrentID)) {
                            if (!$CheckAllTorrents && $CheckSelfTorrents) {
                                if ($TorrentCheckedBy != $LoggedUser['Username']) {
                                    $TorrentCheckedBy = "someone";
                                }
                            }
                        ?>
                            <script>
                                $(document).ready(function() {
                                    $('#slot-torrent<?= $TorrentID ?>_check0').bind('click', {
                                        id: <?= $TorrentID ?>,
                                        checked: 1,
                                    }, torrent_check)
                                    $('#slot-torrent<?= $TorrentID ?>_check1').bind('click', {
                                        id: <?= $TorrentID ?>,
                                        checked: 0,
                                    }, torrent_check)
                                })
                            </script>
                            <a href="javascript:void(0)" class="far fa-check-circle tooltip" id="slot-torrent<?= $TorrentID ?>_check1" style="display:<?= $TorrentChecked ? "inline-block" : "none" ?>;color:#649464;" title="<?= Lang::get('torrents', 'checked_by_before') ?><?= $TorrentChecked ? $TorrentCheckedBy : $LoggedUser['Username'] ?><?= Lang::get('torrents', 'checked_by_after') ?>"></a>
                            <a href="javascript:void(0)" class="far fa-circle tooltip" id="slot-torrent<?= $TorrentID ?>_check0" style="display:<?= $TorrentChecked ? "none" : "inline-block" ?>;color:#CF3434;" title="<?= Lang::get('torrents', 'turn_me_green') ?>"></a>
                        <?
                        } else {

                        ?>
                            <i class="tooltip far fa-<?= $TorrentChecked ? "check-" : "" ?>circle" style="color: <?= $TorrentChecked ? "#74B274" : "#A6A6A6" ?>;" title="<?= $TorrentChecked ? Lang::get('torrents', 'has_been_checked') : Lang::get('torrents', 'has_not_been_checked') ?><?= Lang::get('torrents', 'checked_explanation') ?>"></i>
                        <?
                        }
                        ?>
                        &nbsp;
                        <?
                        if (($Dupe || empty($Slot)) && check_perms("torrents_slot_edit")) {
                            $TorrentInfo = "<strong style='display:inline' class='important_text'>$TorrentInfo</strong>";
                        }
                        ?>
                        <span>[ <a href="torrents.php?action=download&amp;id=<?= $TorrentID ?>&amp;authkey=<?= $LoggedUser['AuthKey'] ?>&amp;torrent_pass=<?= $LoggedUser['torrent_pass'] ?>" class="tooltip" title="<?= Lang::get('global', 'download') ?>"><?= ($HasFile ? 'DL' : 'Missing') ?></a>
                            <? if (Torrents::can_use_token($Torrent)) { ?>
                                | <a href="torrents.php?action=download&amp;id=<?= $TorrentID ?>&amp;authkey=<?= $LoggedUser['AuthKey'] ?>&amp;torrent_pass=<?= $LoggedUser['torrent_pass'] ?>&amp;usetoken=1" class="tooltip" title="<?= Lang::get('global', 'use_fl_tokens') ?>" onclick="return confirm('<?= FL_confirmation_msg($Torrent['Seeders'], $Torrent['Size']) ?>');">FL</a>
                            <?  } ?>
                            | <a href="reportsv2.php?action=report&amp;id=<?= $TorrentID ?>" class="tooltip" title="<?= Lang::get('torrents', 'report') ?>">RP</a>
                            <? if ($CanEdit) { ?>
                                | <a href="torrents.php?action=edit&amp;id=<?= $TorrentID ?>" class="tooltip" title="<?= Lang::get('global', 'edit') ?>">ED</a>
                            <?  }
                            if (check_perms('torrents_delete') || $UserID == $LoggedUser['ID']) { ?>
                                | <a href="torrents.php?action=delete&amp;torrentid=<?= $TorrentID ?>" class="tooltip" title="<?= Lang::get('torrents', 'remove') ?>">RM</a>
                            <?  } ?>
                            | <a href="torrents.php?torrentid=<?= $TorrentID ?>" class="tooltip" title="<?= Lang::get('torrents', 'permalink') ?>">PL</a>

                            ]</span>
                        <a class="specs tooltip" title="<?= Lang::get('torrents', Torrents::slot_name($Slot) . '_slot') ?>" href="#" onclick="$('#torrent_slot_<?= $TorrentID ?>').gtoggle(); return false;"><?= $TorrentInfo ?></a>

                    </td>
                    <td class="td_size number_column nobr"><?= Format::get_size($Size) ?></td>
                    <?
                    if (check_perms("torrents_slot_edit")) {
                    ?>
                        <td class="no_padding">
                            <input type="hidden" name="torrents[]" value="<?= $TorrentID ?>" />
                            <select class="slot_selector" name="slots[]">
                                <?
                                foreach (Torrents::get_resolution_slots($Resolution) as $RSlot) {
                                ?>
                                    <?= Torrents::slot_option($RSlot, false, $Slot, $IsExtraSlot) ?>
                                <?
                                }
                                $extraName = Lang::get('torrents', 'additional_slots');
                                ?>
                                <optgroup label="<?= $extraName ?>">
                                    <?
                                    foreach (Torrents::get_resolution_slots($Resolution) as $RSlot) {
                                        if ($RSlot == TorrentSlotType::None) {
                                            continue;
                                        }
                                    ?>
                                        <?= Torrents::slot_option($RSlot, true, $Slot, $IsExtraSlot) ?>
                                    <?
                                    }
                                    ?>
                                </optgroup>
                            </select>
                        </td>
                    <?
                    } else {
                        $SlotName = Torrents::slot_option_lang($Slot);
                        if (empty($SlotName)) {
                            $SlotName = '---';
                        } else {
                            $SlotName = Lang::get('torrents', $SlotName);
                        }
                        if ($IsExtraSlot) {
                            $SlotName .= '*';
                        }
                    ?>
                        <td class="highlight_slot_name"><?= $SlotName ?></td>
                    <?
                    }
                    ?>

                </tr>
            <?
                print_torrent_detail($GroupID, $Torrent, $ReleaseType, $EditionID, $GroupCategoryID, 'slot');
            }
            ?>
    <?
        }

        $LastRemasterTitle = $RemasterTitle;
        $LastRemasterCustomTitle = $RemasterCustomTitle;
        $LastResolution = $Resolution;
        $LastNotMain = $NotMainMovie;
    }
}

function print_torrent_detail($GroupID, $Torrent, $ReleaseType, $EditionID, $GroupCategoryID, $View = '') {
    $TorrentID = $Torrent['ID'];
    $Size = $Torrent['Size'];
    $Seeders = $Torrent['Seeders'];
    $TorrentTime = $Torrent['Time'];
    $Subtitles = $Torrent['Subtitles'];
    $ExternalSubtitles = $Torrent['ExternalSubtitles'];
    $ExternalSubtitleIDs = $Torrent['ExternalSubtitleIDs'];
    $Description = $Torrent['Description'];
    $MediaInfos = $Torrent['MediaInfo'];
    $Note = $Torrent['Note'];
    $SubtitleType = $Torrent['SubtitleType'];
    $FileList = $Torrent['FileList'];
    $LastReseedRequest = $Torrent['LastReseedRequest'];
    $FilePath = $Torrent['FilePath'];
    $UserID = $Torrent['UserID'];
    $BadTags = $Torrent['BadTags'];
    $BadFolders = $Torrent['BadFolders'];
    $BadImg = $Torrent['BadImg'];
    $BadFiles = $Torrent['BadFiles'];
    $BadCompress = $Torrent['BadCompress'];
    $NoSub = $Torrent['NoSub'];
    $HardSub = $Torrent['HardSub'];
    $LastActive = $Torrent['last_action'];
    $CustomTrumpable = $Torrent['CustomTrumpable'];
    $Dead = Torrents::is_torrent_dead($Torrent);
    G::$DB->query("
        select tt.id, 
            count(tb.fromuserid) count, 
            (
                select count(1) 
                    from thumb where
                    itemid = tt.id
                    and fromuserid=" . G::$LoggedUser['ID'] . "
                    and type = 'torrent'
            ) 'on'
        from torrents as tt
        left join thumb as tb 
            on tt.id = tb.itemid 
            and tb.type = 'torrent' 
        WHERE tt.groupid = $GroupID
        group by tt.id");
    $ThumbCounts = G::$DB->to_array('id');
    G::$DB->query("
        SELECT `ID`,
             sum(bonus) Count,
             (select group_concat(bonus)
                from torrents_send_bonus where torrentid=t.id
                and FromUserID = " . G::$LoggedUser['ID'] . "
             ) Sended
        FROM `torrents` as t
        left join torrents_send_bonus as tsb 
            on t.id = tsb.torrentid
        where t.groupid = $GroupID
        group by id");
    $BonusSended = G::$DB->to_array('ID');
    $Reported = false;
    unset($ReportedTimes);
    $TrumpableMsg = '';
    $TrumpableAddExtra = '';

    if (!empty($BadTags)) {
        $TrumpableMsg .= $TrumpableAddExtra . Lang::get('torrents', 'bad_tags');
        $TrumpableAddExtra = ' / ';
    }
    if (!empty($BadFolders)) {
        $TrumpableMsg .= $TrumpableAddExtra . Lang::get('torrents', 'bad_filename');
        $TrumpableAddExtra = ' / ';
    }
    if (!empty($BadImg)) {
        $TrumpableMsg .= $TrumpableAddExtra . Lang::get('torrents', 'bad_img');
        $TrumpableAddExtra = ' / ';
    }
    if (!empty($BadFiles)) {
        $TrumpableMsg .= $TrumpableAddExtra . Lang::get('torrents', 'bad_files');
        $TrumpableAddExtra = ' / ';
    }
    if (!empty($BadCompress)) {
        $TrumpableMsg .= $TrumpableAddExtra . Lang::get('torrents', 'uncompressed_flacs');
        $TrumpableAddExtra = ' / ';
    }
    if (!empty($MissingLineage)) {
        $TrumpableMsg .= $TrumpableAddExtra . Lang::get('torrents', 'miss_info');
        $TrumpableAddExtra = ' / ';
    }

    if (!empty($NoSub)) {
        $TrumpableMsg .= $TrumpableAddExtra . Lang::get('upload', 'no_sub');
        $TrumpableAddExtra = ' / ';
    }
    if (!empty($HardSub)) {
        $TrumpableMsg .= $TrumpableAddExtra . Lang::get('upload', 'hardcode_sub');
        $TrumpableAddExtra = ' / ';
    }
    if (!empty($CustomTrumpable)) {
        $TrumpableMsg .= $TrumpableAddExtra . $CustomTrumpable;
        $TrumpableAddExtra = ' / ';
    }
    if ($Dead) {
        $TrumpableMsg .= $TrumpableAddExtra . Lang::get('upload', 'dead_torrent');
        $TrumpableAddExtra = ' / ';
    }

    $Reports = Torrents::get_reports($TorrentID);
    $NumReports = count($Reports);

    if ($NumReports > 0) {
        $Reported = true;
        //include(SERVER_ROOT.'/sections/reportsv2/array.php');
        include(Lang::getLangfilePath("report_types"));
        $ReportInfo = '
                <div class="table_container border torrentdetails">
            <table class="reportinfo_table">
                <tr class="colhead_dark" style="font-weight: bold;">
                    <td>' . Lang::get('torrents', 'this_torrent_has_active_reports_1') . $NumReports . Lang::get('torrents', 'this_torrent_has_active_reports_2') . ($NumReports === 1 ? Lang::get('torrents', 'this_torrent_has_active_reports_3') : Lang::get('torrents', 'this_torrent_has_active_reports_4')) . ":</td>
                </tr>";
        // <a class='float_right report_reply_btn' href='#'>".Lang::get('torrents', 'reply')."</a> 这个是回复按钮，等开发做完整个报告回复功能后再填进去
        foreach ($Reports as $Report) {
            $ReportID = $Report['ID'];
            if (check_perms('admin_reports')) {
                $ReporterID = $Report['ReporterID'];
                $Reporter = Users::user_info($ReporterID);
                $ReporterName = $Reporter['Username'];
                $ReportLinks = "<a href=\"user.php?id=$ReporterID\">$ReporterName</a> <a href=\"reportsv2.php?view=report&amp;id=$Report[ID]\">" . Lang::get('torrents', 'reported_it') . "</a>";
                $UploaderLinks = Users::format_username($UserID, false, false, false) . " " . Lang::get('torrents', 'reply_at');
            } else {
                $ReportLinks = Lang::get('torrents', 'someone_reported_it');
                $UploaderLinks = Lang::get('torrents', 'uploader_replied_it');
            }

            if (isset($Types[$GroupCategoryID][$Report['Type']])) {
                $ReportType = $Types[$GroupCategoryID][$Report['Type']];
            } elseif (isset($Types['master'][$Report['Type']])) {
                $ReportType = $Types['master'][$Report['Type']];
            } else {
                //There was a type but it wasn't an option!
                $ReportType = $Types['master']['other'];
            }
            $CanReply = $UserID == G::$LoggedUser['ID'] && !$Report['UploaderReply'];
            $ReportInfo .= "
                <tr>
                    <td>$ReportLinks" . Lang::get('torrents', 'at') . " " . time_diff($Report['ReportedTime'], 2, true, true) . Lang::get('torrents', 'for_the_reason') . $ReportType['title'] . '":' . ($CanReply ? ('<a class="float_right report_reply_btn" onclick="$(\'.can_reply_' . $ReportID . '\').toggle()" href="javascript:void(0)">' . Lang::get('torrents', 'reply') . '</a>') : "") . '
                        <blockquote>' . Text::full_format($Report['UserComment']) . ($Report['UploaderReply'] ? ('<hr class="report_inside_line">' . $UploaderLinks . ' ' . time_diff($Report['ReplyTime'], 2, true, true) . ':<br>' . Text::full_format($Report['UploaderReply'])) : '') . '</blockquote>
                    </td>
                </tr>';
            $area = new TEXTAREA_PREVIEW('uploader_reply', '', '', 50, 10, false, false, true, array(
                'placeholder="' . Lang::get('torrents', 'reply_it_patiently') . '"'
            ), false);
            $ReportInfo .= $CanReply ? '
                <tr class="report_reply_tr can_reply_' . $ReportID . '" style="display: none;">
                    <td class="report_reply_td" align="center">
                        <form action="reportsv2.php?action=takeuploaderreply" method="POST">
                            <input type="hidden" name="reportid" value="' . $ReportID . '">
                            <input type="hidden" name="torrentid" value="' . $TorrentID . '">
                            ' . $area->getBuffer() . '
                            <div class="box vertical_space body hidden preview_wrap" id="preview_wrap_' . $area->getID() . '">
                                <div id="preview_' . $area->getID() . '"></div>
                            </div>
                            <div class="submit_div preview_submit">
                                <input type="button" value="Preview" class="hidden button_preview_' . $area->getID() . '" />
                                <input type="submit">
                            </div>
                        </form>
                    </td>
                </tr>' : "";
        }
        $ReportInfo .= "\n\t\t</table></div>";
    }
    $RegenLink = check_perms('users_mod') ? ' <a href="torrents.php?action=regen_filelist&amp;torrentid=' . $TorrentID . '" class="brackets">' . Lang::get('torrents', 'regenerate') . '</a>' : '';
    $FileTable = '
        <div class="table_container border torrentdetails">
        <table class="filelist_table">
            <tr class="colhead_dark">
                <td>
                    <div class="filelist_title" style="float: left;">' . Lang::get('torrents', 'file_names') . $RegenLink . '</div>
                    <div class="filelist_path" style="float: right;">' . ($FilePath ? "/$FilePath/" : '/') . '</div>
                </td>
                <td class="nobr">
                    <strong>' . Lang::get('torrents', 'size') . '</strong>
                </td>
            </tr>';
    if (substr($FileList, -3) == '}}}') { // Old style
        $FileListSplit = explode('|||', $FileList);
        foreach ($FileListSplit as $File) {
            $NameEnd = strrpos($File, '{{{');
            $Name = substr($File, 0, $NameEnd);
            if ($Spaces = strspn($Name, ' ')) {
                $Name = str_replace(' ', '&nbsp;', substr($Name, 0, $Spaces)) . substr($Name, $Spaces);
            }
            $FileSize = substr($File, $NameEnd + 3, -3);
            $FileTable .= sprintf("\n<tr><td>%s</td><td class=\"number_column nobr\">%s</td></tr>", $Name, Format::get_size($FileSize));
        }
    } else {
        $FileListSplit = explode("\n", $FileList);
        foreach ($FileListSplit as $File) {
            $FileInfo = Torrents::filelist_get_file($File);
            $FileTable .= sprintf("\n<tr><td>%s</td><td class=\"number_column nobr\">%s</td></tr>", $FileInfo['name'], Format::get_size($FileInfo['size']));
        }
    }
    $FileTable .= '
        </table></div>';
    ?>
    <tr class="releases_<?= $ReleaseType ?> groupid_<?= $GroupID ?> edition_<?= $EditionID ?> torrentdetails pad <? if (!isset($_GET['torrentid']) || $_GET['torrentid'] != $TorrentID) { ?>hidden<? } ?>" id="torrent_<?= $View . '_' . $TorrentID; ?>">
        <td colspan="6" class="cmp-torrent-description">
            <div id="release_<?= $TorrentID ?>" class="no_overflow">
                <blockquote>
                    <div>
                        <?= Lang::get('torrents', 'upload_by_before') ?><?= Users::format_username($UserID, false, false, false) ?><?= Lang::get('torrents', 'upload_by_after') ?> <?= time_diff($TorrentTime); ?>
                        <?

                        if ($Seeders == 0) {
                            // If the last time this was seeded was 50 years ago, most likely it has never been seeded, so don't bother
                            // displaying "Last active: 2000+ years" as that's dumb
                            if (time() - strtotime($LastActive) > 1576800000) {
                        ?>
                                <span>&nbsp;&nbsp;|&nbsp;&nbsp;</span><?= Lang::get('torrents', 'last_active') ?>:&nbsp;<?= Lang::get('torrents', 'never') ?>
                            <?
                            } elseif ($LastActive != '0000-00-00 00:00:00' && time() - strtotime($LastActive) >= 1209600) {
                            ?>
                                <span>&nbsp;&nbsp;|&nbsp;&nbsp;</span><strong><?= Lang::get('torrents', 'last_active') ?> <?= time_diff($LastActive); ?></strong>
                            <?
                            } else {
                            ?><span>&nbsp;&nbsp;|&nbsp;&nbsp;</span><?= Lang::get('torrents', 'last_active') ?> <?= time_diff($LastActive); ?>
                            <? }
                        }
                        if (($Seeders == 0 &&
                                $LastActive != '0000-00-00 00:00:00' &&
                                time() - strtotime($LastActive) >= 345678 &&
                                time() - strtotime($LastReseedRequest) >= 864000) ||
                            check_perms('users_mod')
                        ) {
                            ?><span>&nbsp;&nbsp;|&nbsp;&nbsp;</span><a href="torrents.php?action=reseed&amp;torrentid=<?= $TorrentID ?>&amp;groupid=<?= $GroupID ?>" class="brackets" onclick="return confirm('<?= Lang::get('torrents', 'request_re_seed_confirm') ?>');"><?= Lang::get('torrents', 'request_re_seed') ?></a>
                        <?  }
                        ?>
                        <div id="icon-cnt">
                            <span class="tooltip" title="<?=Lang::get('torrents', 'total_reward_bonus_points_pre_tax')?>">
                                <?=ICONS['bonus']?>
                            </span>
                            <span title="<?= Lang::get('torrents', 'total_reward_bonus_points_pre_tax') ?>" id="bonuscnt<?= $TorrentID ?>"><?= isset($BonusSended[$TorrentID]) && isset($BonusSended[$TorrentID]['Count']) && $BonusSended[$TorrentID]['Count'] > 0 ? $BonusSended[$TorrentID]['Count'] : '0' ?></span>&nbsp;&nbsp;&nbsp;
                            <span id="thumb<?= $TorrentID ?>" <?= isset($ThumbCounts[$TorrentID]) && isset($ThumbCounts[$TorrentID]['on']) && $ThumbCounts[$TorrentID]['on'] > 0 ? 'style="display: none;"' : '' ?>><?=
                                                                                                                                                                                                                    G::$LoggedUser['ID'] == $UserID ? "<i title=\"" . Lang::get('torrents', 'you_cant_like_yourself') . "\" class=\"far fa-thumbs-up\"></i>" : "<a href=\"javascript:void(0);\" onclick=\"thumb($TorrentID, $UserID, 'torrent')\"><i class=\"far fa-thumbs-up\"></i></a>"
                                                                                                                                                                                                                    ?></span>
                            <span id="unthumb<?= $TorrentID ?>" <?= isset($ThumbCounts[$TorrentID]) && !$ThumbCounts[$TorrentID]['on'] ? 'style="display: none;"' : '' ?>><a href="javascript:void(0);" onclick="unthumb(<?= $TorrentID ?>, <?= $UserID ?>, 'torrent')"><i class="fas fa-thumbs-up"></i></a></span>
                            <span id="thumbcnt<?= $TorrentID ?>"><?= isset($ThumbCounts[$TorrentID]) && isset($ThumbCounts[$TorrentID]['count']) ? $ThumbCounts[$TorrentID]['count'] : Lang::get('torrents', 'like') ?></span>
                        </div>
                    </div>
                    <?
                    $NewRatio = Format::get_ratio_html(G::$LoggedUser['BytesUploaded'], G::$LoggedUser['BytesDownloaded'] + $Size);
                    ?>
                    <br><?= Lang::get('torrents', 'if_you_download_this_before') ?> <?= $NewRatio ?><?= Lang::get('torrents', 'if_you_download_this_after') ?>
                    <?

                    if ($TrumpableMsg) {
                    ?>
                        <hr>
                        <table>
                            <tr>
                                <td><b><?= Lang::get('torrents', 'trumpable_reason') ?>:&nbsp;</b></td>
                                <td><?= $TrumpableMsg ?></td>
                            </tr>
                        </table>
                    <?
                    }
                    ?>
                </blockquote>
            </div>
            <? if (check_perms('site_moderate_requests')) { ?>
                <div class="linkbox">
                    <a href="torrents.php?action=masspm&amp;id=<?= $GroupID ?>&amp;torrentid=<?= $TorrentID ?>" class="brackets"><?= Lang::get('torrents', 'masspm') ?></a>
                </div>
            <?  } ?>
            <div class="linkbox">
                <a href="#" class="brackets" onclick="show_peers('<?= $TorrentID ?>', 0, '<?= $View ?>'); return false;"><?= Lang::get('torrents', 'view_peer_list') ?></a>
                <? if (check_perms('site_view_torrent_snatchlist')) { ?>
                    <a href="#" class="brackets tooltip" onclick="show_downloads('<?= $TorrentID ?>', 0, '<?= $View ?>'); return false;" title="<?= Lang::get('torrents', 'show_downloads_title') ?>"><?= Lang::get('torrents', 'view_download_list') ?></a>
                    <a href="#" class="brackets tooltip" onclick="show_snatches('<?= $TorrentID ?>', 0, '<?= $View ?>'); return false;" title="<?= Lang::get('torrents', 'show_snatches_title') ?>"><?= Lang::get('torrents', 'view_snatch_list') ?></a>
                <?  } ?>
                <a href="#" class="brackets" onclick="show_giver('<?= $TorrentID ?>', 0, '<?= $View ?>'); return false;"><?= Lang::get('torrents', 'giver_list') ?></a>
                <a href="#" class="brackets" onclick="show_files('<?= $TorrentID ?>', '<?= $View ?>'); return false;"><?= Lang::get('torrents', 'view_file_list') ?></a>
                <? if ($Reported) { ?>
                    <a href="#" class="brackets" onclick="show_reported('<?= $TorrentID ?>','<?= $View ?>'); return false;"><?= Lang::get('torrents', 'view_report_information') ?></a>
                <?  } ?>
                <div id="sendbonus_<?= $TorrentID ?>" class="torrentdetails_sendbonus">
                    <?
                    $Sended = isset($BonusSended[$TorrentID]) ? explode(',', $BonusSended[$TorrentID]['Sended']) : [];
                    ?>
                    <span title="<?=G::$LoggedUser['ID'] == $UserID? Lang::get('torrents', 'you_cant_reward_yourself'): Lang::get('torrents', 'you_have_rewarded')?>" style="color: #ff6600; <?=in_array(5, $Sended) || G::$LoggedUser['ID'] == $UserID? "": "display: none;"?>" id="bonus5<?=$TorrentID?>">
                        <?=ICONS['bonus active']?>
                    </span>
                    <a title="<?=Lang::get('torrents', 'reward_5_bonus_to_uploader')?>" style="<?=in_array(5, $Sended) || G::$LoggedUser['ID'] == $UserID? "display: none;": ""?>" id="abonus5<?=$TorrentID?>" href="javascript:void(0);" onclick="sendbonus(<?=$TorrentID?>, 5)">
                        <?=ICONS['bonus']?>
                    </a>5&nbsp;&nbsp;&nbsp;
                    <span title="<?=G::$LoggedUser['ID'] == $UserID? Lang::get('torrents', 'you_cant_reward_yourself'): Lang::get('torrents', 'you_have_rewarded')?>" style="color: #ff6600; <?=in_array(30, $Sended) || G::$LoggedUser['ID'] == $UserID? "": "display: none;"?>" id="bonus30<?=$TorrentID?>">
                        <?=ICONS['bonus active']?>
                    </span>
                    <a title = "<?=Lang::get('torrents', 'reward_30_bonus_to_uploader')?>" style="<?=in_array(30, $Sended) || G::$LoggedUser['ID'] == $UserID? "display: none;": ""?>" id="abonus30<?=$TorrentID?>" href="javascript:void(0);" onclick="sendbonus(<?=$TorrentID?>, 30)">
                        <?=ICONS['bonus']?>
                    </a>30&nbsp;&nbsp;&nbsp;
                    <span title="<?=G::$LoggedUser['ID'] == $UserID? Lang::get('torrents', 'you_cant_reward_yourself'): Lang::get('torrents', 'you_have_rewarded')?>" style="color: #ff6600; <?=in_array(100, $Sended) || G::$LoggedUser['ID'] == $UserID? "": "display: none;"?>" id="bonus100<?=$TorrentID?>">
                        <?=ICONS['bonus active']?>
                    </span>
                    <a title="<?=Lang::get('torrents', 'reward_100_bonus_to_uploader')?>" style="<?=in_array(100, $Sended) || G::$LoggedUser['ID'] == $UserID? "display: none;": ""?>" id="abonus100<?=$TorrentID?>" href="javascript:void(0);" onclick="sendbonus(<?=$TorrentID?>, 100)">
                        <?=ICONS['bonus']?>
                    </a>100&nbsp;&nbsp;&nbsp;
                    <span title="<?=G::$LoggedUser['ID'] == $UserID? Lang::get('torrents', 'you_cant_reward_yourself'): Lang::get('torrents', 'you_have_rewarded')?>" style="color: #ff6600; <?=in_array(300, $Sended) || G::$LoggedUser['ID'] == $UserID? "": "display: none;"?>" id="bonus300<?=$TorrentID?>">
                        <?=ICONS['bonus active']?>
                    </span>
                    <a title = "<?=Lang::get('torrents', 'reward_300_bonus_to_uploader')?>" style="<?=in_array(300, $Sended) || G::$LoggedUser['ID'] == $UserID? "display: none;": ""?>" id="abonus300<?=$TorrentID?>" href="javascript:void(0);" onclick="sendbonus(<?=$TorrentID?>, 300)">
                        <?=ICONS['bonus']?>
                    </a>300
                    </dic>
                </div>
                <div id="<?= $View ?>_giver_<?= $TorrentID ?>" class="torrentdetails_giver hidden"></div>
                <div id="<?= $View ?>_peers_<?= $TorrentID ?>" class="torrentdetails_peers hidden"></div>
                <div id="<?= $View ?>_downloads_<?= $TorrentID ?>" class="torrentdetails_downloads hidden"></div>
                <div id="<?= $View ?>_snatches_<?= $TorrentID ?>" class="torrentdetails_snatches hidden"></div>
                <div id="<?= $View ?>_files_<?= $TorrentID ?>" class="torrentdetails_files hidden"><?= $FileTable ?></div>
                <? if ($Reported) { ?>
                    <div id="<?= $View ?>_reported_<?= $TorrentID ?>" class="torrentdetails_reported hidden"><?= $ReportInfo ?></div>
                <?
                }
                if ($Note) {
                    echo "\n<blockquote><strong class='important_text'>" . Lang::get('upload', 'staff_note') . ":&nbsp;</strong>";
                    if (!empty($Note)) {
                        echo Text::full_format($Note);
                    }
                    echo "</blockquote>";
                }

                ?>
                <blockquote id="subtitles_box" class="subtitles-container">
                    <div id="subtitles_box_header">
                        <?
                        ?>
                        <span id="subtitles_box_title"><?= Lang::get('global', 'subtitles') ?>:</span>
                        <?
                        ?>
                        <span class="float_right"><a href="subtitles.php?torrent_id=<?= $TorrentID ?>"><?= Lang::get('torrents', 'add_subtitles') ?></a></span>
                        <?
                        if (!$Subtitles && !$ExternalSubtitleIDs) {
                            echo '<span class="subtitles-item national_flags tooltip" title="' . Lang::get('upload', "no_subtitles") . '">' . ICONS['no-subtitle'] . '</span>';
                        }
                        ?>
                    </div>
                    <?
                    if ($Subtitles) {
                        $SubtitleArray = explode(',', $Subtitles);
                    ?>
                        <div class="subtitles-list" id="subtitles_box_in_torrent">
                            <span><?= $SubtitleType == 1 ? Lang::get('global', 'in_torrent_subtitles') : Lang::get('global', 'in_torrent_hard_subtitles'); ?>:</span>
                            <?
                            foreach ($SubtitleArray as $Subtitle) {
                                echo '<span class="subtitles-item national_flags tooltip" title="' . Lang::get('upload', $Subtitle) . '">' . getFlag($Subtitle) . '</span>';
                            }
                            ?>
                        </div>
                    <?
                    }
                    if ($ExternalSubtitleIDs) {
                        $ExternalSubtitleIDArray = explode('|', $ExternalSubtitleIDs);
                        $ExternalSubtitleArray = explode('|', $ExternalSubtitles);
                    ?>
                        <div class="subtitles-list" id="subtitles_box_external">
                            <span><?= Lang::get('global', 'external_subtitles') ?>:</span>
                            <?
                            foreach ($ExternalSubtitleIDArray as $index => $ExternalSubtitleID) {
                                $SubtitleLanguages = $ExternalSubtitleArray[$index];
                                $SubtitleLanguagesArray = explode(',', $SubtitleLanguages);
                                if (in_array('chinese_simplified', $SubtitleLanguagesArray)) {
                                    echo '<a class="subtitles-item" href="subtitles.php?action=download&id=' . $ExternalSubtitleID . '">' . getFlag('chinese_simplified') . '</a>';
                                } else if (in_array('chinese_traditional', $SubtitleLanguagesArray)) {
                                    echo '<a class="subtitles-item" href="subtitles.php?action=download&id=' . $ExternalSubtitleID . '">' . getFlag('chinese_traditional') . '</a>';
                                } else if ($SubtitleLanguagesArray[0]) {
                                    echo '<a class="subtitles-item" href="subtitles.php?action=download&id=' . $ExternalSubtitleID . '">' . getFlag($SubtitleLanguagesArray[0]) . '</a>';
                                }
                            }
                            ?>
                        </div>
                        <?
                        ?>
                    <?
                    }
                    ?>
                </blockquote>
                <?
                if (!empty($MediaInfos) || !empty($Description)) {
                    echo "\n<blockquote>";
                    if (!empty($MediaInfos)) {
                        $Index = 0;
                        $MediaInfoObj = json_decode($MediaInfos);
                        if (is_array($MediaInfoObj)) {
                            foreach ($MediaInfoObj as $MediaInfo) {
                                $MediaInfo = ltrim(trim($MediaInfo), '[mediainfo]');
                                $MediaInfo = ltrim(trim($MediaInfo), '[bdinfo]');
                                $MediaInfo = rtrim(trim($MediaInfo), '[/mediainfo]');
                                $MediaInfo = rtrim(trim($MediaInfo), '[/bdinfo]');
                                echo ($Index > 0 ? "<br>" : "") . Text::full_format('[mediainfo]' . $MediaInfo . '[/mediainfo]');
                                $Index++;
                            }
                        }
                    }
                    if (!empty($Description)) {
                        echo "<br>" . Text::full_format($Description);
                    }
                    echo '</blockquote>';
                }
                ?>
        </td>
    </tr>
<?
}
