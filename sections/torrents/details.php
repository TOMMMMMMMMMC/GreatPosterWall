<?php
include(SERVER_ROOT . '/classes/torrenttable.class.php');
function compare($X, $Y) {
    return ($Y['score'] - $X['score']);
}
header('Access-Control-Allow-Origin: *');

define('MAX_PERS_COLLAGES', 3); // How many personal collages should be shown by default
define('MAX_COLLAGES', 5); // How many normal collages should be shown by default

$GroupID = ceil($_GET['id']);
if (!empty($_GET['revisionid']) && is_number($_GET['revisionid'])) {
    $RevisionID = $_GET['revisionid'];
} else {
    $RevisionID = 0;
}

include(SERVER_ROOT . '/sections/torrents/functions.php');
$TorrentCache = get_group_info($GroupID, true, $RevisionID);
$TorrentDetails = $TorrentCache[0];
$TorrentList = $TorrentCache[1];
$View = isset($_GET['view']) ? $_GET['view'] : '';

// Group details
list(
    $WikiBody, $WikiImage, $IMDBID, $IMDBRating, $Duration, $ReleaseDate, $Region, $Language, $RTRating, $DoubanRating, $IMDBVote, $DoubanVote, $DoubanID, $RTTitle, $GroupID, $GroupName, $GroupYear,
    $GroupRecordLabel, $GroupCatalogueNumber, $ReleaseType, $GroupCategoryID,
    $GroupTime, $GroupVanityHouse, $TorrentTags, $TorrentTagIDs, $TorrentTagUserIDs,
    $TagPositiveVotes, $TagNegativeVotes, $SubName
) = array_values($TorrentDetails);
$RawName = Torrents::torrent_group_name($TorrentDetails, true);
$DisplayName = "<span dir=\"ltr\">$RawName</span>";


$WikiBody = Text::full_format($WikiBody);

$Artists = Artists::get_artist($GroupID);
$Director = null;
foreach ($Artists[1] as $ID => $Artist) {
    $Director = $Artist;
    break;
}

$Title = $RawName;
$AltName = $RawName;

$Tags = array();
$TagNames = array();
if ($TorrentTags != '') {
    $TorrentTags = explode('|', $TorrentTags);
    $TorrentTagIDs = explode('|', $TorrentTagIDs);
    $TorrentTagUserIDs = explode('|', $TorrentTagUserIDs);
    $TagPositiveVotes = explode('|', $TagPositiveVotes);
    $TagNegativeVotes = explode('|', $TagNegativeVotes);

    foreach ($TorrentTags as $TagKey => $TagName) {
        $Tags[$TagKey]['name'] = $TagName;
        $Tags[$TagKey]['score'] = ($TagPositiveVotes[$TagKey] - $TagNegativeVotes[$TagKey]);
        $Tags[$TagKey]['id'] = $TorrentTagIDs[$TagKey];
        $Tags[$TagKey]['userid'] = $TorrentTagUserIDs[$TagKey];
        $TagNames[] = $TagName;
    }
    uasort($Tags, 'compare');
}



$CoverArt = $Cache->get_value("torrents_cover_art_$GroupID");
if (!$CoverArt) {
    $DB->query("
		SELECT ID, Image, Summary, UserID, Time
		FROM cover_art
		WHERE GroupID = '$GroupID'
		ORDER BY Time ASC");
    $CoverArt = array();
    $CoverArt = $DB->to_array();
    if ($DB->has_results()) {
        $Cache->cache_value("torrents_cover_art_$GroupID", $CoverArt, 0);
    }
}

// Comments (must be loaded before View::show_header so that subscriptions and quote notifications are handled properly)
list($NumComments, $Page, $Thread, $LastRead) = Comments::load('torrents', $GroupID);

// Start output
View::show_header($Title, 'browse,comments,torrent,bbcode,recommend,cover_art,subscriptions,sendbonus,thumb');
?>
<div class="thin">
    <div class="header">
        <!-- <h2><?= $DisplayName ?><br><? if ($SubName) {
                                            echo " <a href=\"torrents.php?searchstr=" . $SubName . "\">$SubName</a>";
                                        } ?></h2> -->
        <div class="linkbox">
            <? if (check_perms('site_edit_wiki')) { ?>
                <a href="torrents.php?action=editgroup&amp;groupid=<?= $GroupID ?>" class="brackets"><?= Lang::get('torrents', 'editgroup') ?></a>
            <?  } ?>
            <a href="torrents.php?action=editrequest&amp;groupid=<?= $GroupID ?>" class="brackets"><?= Lang::get('torrents', 'editrequest') ?></a>
            <a href="torrents.php?action=history&amp;groupid=<?= $GroupID ?>" class="brackets"><?= Lang::get('torrents', 'viewhistory') ?></a>
            <? if ($RevisionID && check_perms('site_edit_wiki')) { ?>
                <a href="torrents.php?action=revert&amp;groupid=<?= $GroupID ?>&amp;revisionid=<?= $RevisionID ?>&amp;auth=<?= $LoggedUser['AuthKey'] ?>" class="brackets"><?= Lang::get('torrents', 'revert') ?></a>
            <?
            }
            if (Bookmarks::has_bookmarked('torrent', $GroupID)) {
            ?>
                <a href="#" id="bookmarklink_torrent_<?= $GroupID ?>" class="remove_bookmark brackets" onclick="Unbookmark('torrent', <?= $GroupID ?>, '<?= Lang::get('global', 'add_bookmark') ?>'); return false;"><?= Lang::get('global', 'remove_bookmark') ?></a>
            <?  } else { ?>
                <a href="#" id="bookmarklink_torrent_<?= $GroupID ?>" class="add_bookmark brackets" onclick="Bookmark('torrent', <?= $GroupID ?>, '<?= Lang::get('global', 'remove_bookmark') ?>'); return false;"><?= Lang::get('global', 'add_bookmark') ?></a>
            <?  } ?>
            <a href="#" id="subscribelink_torrents<?= $GroupID ?>" class="brackets" onclick="SubscribeComments('torrents', <?= $GroupID ?>); return false;"><?= Subscriptions::has_subscribed_comments('torrents', $GroupID) !== false ? Lang::get('global', 'unsubscribe') : Lang::get('global', 'subscribe') ?></a>
            <!-- <a href="#" id="recommend" class="brackets">Recommend</a> -->
            <?
            if ($Categories[$GroupCategoryID - 1] == 'Movies') { ?>
                <a href="upload.php?groupid=<?= $GroupID ?>" class="brackets"><?= Lang::get('torrents', 'add_format') ?></a>
            <?
            }
            if (check_perms('site_submit_requests')) { ?>
                <a href="requests.php?action=new&amp;groupid=<?= $GroupID ?>" class="brackets"><?= Lang::get('torrents', 'req_format') ?></a>
            <?  } ?>
            <a href="torrents.php?action=grouplog&amp;groupid=<?= $GroupID ?>" class="brackets"><?= Lang::get('torrents', 'viewlog') ?></a>
        </div>
    </div>
    <!-- IMDB -->
    <div class="torrent_imdb_info_container">
        <div class="box">
            <div class="head hidden"></div>
            <div class="body">
                <div class="imdb_info">

                    <div class="img_box">
                        <div class="poltimg"><img src="<? print_r($WikiImage) ?>" onclick="lightbox.init(this, $(this).width());">
                        </div>
                    </div>


                    <div id=movie_title>
                        <div class="imdbtitle">
                            <h11><?= $GroupName ?>&nbsp;<small><i>(<? print_r($GroupYear) ?>)</i></small></h11>
                        </div>
                        <h2>
                            <? if ($SubName) {
                                echo " <a href=\"torrents.php?searchstr=" . $SubName . "\">$SubName</a>";
                            } ?>
                        </h2>
                    </div>
                    <div class="imdbcont">
                        <div class="cont">
                            <a class="info_crumbs tooltip" title="<?=Lang::get('global', 'imdb_rating')?>, <?=$IMDBVote.' '.Lang::get('torrents', 'movie_votes')?>" target="_blank" href="https://www.imdb.com/title/<?print_r($IMDBID)?>">
                                <?=ICONS['imdb']?>
                                <span><?=!empty($IMDBRating)?sprintf("%.1f",$IMDBRating):'--'?></span>
                            </a>
                            <a class="info_crumbs tooltip" title="<?=Lang::get('global', 'douban_rating')?>, <?=($DoubanVote?$DoubanVote:'?').' '.Lang::get('torrents', 'movie_votes')?>" target="_blank" href="https://movie.douban.com/subject/<?=$DoubanID?>/">
                                <?=ICONS['douban']?>
                                <span><?=!empty($DoubanRating)?sprintf("%.1f",$DoubanRating):'--'?></span>
                            </a>
                            <a class="info_crumbs tooltip <?=empty($RTRating)?'lack_of_info':''?>" title="<?=Lang::get('global', 'rt_rating')?>" target="_blank" href="https://www.rottentomatoes.com/<?=$RTTitle?>">
                                <?=ICONS['rotten-tomatoes']?>
                                <span><?=!empty($RTRating)?$RTRating:'--'?></span>
                            </a>
                            <span class="info_crumbs tooltip" title="<?=Lang::get('upload', 'director')?>">
                                <?=ICONS['movie-director']?>
                                <span><?print_r(Artists::display_artist($Director))?></span>
                            </span>
                            <?  if (!empty($Duration)) { ?>
                                <span class="info_crumbs tooltip" title="<?=Lang::get('torrents', 'imdb_runtime')?>">
                                    <?=ICONS['movie-runtime']?>
                                    <span><?=$Duration . " min" ?></span>
                                </span>
                            <?  } ?>
                            <?if (!empty($Region)) { ?>
                                <span class="info_crumbs tooltip" title="<?=Lang::get('torrents', 'imdb_region')?>">
                                    <?=ICONS['movie-country']?>
                                    <span><?print_r(implode(', ',array_slice(explode(',',$Region), 0, 2)))?></span>
                                </span>
                            <?  } ?>
                            <? if (!empty($Language)) { ?>
                                <span class="info_crumbs tooltip" title="<?=Lang::get('torrents', 'imdb_language')?>">
                                    <?=ICONS['movie-language']?>
                                    <span><?print_r(implode(', ',array_slice(explode(',',$Language), 0, 2)))?></span>
                                </span>
                            <?  } ?>
                        </div>
                        <div class="tags">
                            <span class="tag_crumbs tooltip" title="<?= Lang::get('torrents', 'tag') ?>">
                                <? print_r(implode($TagNames, '</span><span class="tag_crumbs tooltip" title="' . Lang::get('torrents', 'tag') . '">')) ?>
                            </span>
                        </div>

                    </div>
                    <div id="synopsis" class="tooltip tooltip" title="<?= Lang::get('torrents', 'fold_tooltip') ?>">
                        <!-- <strong><?= Lang::get('torrents', 'imdb_plot') ?></strong> -->
                        <p>
                            <? print_r($WikiBody) ?>
                        </p>
                    </div>
                    <div class="imdb_artists">
                        <?
                        for ($i = 0; $i < 10 && $i < count($Artists[6]); $i++) {
                        ?>
                            <div class="imdb_artist">
                                <a href="<? echo " artist.php?id=" . $Artists[6][$i]['id'] ?>"><img class="<?= $Artists[6][$i]['image'] ? '' : 'empty_photo' ?>" src="<? echo $Artists[6][$i]['image'] ?>">
                                    <p class="tooltip" title="<? echo $Artists[6][$i]['name'] ?>"><? echo $Artists[6][$i]['name'] ?></p>
                                    <p class="tooltip" title="<? echo $Artists[6][$i]['cname'] ?>"><? echo $Artists[6][$i]['cname'] ?></p>
                                </a>
                            </div>
                        <?
                        }
                        ?>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <? /* Misc::display_recommend($GroupID, "torrent"); */ ?>
    <div class="grid_container">
        <div class="sidebar">
            <div class="caidan artbox">
                <div class="sec-title">
                    <span><?= Lang::get('torrents', 'tag') ?></span>
                    <?
                    $DeletedTag = $Cache->get_value("deleted_tags_$GroupID" . '_' . $LoggedUser['ID']);
                    if (!empty($DeletedTag)) { ?>
                        <form style="display: none;" id="undo_tag_delete_form" name="tags" action="torrents.php" method="post">
                            <input type="hidden" name="action" value="add_tag" />
                            <input type="hidden" name="auth" value="<?= $LoggedUser['AuthKey'] ?>" />
                            <input type="hidden" name="groupid" value="<?= $GroupID ?>" />
                            <input type="hidden" name="tagname" value="<?= $DeletedTag ?>" />
                            <input type="hidden" name="undo" value="true" />
                        </form>
                        <a class="brackets" href="#" onclick="$('#undo_tag_delete_form').raw().submit(); return false;"><?= Lang::get('torrents', 'undo_delete') ?></a>

                    <?              } ?>
                </div>

                <?
                if (count($Tags) > 0) {
                ?>
                <ul class="stats nobullet tags-list cmp-list">
                    <?  foreach ($Tags as $TagKey=>$Tag) { ?>
                    <li class="list-item">
                        <a class="tags-name" href="torrents.php?taglist=<?=$Tag['name']?>"><?=display_str($Tag['name'])?></a>
                        <div class="list-actions edit_tags_votes">
                            <? if (check_perms('users_warn')) { ?>
                                <a class="list-action view-user hover-to-show brackets tooltip view_tag_user" href="user.php?id=<?=$Tag['userid']?>" title="<?=Lang::get('torrents', 'view_the_profile_of_the_user_that_added_this_tag')?>">
                                    <?=ICONS['user']?>
                                </a>
                            <? } ?>
                            <? if (empty($LoggedUser['DisableTagging']) && check_perms('site_delete_tag')) { ?>
                                <a class="list-action remove hover-to-show brackets tooltip" href="torrents.php?action=delete_tag&amp;groupid=<?=$GroupID?>&amp;tagid=<?=$Tag['id']?>&amp;auth=<?=$LoggedUser['AuthKey']?>" title="<?=Lang::get('torrents', 'remove_tag')?>">
                                    <?=ICONS['remove']?>
                                </a>
                            <? } ?>
                            <a class="list-action vote-up brackets tooltip vote_tag_up" href="torrents.php?action=vote_tag&amp;way=up&amp;groupid=<?=$GroupID?>&amp;tagid=<?=$Tag['id']?>&amp;auth=<?=$LoggedUser['AuthKey']?>" title="<?=Lang::get('torrents', 'vote_this_tag_up')?>">
                                <?=ICONS['vote-up']?>
                            </a>
                            <span class="tags-score"><?=$Tag['score']?></span>
                            <a class="list-action vote-down brackets tooltip vote_tag_down" href="torrents.php?action=vote_tag&amp;way=down&amp;groupid=<?=$GroupID?>&amp;tagid=<?=$Tag['id']?>&amp;auth=<?=$LoggedUser['AuthKey']?>" title="<?=Lang::get('torrents', 'vote_this_tag_down')?>">
                                <?=ICONS['vote-down']?>
                            </a>
                        </div>
                            </li>
                        <?
                        }
                        ?>
                    </ul>
                <?
                } else { // The "no tags to display" message was wrapped in <ul> tags to pad the text.
                ?>
                    <ul>
                        <li><?= Lang::get('torrents', 'there_are_no_tags_to_display') ?></li>
                    </ul>
                <?
                }
                ?>
            </div>
            <?
            if (empty($LoggedUser['DisableTagging'])) {
            ?>
                <div class="caidan artbox">
                    <div class="sec-title"><span><?= Lang::get('torrents', 'add_tag') ?></span></div>
                    <div class="body">
                        <form class="add_form" name="tags" action="torrents.php" method="post">
                            <input type="hidden" name="action" value="add_tag" />
                            <input type="hidden" name="auth" value="<?= $LoggedUser['AuthKey'] ?>" />
                            <input type="hidden" name="groupid" value="<?= $GroupID ?>" />
                            <input type="text" name="tagname" id="tagname" size="20" <?
                                                                                        Users::has_autocomplete_enabled('other'); ?> />
                            <input type="submit" value="+" />
                        </form>
                        <!-- <br /><br /> -->
                        <span><a href="rules.php?p=tag" class="brackets"><?= Lang::get('torrents', 'tag_rules') ?></a></span>
                    </div>
                </div>
            <?
            }
            ?>

            <?
            if ($Categories[$GroupCategoryID - 1] == 'Movies') {
                $ShownWith = false;
            ?>
                <div class="caidan artbox">
                    <div class="sec-title"><span><?= Lang::get('global', 'artist') ?></span>
                        <?= check_perms('torrents_edit') ? '<a class="edit_artists"><a onclick="ArtistManager(); return false;" href="#" class="brackets">' . Lang::get('global', 'edit') . '</a></a>' : '' ?>
                    </div>
                    <ul class="stats nobullet artists-list cmp-list" id="artist_list">
                        <?
                        if (!empty($Artists[6]) && !empty($Artists[1])) {
                            print '<li class="list-item artists_main"><strong class="artists_label">' . Lang::get('torrents', 'director') . ':</strong></li>';
                        } elseif (!empty($Artists[4]) && !empty($Artists[1])) {
                            print '<li class="list-item artists_main"><strong class="artists_label">' . Lang::get('torrents', 'performers') . ':</strong></li>';
                        }
                        foreach ($Artists[1] as $Artist) {
                        ?>
                            <li class="list-item artist_main">
                                <?= Artists::display_artist($Artist) . ' &lrm;' ?>
                                <?
                                if (check_perms('torrents_edit')) {
                                    $AliasID = $Artist['aliasid'];
                                    if (empty($AliasID)) {
                                        $AliasID = $Artist['id'];
                                    }
                                ?>
                                    (<span class="tooltip" title="<?= Lang::get('torrents', 'artist_alias_id') ?>"><?= $AliasID ?></span>)&nbsp;
                                    <div class="list-actions">
                                        <a class="list-action remove hover-to-show brackets tooltip" href="javascript:void(0);" onclick="ajax.get('torrents.php?action=delete_alias&amp;auth=' + authkey + '&amp;groupid=<?=$GroupID?>&amp;artistid=<?=$Artist['id']?>&amp;importance=1'); this.parentNode.parentNode.style.display = 'none';" title="<?=Lang::get('torrents', 'remove_artist')?>">
                                            <?=ICONS['remove']?>
                                        </a>
                                    </div>
                                <?      } ?>
                            </li>
                            <?
                        }


                        if (!empty($Artists[2]) && count($Artists[2]) > 0) {
                            print '				<li class="list-item artists_with"><strong class="artists_label">' . Lang::get('torrents', 'with') . ':</strong></li>';
                            foreach ($Artists[2] as $Artist) {
                            ?>
                                <li class="artist_guest">
                                    <?= Artists::display_artist($Artist) . ' &lrm;' ?>
                                    <? if (check_perms('torrents_edit')) {
                                        $DB->query("
					SELECT AliasID
					FROM artists_alias
					WHERE ArtistID = " . $Artist['id'] . "
						AND ArtistID != AliasID
						AND Name = '" . db_string($Artist['name']) . "'");
                                        list($AliasID) = $DB->next_record();
                                        if (empty($AliasID)) {
                                            $AliasID = $Artist['id'];
                                        }
                                    ?>
                                        (<span class="tooltip" title="<?= Lang::get('torrents', 'artist_alias_id') ?>"><?= $AliasID ?></span>)&nbsp;
                                        <div class="list-actions">
                                            <a class="list-action remove hover-to-show brackets tooltip" href="javascript:void(0);" onclick="ajax.get('torrents.php?action=delete_alias&amp;auth=' + authkey + '&amp;groupid=<?=$GroupID?>&amp;artistid=<?=$Artist['id']?>&amp;importance=2'); this.parentNode.parentNode.style.display = 'none';" title="<?=Lang::get('torrents', 'remove_artist')?>">
                                                <?=ICONS['remove']?>
                                            </a>
                                        </div>
                                    <?          } ?>
                                </li>
                            <?
                            }
                        }


                        if (!empty($Artists[3]) && count($Artists[3]) > 0) {
                            print '				<li class="list-item artists_remix"><strong class="artists_label">' . Lang::get('torrents', 'movie_producer') . ':</strong></li>';
                            foreach ($Artists[3] as $Artist) {
                            ?>
                                <li class="list-item artists_remix">
                                    <?= Artists::display_artist($Artist) . ' &lrm;' ?>
                                    <? if (check_perms('torrents_edit')) {
                                        $DB->query("
					SELECT AliasID
					FROM artists_alias
					WHERE ArtistID = " . $Artist['id'] . "
						AND ArtistID != AliasID
						AND Name = '" . db_string($Artist['name']) . "'");
                                        list($AliasID) = $DB->next_record();
                                        if (empty($AliasID)) {
                                            $AliasID = $Artist['id'];
                                        }
                                    ?>
                                        (<span class="tooltip" title="<?= Lang::get('torrents', 'artist_alias_id') ?>"><?= $AliasID ?></span>)&nbsp;
                                        <span class="list-actions">
                                            <a class="list-action remove hover-to-show brackets tooltip" href="javascript:void(0);" onclick="ajax.get('torrents.php?action=delete_alias&amp;auth=' + authkey + '&amp;groupid=<?=$GroupID?>&amp;artistid=<?=$Artist['id']?>&amp;importance=3'); this.parentNode.parentNode.style.display = 'none';" title="<?=Lang::get('torrents', 'remove_artist')?>">
                                                <?=ICONS['remove']?>
                                            </a>
                                        </span>
                                    <?          } ?>
                                </li>
                            <?
                            }
                        }
                        if (!empty($Artists[4]) && count($Artists[4]) > 0) {
                            print '<li class="list-item artists_composers"><strong class="artists_label">' . Lang::get('torrents', 'composers') . ':</strong></li>';
                            foreach ($Artists[4] as $Artist) {
                            ?>
                                <li class="list-item artists_composers">
                                    <?= Artists::display_artist($Artist) . ' &lrm;' ?>
                                    <?
                                    if (check_perms('torrents_edit')) {
                                        $DB->query("
					SELECT AliasID
					FROM artists_alias
					WHERE ArtistID = " . $Artist['id'] . "
						AND ArtistID != AliasID
						AND Name = '" . db_string($Artist['name']) . "'");
                                        list($AliasID) = $DB->next_record();
                                        if (empty($AliasID)) {
                                            $AliasID = $Artist['id'];
                                        }
                                    ?>
                                        (<span class="tooltip" title="<?= Lang::get('torrents', 'artist_alias_id') ?>"><?= $AliasID ?></span>)&nbsp;
                                        <span class="list-actions">
                                            <a class="list-action remove hover-to-show brackets tooltip" href="javascript:void(0);" onclick="ajax.get('torrents.php?action=delete_alias&amp;auth=' + authkey + '&amp;groupid=<?=$GroupID?>&amp;artistid=<?=$Artist['id']?>&amp;importance=4'); this.parentNode.parentNode.style.display = 'none';" title="<?=Lang::get('torrents', 'remove_artist')?>">
                                                <?=ICONS['remove']?>
                                            </a>
                                        </span>
                                    <?          } ?>
                                </li>
                            <?
                            }
                        }
                        if (!empty($Artists[5]) && count($Artists[5]) > 0) {
                            print '<li class="list-item artists_conductors"><strong class="artists_label">' . Lang::get('torrents', 'cinematographer') . ':</strong></li>';
                            foreach ($Artists[5] as $Artist) {
                            ?>
                                <li class="list-item artists_conductors">
                                    <?= Artists::display_artist($Artist) . ' &lrm;' ?>
                                    <? if (check_perms('torrents_edit')) {
                                        $DB->query("
					SELECT AliasID
					FROM artists_alias
					WHERE ArtistID = " . $Artist['id'] . "
						AND ArtistID != AliasID
						AND Name = '" . db_string($Artist['name']) . "'");
                                        list($AliasID) = $DB->next_record();
                                        if (empty($AliasID)) {
                                            $AliasID = $Artist['id'];
                                        }
                                    ?>
                                        (<span class="tooltip" title="<?= Lang::get('torrents', 'artist_alias_id') ?>"><?= $AliasID ?></span>)&nbsp;
                                        <span class="list-actions">
                                            <a class="list-action remove hover-to-show brackets tooltip" href="javascript:void(0);" onclick="ajax.get('torrents.php?action=delete_alias&amp;auth=' + authkey + '&amp;groupid=<?=$GroupID?>&amp;artistid=<?=$Artist['id']?>&amp;importance=5'); this.parentNode.parentNode.style.display = 'none';" title="<?=Lang::get('torrents', 'remove_conductor')?>">
                                                <?=ICONS['remove']?>
                                            </a>
                                        </span>
                                    <?          } ?>
                                </li>
                            <?
                            }
                        }
                        if (!empty($Artists[6]) && count($Artists[6]) > 0) {
                            print '<li class="list-item artists_dj"><strong class="artists_label">' . Lang::get('torrents', 'actor') . ':</strong></li>';
                            foreach ($Artists[6] as $Artist) {
                            ?>
                                <li class="list-item artists_dj">
                                    <?= Artists::display_artist($Artist) . ' &lrm;' ?>
                                    <? if (check_perms('torrents_edit')) {
                                        $DB->query("
					SELECT AliasID
					FROM artists_alias
					WHERE ArtistID = " . $Artist['id'] . "
						AND ArtistID != AliasID
						AND Name = '" . db_string($Artist['name']) . "'");
                                        list($AliasID) = $DB->next_record();
                                        if (empty($AliasID)) {
                                            $AliasID = $Artist['id'];
                                        }
                                    ?>
                                        (<span class="tooltip" title="<?= Lang::get('torrents', 'artist_alias_id') ?>"><?= $AliasID ?></span>)&nbsp;
                                        <span class="list-actions">
                                            <a class="list-action remove hover-to-show brackets tooltip" href="javascript:void(0);" onclick="ajax.get('torrents.php?action=delete_alias&amp;auth=' + authkey + '&amp;groupid=<?=$GroupID?>&amp;artistid=<?=$Artist['id']?>&amp;importance=6'); this.parentNode.parentNode.style.display = 'none';" title="<?=Lang::get('torrents', 'remove_artist')?>">
                                                <?=ICONS['remove']?>
                                            </a>
                                        </span>
                                    <?          } ?>
                                </li>
                            <?
                            }
                        }
                        if (!empty($Artists[7]) && count($Artists[7]) > 0) {
                            print '				<li class="list-item artists_producer"><strong class="artists_label">' . Lang::get('torrents', 'produced_by') . ':</strong></li>';
                            foreach ($Artists[7] as $Artist) {
                            ?>
                                <li class="list-item artists_producer">
                                    <?= Artists::display_artist($Artist) . ' &lrm;' ?>
                                    <?
                                    if (check_perms('torrents_edit')) {
                                        $DB->query("
					SELECT AliasID
					FROM artists_alias
					WHERE ArtistID = " . $Artist['id'] . "
						AND ArtistID != AliasID
						AND Name = '" . db_string($Arstist['name']) . "'");
                                        list($AliasID) = $DB->next_record();
                                        if (empty($AliasID)) {
                                            $AliasID = $Artist['id'];
                                        }
                                    ?>
                                        (<span class="tooltip" title="<?= Lang::get('torrents', 'artist_alias_id') ?>"><?= $AliasID ?></span>)&nbsp;
                                        <span class="list-actions">
                                            <a class="list-action remove hover-to-show brackets tooltip" href="javascript:void(0);" onclick="ajax.get('torrents.php?action=delete_alias&amp;auth=' + authkey + '&amp;groupid=<?=$GroupID?>&amp;artistid=<?=$Artist['id']?>&amp;importance=7'); this.parentNode.parentNode.style.display = 'none';" title="<?=Lang::get('torrents', 'remove_artist')?>">
                                                <?=ICONS['remove']?>
                                            </a>
                                        </span>
                                    <?          } ?>
                                </li>
                        <?
                            }
                        }
                        ?>
                    </ul>
                </div>
                <? if (check_perms('torrents_add_artist')) { ?>
                    <div class="caidan artbox">
                        <div class="sec-title"><span><?= Lang::get('torrents', 'add_artist') ?></span><a class="additional_add_artist"><a onclick="AddArtistField(); return false;" href="#" class="brackets">+</a></a></div>
                        <div class="body">
                            <form class="add_form" name="artists" action="torrents.php" method="post">
                                <div id="AddArtists">
                                    <input type="hidden" name="action" value="add_alias" />
                                    <input type="hidden" name="auth" value="<?= $LoggedUser['AuthKey'] ?>" />
                                    <input type="hidden" name="groupid" value="<?= $GroupID ?>" />
                                    <input type="text" id="artist" name="aliasname[]" size="17" <?
                                                                                                Users::has_autocomplete_enabled('other'); ?> />
                                    <select name="importance[]">
                                        <option value="1"><?= Lang::get('torrents', 'director') ?></option>
                                        <option value="2"><?= Lang::get('torrents', 'writer') ?></option>
                                        <option value="4"><?= Lang::get('torrents', 'movie_producer') ?></option>
                                        <option value="5"><?= Lang::get('torrents', 'composer') ?></option>
                                        <option value="6"><?= Lang::get('torrents', 'cinematographer') ?></option>
                                        <option value="3"><?= Lang::get('torrents', 'actor') ?></option>
                                    </select>
                                </div>
                                <input type="submit" value="Add" />
                            </form>
                        </div>
                    </div>
            <?
                }
            }
            if (ENABLE_COLLAGES) {
                include(SERVER_ROOT . '/sections/torrents/collage.php');
            }
            include(SERVER_ROOT . '/sections/torrents/vote_ranks.php');
            include(SERVER_ROOT . '/sections/torrents/vote.php');
            ?>
        </div>
        <?
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


        if ($CheckAllTorrents || $CheckSelfTorrents) {
        ?>
            <script>
                function torrent_check(event) {
                    var id = event.data.id,
                        checked = event.data.checked
                    $.get("torrents.php", {
                            action: "torrent_check",
                            torrentid: id,
                            checked: checked
                        },
                        function(data) {
                            var obj = eval("(" + data + ")");
                            if (obj.ret == "success") {
                                if (checked == 1) {
                                    $('#torrent' + id + '_check1').show()
                                    $('#slot-torrent' + id + '_check1').show()
                                    $('#torrent' + id + '_check0').hide()
                                    $('#slot-torrent' + id + '_check0').hide()
                                } else {
                                    $('#torrent' + id + '_check0').show()
                                    $('#slot-torrent' + id + '_check0').show()
                                    $('#torrent' + id + '_check1').hide()
                                    $('#slot-torrent' + id + '_check1').hide()
                                }
                            } else {
                                alert('失败');
                            }
                        });
                }
            </script>
        <? } ?>
        <div class="main_column">

            <table class="cmp-torrent-table torrent_table has-slots cmp-table-tab show details m_table" style="<?= $View == 'slot' ? "display:none" : "" ?>" id="torrent_details">
                <tr class="colhead_dark">
                    <td colspan="2" class="m_th_left" width="80%">
                        <span>
                            <?= Lang::get('global', 'torrents') ?>
                            <span> | <span>
                                    <a href='#' onclick='torrentTable.toggleTab(event, ".cmp-slot-table")'><?= Lang::get('torrents', 'slot_table') ?></a>
                                </span>
                    </td>
                    <td class="number_column">
                        <span class="tooltip" aria-hidden="true" title="<?=Lang::get('global', 'size')?>">
                            <?=ICONS['torrent-size']?>
                        </span>
                    </td>
                    <td class="m_th_right sign snatches">
                        <span class="tooltip" aria-hidden="true" title="<?=Lang::get('global', 'snatched')?>">
                            <?=ICONS['torrent-snatches']?>
                        </span>
                    </td>
                    <td class="m_th_right sign seeders">
                        <i class="tooltip" aria-hidden="true" title="<?=Lang::get('global', 'seeders')?>">
                            <?=ICONS['torrent-seeders']?>
                        </i>
                    </td>
                    <td class="m_th_right sign leechers">
                        <i class="tooltip" aria-hidden="true" title="<?=Lang::get('global', 'leechers')?>">
                            <?=ICONS['torrent-leechers']?>
                        </i>
                    </td>
                </tr>
                <tr id="slot_filter_container">
                    <td class="slot-filters" colspan="6">
                        <div id="slot_filter" class="center">
                            <a href="#" class="slot_filter_button tooltip" onclick='torrentTable.filterSlot(event, ["quality", "en_quality", "cn_quality", "feature"])' title="<?= Lang::get('torrents', 'all_quality_slot') ?>" id="slot_filter_all_quality_slot" data-slot="quality"><i class="fas fa-thumbs-up"></i></a>
                            <a href="#" class="slot_filter_button tooltip" onclick='torrentTable.filterSlot(event, ["cn_quality"])' title="<?= Lang::get('torrents', 'cn_quality_slot') ?>" id="slot_filter_cn_quality_slot" data-slot="cn_quality"><i class="fas fa-copyright"></i></a>
                            <a href="#" class="slot_filter_button tooltip" onclick='torrentTable.filterSlot(event, ["en_quality"])' title="<?= Lang::get('torrents', 'en_quality_slot') ?>" id="slot_filter_en_quality_slot" data-slot="en_quality"><i class="fas fa-flag-usa"></i></a>
                            <a href="#" class="slot_filter_button tooltip" onclick='torrentTable.filterSlot(event, ["retention"])' title="<?= Lang::get('torrents', 'retention_slot') ?>" id="slot_filter_retention_slot" data-slot="retention"><i class="fas fa-archive"></i></a>
                            <a href="#" class="slot_filter_button tooltip" onclick='torrentTable.filterSlot(event, ["feature"])' title="<?= Lang::get('torrents', 'feature_slot') ?>" id="slot_filter_feature_slot" data-slot="feature"><i class="fas fa-star-half-alt"></i></a>
                            <a href="#" class="slot_filter_button tooltip" onclick='torrentTable.filterSlot(event, ["remux"])' title="<?= Lang::get('torrents', 'remux_slot') ?>" id="slot_filter_remux_slot" data-slot="remux"><i class="fas fa-circle-notch"></i></a>
                            <a href="#" class="slot_filter_button tooltip" onclick='torrentTable.filterSlot(event, ["untouched"])' title="<?= Lang::get('torrents', 'untouched_slot') ?>" id="slot_filter_untouched_slot" data-slot="untouched"><i class="fas fa-dot-circle"></i></a>
                            <a href="#" class="slot_filter_button tooltip" onclick='torrentTable.filterSlot(event, ["diy"])' title="<?= Lang::get('torrents', 'diy_slot') ?>" id="slot_filter_diy_slot" data-slot="diy"><i class="far fa-dot-circle"></i></a>
                            <a href="#" class="slot_filter_button type-clear tooltip" onclick='torrentTable.filterSlot(event, [])' style="visibility: hidden;" title="<?= Lang::get('torrents', 'clear_slot') ?>" id="slot_filter_clear_slot"><i class="far fa-times-circle"></i></a>
                        </div>
                    </td>
                </tr>
                <?


                include(Lang::getLangfilePath("report_types"));
                print_group($LoggedUser, $GroupID, $GroupName, $GroupCategoryID, $ReleaseType, $TorrentList, $Types);
                ?>
            </table>
            <form id="slot" class="cmp-slot-table cmp-table-tab" style="<?= $View == 'slot' ? "" : "display: none" ?>" method="post">
                <input type="hidden" name="action" value="takeeditslot" />
                <input type="hidden" name="auth" value="<?= $LoggedUser['AuthKey'] ?>" />
                <input type="hidden" name="groupid" value="<?= $GroupID ?>" />
                <table class="torrent_table large-head cmp-torrent-table">
                    <tr class="colhead_dark">
                        <td colspan="2" class="m_th_left">
                            <span><a href="#" onclick="torrentTable.toggleTab(event, '.cmp-torrent-table')"><?= Lang::get('global', 'torrents') ?></a> | <?= Lang::get('torrents', 'slot_table') ?></span>
                            <a class="tooltip" href="wiki.php?action=article&id=66" title="<?= Lang::get('torrents', 'slot_wiki') ?>">[?]</a>
                        </td>
                        <td class="number_column"><?= Lang::get('global', 'size') ?></td>
                        <td style="width: 205px;">
                            <span><?= Lang::get('torrents', 'slot_action') ?></span>
                        </td>
                    </tr>
                    <? print_slot_group($LoggedUser, $GroupID, $GroupName, $GroupCategoryID, $ReleaseType, $TorrentList, $Types); ?>

                    <?
                    if (check_perms("torrents_slot_edit")) {
                    ?>
                        <tr class="submit_tr">
                            <td colspan="4" class="center no_padding"><input type="submit" /></td>
                        </tr>
                    <?
                    }
                    ?>
                </table>
            </form>
            <?

            $Requests = get_group_requests($GroupID);
            if (empty($LoggedUser['DisableRequests']) && count($Requests) > 0) {
                $i = 0;
            ?>
                <div class="box requests_list">
                    <div class="head">
                        <span><?= Lang::get('global', 'requests') ?> (<?= number_format(count($Requests)) ?>)</span>
                        <a href="#" style="float: right;" onclick="$('#requests').gtoggle(); this.innerHTML = (this.innerHTML == 'Hide' ? 'Show' : 'Hide'); return false;" class="brackets"><?= Lang::get('global', 'show') ?></a>
                    </div>
                    <table id="requests" class="request_table hidden">
                        <tr class="colhead">
                            <td><?= Lang::get('torrents', 'format_bitrate_media') ?></td>
                            <td><?= Lang::get('torrents', 'votes') ?></td>
                            <td><?= Lang::get('torrents', 'bounty') ?></td>
                        </tr>
                        <? foreach ($Requests as $Request) {
                            $RequestVotes = Requests::get_votes_array($Request['ID']);

                            $CodecString = implode(', ', explode('|', $Request['CodecList']));
                            $SourceString = implode(', ', explode('|', $Request['SourceList']));
                            $ContainerString = implode(', ', explode('|', $Request['ContainerList']));
                            $ResolutionString = implode(', ', explode('|', $Request['ResolutionList']));

                        ?>
                            <tr class="requestrows <?= (++$i % 2 ? 'rowa' : 'rowb') ?>">
                                <td><a href="requests.php?action=view&amp;id=<?= $Request['ID'] ?>"><?= $CodecString ?> /
                                        <?= $SourceString ?> / <?= $ResolutionString ?> / <?= $ContainerString ?></a></td>
                                <td>
                                    <span id="vote_count_<?= $Request['ID'] ?>"><?= count($RequestVotes['Voters']) ?></span>
                                    <? if (check_perms('site_vote')) { ?>
                                        &nbsp;&nbsp; <a href="javascript:Vote(0, <?= $Request['ID'] ?>)" class="brackets">+</a>
                                    <?          } ?>
                                </td>
                                <td><?= Format::get_size($RequestVotes['TotalBounty']) ?></td>
                            </tr>
                        <?  } ?>
                    </table>
                </div>
            <?
            }
            $Collages = $Cache->get_value("torrent_collages_$GroupID");
            if (!is_array($Collages)) {
                $DB->query("
		SELECT c.Name, c.NumTorrents, c.ID
		FROM collages AS c
			JOIN collages_torrents AS ct ON ct.CollageID = c.ID
		WHERE ct.GroupID = '$GroupID'
			AND Deleted = '0'
			AND CategoryID != '0'");
                $Collages = $DB->to_array();
                $Cache->cache_value("torrent_collages_$GroupID", $Collages, 600 * 6);
            }
            if (count($Collages) > 0) {
                if (count($Collages) > MAX_COLLAGES) {
                    // Pick some at random
                    $Range = range(0, count($Collages) - 1);
                    shuffle($Range);
                    $Indices = array_slice($Range, 0, MAX_COLLAGES);
                    $SeeAll = ' <a href="#" onclick="$(\'.collage_rows\').gtoggle(); return false;">(See all)</a>';
                } else {
                    $Indices = range(0, count($Collages) - 1);
                    $SeeAll = '';
                }
            ?>
                <table class="collage_table" id="collages">
                    <tr class="colhead">
                        <td width="85%"><a href="#">&uarr;</a>&nbsp;<?= Lang::get('torrents', 'this_album_is_in_collages_1') ?>
                            <?= number_format(count($Collages)) ?>
                            <?= Lang::get('torrents', 'this_album_is_in_collages_2') ?><?= ((count($Collages) > 1) ? Lang::get('torrents', 'this_album_is_in_collages_3') : '') ?><?= $SeeAll ?>
                        </td>
                        <td><?= Lang::get('torrents', 'torrents_count') ?></td>
                    </tr>
                    <? foreach ($Indices as $i) {
                        list($CollageName, $CollageTorrents, $CollageID) = $Collages[$i];
                        unset($Collages[$i]);
                    ?>
                        <tr>
                            <td><a href="collages.php?id=<?= $CollageID ?>"><?= $CollageName ?></a></td>
                            <td class="number_column"><?= number_format($CollageTorrents) ?></td>
                        </tr>
                    <?  }
                    foreach ($Collages as $Collage) {
                        list($CollageName, $CollageTorrents, $CollageID) = $Collage;
                    ?>
                        <tr class="collage_rows hidden">
                            <td><a href="collages.php?id=<?= $CollageID ?>"><?= $CollageName ?></a></td>
                            <td class="number_column"><?= number_format($CollageTorrents) ?></td>
                        </tr>
                    <?  } ?>
                </table>
            <?
            }

            $PersonalCollages = $Cache->get_value("torrent_collages_personal_$GroupID");
            if (!is_array($PersonalCollages)) {
                $DB->query("
		SELECT c.Name, c.NumTorrents, c.ID
		FROM collages AS c
			JOIN collages_torrents AS ct ON ct.CollageID = c.ID
		WHERE ct.GroupID = '$GroupID'
			AND Deleted = '0'
			AND CategoryID = '0'");
                $PersonalCollages = $DB->to_array(false, MYSQLI_NUM);
                $Cache->cache_value("torrent_collages_personal_$GroupID", $PersonalCollages, 600 * 6);
            }


            if (count($PersonalCollages) > 0) {
                if (count($PersonalCollages) > MAX_PERS_COLLAGES) {
                    // Pick some at random
                    $Range = range(0, count($PersonalCollages) - 1);
                    shuffle($Range);
                    $Indices = array_slice($Range, 0, MAX_PERS_COLLAGES);
                    $SeeAll = ' <a href="#" onclick="$(\'.personal_rows\').gtoggle(); return false;">(See all)</a>';
                } else {
                    $Indices = range(0, count($PersonalCollages) - 1);
                    $SeeAll = '';
                }
            ?>
                <table class="collage_table" id="personal_collages">
                    <tr class="colhead">
                        <td width="85%"><a href="#">&uarr;</a>&nbsp;<?= Lang::get('torrents', 'this_album_is_in_personal_collages_1') ?>
                            <?= number_format(count($PersonalCollages)) ?>
                            <?= Lang::get('torrents', 'this_album_is_in_personal_collages_2') ?><?= ((count($PersonalCollages) > 1) ? Lang::get('torrents', 'this_album_is_in_personal_collages_3') : '') ?><?= $SeeAll ?>
                        </td>
                        <td><?= Lang::get('torrents', 'torrents_count') ?></td>
                    </tr>
                    <? foreach ($Indices as $i) {
                        list($CollageName, $CollageTorrents, $CollageID) = $PersonalCollages[$i];
                        unset($PersonalCollages[$i]);
                    ?>
                        <tr>
                            <td><a href="collages.php?id=<?= $CollageID ?>"><?= $CollageName ?></a></td>
                            <td class="number_column"><?= number_format($CollageTorrents) ?></td>
                        </tr>
                    <?  }
                    foreach ($PersonalCollages as $Collage) {
                        list($CollageName, $CollageTorrents, $CollageID) = $Collage;
                    ?>
                        <tr class="personal_rows hidden">
                            <td><a href="collages.php?id=<?= $CollageID ?>"><?= $CollageName ?></a></td>
                            <td class="number_column"><?= number_format($CollageTorrents) ?></td>
                        </tr>
                    <?  } ?>
                </table>
            <?
            }
            // Matched Votes
            include(SERVER_ROOT . '/sections/torrents/voter_picks.php');
            ?>
            <!-- <div class="box torrent_description">
            <div class="head"><a href="#">&uarr;</a>&nbsp;<strong><?= (!empty($ReleaseType) ? Lang::get('torrents', 'release_types')[$ReleaseType] . Lang::get('torrents', 'space_info') : Lang::get('torrents', 'info')) ?></strong></div>
            <div class="body"><? if ($WikiBody != '') {
                                    echo $WikiBody;
                                } else {
                                    echo Lang::get('torrents', 'there_is_no_information_on_this_torrent');
                                } ?></div>
        </div> -->
            <?
            // --- Comments ---
            $Pages = Format::get_pages($Page, $NumComments, TORRENT_COMMENTS_PER_PAGE, 9, '#comments');
            ?>
            <div id="torrent_comments">
                <div class="linkbox"><a name="comments"></a>
                    <?= $Pages ?>
                </div>
                <?
                CommentsView::render_comments($Thread, $LastRead, "torrents.php?id=$GroupID");
                ?>
                <div class="linkbox">
                    <?= $Pages ?>
                </div>
                <?
                View::parse('generic/reply/quickreply.php', array(
                    'InputName' => 'pageid',
                    'InputID' => $GroupID,
                    'Action' => 'comments.php?page=torrents',
                    'InputAction' => 'take_post',
                    'TextareaCols' => 65,
                    'SubscribeBox' => true
                ));
                ?>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(() => {
        function isOverflown(ele) {
            return ele.scrollHeight > ele.clientHeight
        }

        var synopsisContent = $("#synopsis").tooltipster('content');

        function synopsisToggle() {
            if (isOverflown($('#synopsis > p')[0])) {
                $('#synopsis').addClass('overflown')
                $("#synopsis").tooltipster('content', synopsisContent);
            } else {
                $('#synopsis').removeClass('overflown')
                $("#synopsis").tooltipster('content', null);
            }
        }

        synopsisToggle()

        $(window).resize(synopsisToggle)

        $('#synopsis').click(() => {
            $('#synopsis').toggleClass('expand')
        })
    });
</script>
<?
View::show_footer([], 'torrents/index');
