<?php

/*
 * This is the page that displays the request to the end user after being created.
 */
if (empty($_GET['id']) || !is_number($_GET['id'])) {
    error(0);
}

$RequestID = $_GET['id'];
$RequestTaxPercent = ($RequestTax * 100);

//First things first, lets get the data for the request.

$Request = Requests::get_request($RequestID);
if ($Request === false) {
    error(404);
}

//Convenience variables
$IsFilled = !empty($Request['TorrentID']);
$CanVote = !$IsFilled && check_perms('site_vote');

if ($Request['CategoryID'] === '0') {
    $CategoryName = 'Unknown';
} else {
    $CategoryName = $Categories[$Request['CategoryID'] - 1];
}

//Do we need to get artists?
if ($CategoryName === 'Movies') {
    $ArtistForm = Requests::get_artists($RequestID);
    $ArtistName = Artists::display_artists($ArtistForm, false, true);
    $ArtistLink = Artists::display_artists($ArtistForm, true, false);
    $RequestGroupName = Torrents::torrent_group_name($Request, true);

    if ($IsFilled) {
        $DisplayLink = "<a href=\"torrents.php?torrentid=$Request[TorrentID]\" dir=\"ltr\">$RequestGroupName</a>";
    } else {
        $DisplayLink = '<span dir="ltr">' . $RequestGroupName . "</span>";
    }
    $FullName = $RequestGroupName;

    $CodecString = implode(', ', explode('|', $Request['CodecList']));
    $ResolutionString = implode(', ', explode('|', $Request['ResolutionList']));
    $ContainerString = implode(', ', explode('|', $Request['ContainerList']));
    $SourceString =  implode(', ', explode('|', $Request['SourceList']));

    if (empty($Request['ReleaseType'])) {
        $ReleaseName = 'Unknown';
    } else {
        $ReleaseName = Lang::get('torrents', 'release_types')[$Request['ReleaseType']];
    }
}

//Votes time
$RequestVotes = Requests::get_votes_array($RequestID);
$VoteCount = count($RequestVotes['Voters']);
$ProjectCanEdit = (check_perms('project_team') && !$IsFilled && ($Request['CategoryID'] === '0' || ($CategoryName === 'Music' && $Request['Year'] === '0')));
$UserCanEdit = (!$IsFilled && $LoggedUser['ID'] === $Request['UserID'] && $VoteCount < 2);
$CanEdit = ($UserCanEdit || $ProjectCanEdit || check_perms('site_moderate_requests'));

// Comments (must be loaded before View::show_header so that subscriptions and quote notifications are handled properly)
list($NumComments, $Page, $Thread, $LastRead) = Comments::load('requests', $RequestID);

View::show_header(Lang::get('requests', 'view_request') . ": $FullName", 'comments,requests,bbcode,subscriptions');

?>
<div class="thin">
    <div class="header">
        <!-- <h2><a href="requests.php"><?= Lang::get('global', 'requests') ?></a> &gt; <?= $CategoryName ?> &gt; <?= $DisplayLink ?></h2> -->
        <h2><a href="requests.php"><?= Lang::get('global', 'requests') ?></a> &gt; <?= $DisplayLink ?></h2>
        <div class="linkbox">
            <? if ($CanEdit) { ?>
                <a href="requests.php?action=edit&amp;id=<?= $RequestID ?>" class="brackets"><?= Lang::get('global', 'edit') ?></a>
            <?  }
            if ($UserCanEdit || check_perms('users_mod')) { ?>
                <a href="requests.php?action=delete&amp;id=<?= $RequestID ?>" class="brackets"><?= Lang::get('global', 'delete') ?></a>
            <?  }
            if (Bookmarks::has_bookmarked('request', $RequestID)) { ?>
                <a href="#" id="bookmarklink_request_<?= $RequestID ?>" onclick="Unbookmark('request', <?= $RequestID ?>, <?= Lang::get('global', 'add_bookmark') ?>); return false;" class="brackets"><?= Lang::get('global', 'remove_bookmark') ?></a>
            <?  } else { ?>
                <a href="#" id="bookmarklink_request_<?= $RequestID ?>" onclick="Bookmark('request', <?= $RequestID ?>, <?= Lang::get('global', 'remove_bookmark') ?>); return false;" class="brackets"><?= Lang::get('global', 'add_bookmark') ?></a>
            <?  } ?>
            <a href="#" id="subscribelink_requests<?= $RequestID ?>" class="brackets" onclick="SubscribeComments('requests',<?= $RequestID ?>);return false;"><?= Subscriptions::has_subscribed_comments('requests', $RequestID) !== false ? Lang::get('global', 'unsubscribe') : Lang::get('global', 'subscribe') ?></a>
            <a href="reports.php?action=report&amp;type=request&amp;id=<?= $RequestID ?>" class="brackets"><?= Lang::get('requests', 'report_request') ?></a>
            <? if (!$IsFilled) { ?>
                <a href="upload.php?requestid=<?= $RequestID ?><?= ($Request['GroupID'] ? "&amp;groupid=$Request[GroupID]" : '') ?>" class="brackets"><?= Lang::get('requests', 'upload_request') ?></a>
            <?  }
            if (!$IsFilled && ($Request['CategoryID'] === '0' || ($CategoryName === 'Music' && $Request['Year'] === '0'))) { ?>
                <a href="reports.php?action=report&amp;type=request_update&amp;id=<?= $RequestID ?>" class="brackets"><?= Lang::get('requests', 'request_update') ?></a>
            <? } ?>

            <?
            // Create a search URL to WorldCat and Google based on title
            $encoded_title = urlencode(preg_replace("/\([^\)]+\)/", '', $Request['Title']));
            $encoded_artist = substr(str_replace('&amp;', 'and', $ArtistName), 0, -3);
            $encoded_artist = str_ireplace('Directed By', '', $encoded_artist);
            $encoded_artist = preg_replace("/\([^\)]+\)/", '', $encoded_artist);
            $encoded_artist = urlencode($encoded_artist);

            $google_url  = "https://www.blu-ray.com/search/?quicksearch=1&quicksearch_country=all&section=bluraymovies&quicksearch_keyword=" . "$encoded_title";
            ?>
            <a href="<? echo $google_url; ?>" class="brackets"><?= Lang::get('requests', 'find_in_stores') ?></a>
        </div>
    </div>
    <div class="grid_container">
        <div class="sidebar">
            <? if ($Request['CategoryID'] !== '0') { ?>
                <div class="box box_image box_image_albumart box_albumart">
                    <!-- .box_albumart deprecated -->
                    <div class="head"><strong><?= Lang::get('requests', 'cover') ?></strong></div>
                    <div id="covers">
                        <div class="pad">
                            <?
                            if (!empty($Request['Image'])) {
                            ?>
                                <p align="center"><img style="width: 100%;" src="<?= ImageTools::process($Request['Image'], true) ?>" alt="<?= $FullName ?>" onclick="lightbox.init(this, 220);" /></p>
                            <?      } else { ?>
                                <p align="center"><img style="width: 100%;" src="<?= STATIC_SERVER ?>common/noartwork/<?= $CategoryIcons[$Request['CategoryID'] - 1] ?>" alt="<?= $CategoryName ?>" class="tooltip" title="<?= $CategoryName ?>" height="220" border="0" /></p>
                            <?      } ?>
                        </div>
                    </div>
                </div>
            <?
            }
            if ($CategoryName === 'Movies') { ?>
                <div class="box box_artists">
                    <div class="head"><strong><?= Lang::get('global', 'artists') ?></strong></div>
                    <ul class="stats nobullet">
                        <?
                        foreach ($ArtistForm[1] as $Artist) {
                        ?>
                            <li class="artists_main">
                                <?= Artists::display_artist($Artist) ?>
                            </li>
                        <?
                        }
                        ?>
                    </ul>
                </div>
            <?  } ?>
            <div class="box box_tags">
                <div class="head"><strong><?= Lang::get('requests', 'tags') ?></strong></div>
                <ul class="stats nobullet">
                    <? foreach ($Request['Tags'] as $TagID => $TagName) { ?>
                        <li>
                            <a href="torrents.php?taglist=<?= $TagName ?>"><?= display_str($TagName) ?></a>
                            <br style="clear: both;" />
                        </li>
                    <?  } ?>
                </ul>
            </div>
            <div class="box box_votes">
                <div class="head"><strong><?= Lang::get('requests', 'top_contributors') ?></strong></div>
                <table class="layout" id="request_top_contrib">
                    <?
                    $VoteMax = ($VoteCount < 5 ? $VoteCount : 5);
                    $ViewerVote = false;
                    for ($i = 0; $i < $VoteMax; $i++) {
                        $User = array_shift($RequestVotes['Voters']);
                        $Boldify = false;
                        if ($User['UserID'] === $LoggedUser['ID']) {
                            $ViewerVote = true;
                            $Boldify = true;
                        }
                    ?>
                        <tr>
                            <td>
                                <a href="user.php?id=<?= $User['UserID'] ?>"><?= ($Boldify ? '<strong>' : '') . display_str($User['Username']) . ($Boldify ? '</strong>' : '') ?></a>
                            </td>
                            <td class="number_column">
                                <?= ($Boldify ? '<strong>' : '') . Format::get_size($User['Bounty']) . ($Boldify ? "</strong>\n" : "\n") ?>
                            </td>
                        </tr>
                        <?  }
                    reset($RequestVotes['Voters']);
                    if (!$ViewerVote) {
                        foreach ($RequestVotes['Voters'] as $User) {
                            if ($User['UserID'] === $LoggedUser['ID']) { ?>
                                <tr>
                                    <td>
                                        <a href="user.php?id=<?= $User['UserID'] ?>"><strong><?= display_str($User['Username']) ?></strong></a>
                                    </td>
                                    <td class="number_column">
                                        <strong><?= Format::get_size($User['Bounty']) ?></strong>
                                    </td>
                                </tr>
                    <?          }
                        }
                    }
                    ?>
                </table>
            </div>
        </div>
        <div class="main_column">
            <table class="layout requests__content">
                <tr>
                    <td class="label"><?= Lang::get('requests', 'created') ?></td>
                    <td>
                        <?= time_diff($Request['TimeAdded']) ?><?= Lang::get('requests', 'created_by') ?>
                        <strong><?= Users::format_username($Request['UserID'], false, false, false) ?></strong>
                    </td>
                </tr>
                <tr>
                    <td class="label"><?= Lang::get('upload', 'movie_imdb') ?></td>
                    <td><a target="_blank" href="<?= "https://www.imdb.com/title/" . $Request['IMDBID'] ?>"><?= $Request['IMDBID'] ?></a></td>
                </tr>
                <tr>
                    <td class="label"><?= Lang::get('upload', 'movie_type') ?></td>
                    <td><?= $ReleaseName ?></td>
                </tr>
                <tr>
                    <td class="label"><?= Lang::get('requests', 'acceptable_codecs') ?></td>
                    <td><?= $CodecString ?></td>
                </tr>
                <tr>
                    <td class="label"><?= Lang::get('requests', 'acceptable_containers') ?></td>
                    <td><?= $ContainerString ?></td>
                </tr>
                <tr>
                    <td class="label"><?= Lang::get('requests', 'acceptable_resolutions') ?></td>
                    <td><?= $ResolutionString ?></td>
                </tr>
                <tr>
                    <td class="label"><?= Lang::get('requests', 'acceptable_sources') ?></td>
                    <td><?= $SourceString ?></td>
                </tr>
                <? if ($CategoryName === 'Music') {
                    if (!empty($Request['RecordLabel'])) { ?>
                        <tr>
                            <td class="label"><?= Lang::get('requests', 'record_label') ?></td>
                            <td><?= $Request['RecordLabel'] ?></td>
                        </tr>
                    <?      }
                    if (!empty($Request['CatalogueNumber'])) { ?>
                        <tr>
                            <td class="label"><?= Lang::get('requests', 'catalogue_number') ?></td>
                            <td><?= $Request['CatalogueNumber'] ?></td>
                        </tr>
                    <?      } ?>
                    <tr>
                        <td class="label"><?= Lang::get('requests', 'release_type') ?></td>
                        <td><?= $ReleaseName ?></td>
                    </tr>
                    <tr>
                        <td class="label"><?= Lang::get('requests', 'acceptable_bitrates') ?></td>
                        <td><?= $BitrateString ?></td>
                    </tr>
                    <tr>
                        <td class="label"><?= Lang::get('requests', 'acceptable_formats') ?></td>
                        <td><?= $FormatString ?></td>
                    </tr>
                    <tr>
                        <td class="label"><?= Lang::get('requests', 'acceptable_media') ?></td>
                        <td><?= $MediaString ?></td>
                    </tr>
                    <? if (!empty($Request['LogCue'])) { ?>
                        <tr>
                            <td class="label"><?= Lang::get('requests', 'required_cd_flac_only_extras') ?></td>
                            <td><?= $Request['LogCue'] ?></td>
                        </tr>
                        <!-- <tr>
                <td class="label"><?= Lang::get('requests', 'required_cd_flac_checksum') ?></td>
                <td><?= $Request['LogCue'] ? Lang::get('requests', 'yes') : Lang::get('requests', 'no') ?></td>
            </tr> -->
                    <?
                    }
                }
                $Worldcat = '';
                $OCLC = str_replace(' ', '', $Request['OCLC']);
                if ($OCLC !== '') {
                    $OCLCs = explode(',', $OCLC);
                    for ($i = 0; $i < count($OCLCs); $i++) {
                        if (!empty($Worldcat)) {
                            $Worldcat .= ', <a href="https://www.worldcat.org/oclc/' . $OCLCs[$i] . '">' . $OCLCs[$i] . '</a>';
                        } else {
                            $Worldcat = '<a href="https://www.worldcat.org/oclc/' . $OCLCs[$i] . '">' . $OCLCs[$i] . '</a>';
                        }
                    }
                }
                if (!empty($Worldcat)) {
                    ?>
                    <tr>
                        <td class="label"><?= Lang::get('requests', 'oclc_id') ?></td>
                        <td><?= $Worldcat ?></td>
                    </tr>
                <?
                }
                if ($Request['GroupID']) {
                ?>
                    <tr>
                        <td class="label"><?= Lang::get('requests', 'torrent_group') ?></td>
                        <td><a href="torrents.php?id=<?= $Request['GroupID'] ?>">torrents.php?id=<?= $Request['GroupID'] ?></a></td>
                    </tr>
                <?
                }
                if ($Request['SourceTorrent']) {
                ?>
                    <tr>
                        <td class="label"><?= Lang::get('requests', 'source_torrent') ?>:</td>
                        <td><?= $Request['SourceTorrent'] ?></td>
                    </tr>

                <?  } ?>
                <tr>
                    <td class="label"><?= Lang::get('requests', 'purchasable_at') ?>:</td>
                    <td><?= $Request['PurchasableAt'] ?></td>
                </tr>
                <tr>
                    <td class="label"><?= Lang::get('requests', 'votes') ?></td>
                    <td>
                        <span id="votecount"><?= number_format($VoteCount) ?></span>
                        <? if ($CanVote) { ?>
                            &nbsp;&nbsp;<a href="javascript:Vote(0)" class="brackets"><strong>+</strong></a>
                            <strong><?= Lang::get('requests', 'costs') ?> <?= Format::get_size($MinimumVote, 0) ?></strong>
                        <?  } ?>
                    </td>
                </tr>
                <? if ($Request['LastVote'] > $Request['TimeAdded']) { ?>
                    <tr>
                        <td class="label"><?= Lang::get('requests', 'last_voted') ?></td>
                        <td><?= time_diff($Request['LastVote']) ?></td>
                    </tr>
                <?
                }
                if ($CanVote) {
                ?>
                    <tr id="voting">
                        <td class="label tooltip" title="<?= Lang::get('requests', 'custom_vote_title') ?>">
                            <?= Lang::get('requests', 'custom_vote') ?></td>
                        <td>
                            <input type="text" id="amount_box" size="8" onchange="Calculate();" />
                            <select id="unit" name="unit" onchange="Calculate();">
                                <option value="mb">MB</option>
                                <option value="gb">GB</option>
                            </select>
                            <input type="button" value="Preview" onclick="Calculate();" />
                            <?= $RequestTax > 0 ? "<strong>{$RequestTaxPercent}% " . Lang::get('requests', 'system_taxed') : '' ?>
                            <!-- <p><?= Lang::get('requests', 'bounty_min_100_mb') ?></p> -->
                        </td>
                    </tr>
                    <tr>
                        <td class="label"><?= Lang::get('requests', 'post_vote_information') ?></td>
                        <td>
                            <form class="add_form" name="request" action="requests.php" method="get" id="request_form">
                                <input type="hidden" name="action" value="vote" />
                                <input type="hidden" name="auth" value="<?= $LoggedUser['AuthKey'] ?>" />
                                <input type="hidden" id="request_tax" value="<?= $RequestTax ?>" />
                                <input type="hidden" id="requestid" name="id" value="<?= $RequestID ?>" />
                                <input type="hidden" id="auth" name="auth" value="<?= $LoggedUser['AuthKey'] ?>" />
                                <input type="hidden" id="amount" name="amount" value="0" />
                                <input type="hidden" id="current_uploaded" value="<?= $LoggedUser['BytesUploaded'] ?>" />
                                <input type="hidden" id="current_downloaded" value="<?= $LoggedUser['BytesDownloaded'] ?>" />
                                <input type="hidden" id="current_rr" value="<?= (float)$LoggedUser['RequiredRatio'] ?>" />
                                <input id="total_bounty" type="hidden" value="<?= $RequestVotes['TotalBounty'] ?>" />
                                <?= $RequestTax > 0 ? 'Bounty after tax: <strong><span id="bounty_after_tax">90.00 MB</span></strong><br />' : '' ?><?= Lang::get('requests', 'if_you_add_the_entered') ?>
                                <strong><span id="new_bounty">0.00 MB</span></strong>
                                <?= Lang::get('requests', 'of_bounty_your_new_stats') ?>: <br />
                                <?= Lang::get('requests', 'uploaded') ?>: <span id="new_uploaded"><?= Format::get_size($LoggedUser['BytesUploaded']) ?></span><br />
                                <?= Lang::get('requests', 'ratio') ?>: <span id="new_ratio"><?= Format::get_ratio_html($LoggedUser['BytesUploaded'], $LoggedUser['BytesDownloaded']) ?></span>
                                <input type="button" id="button" value="确认!" disabled="disabled" onclick="Vote();" />
                            </form>
                        </td>
                    </tr>
                <?  } ?>
                <tr id="bounty">
                    <td class="label"><?= Lang::get('requests', 'bounty') ?></td>
                    <td id="formatted_bounty"><?= Format::get_size($RequestVotes['TotalBounty']) ?></td>
                </tr>
                <?
                if ($IsFilled) {
                    $TimeCompare = 1267643718; // Requests v2 was implemented 2010-03-03 20:15:18
                ?>
                    <tr>
                        <td class="label"><?= Lang::get('requests', 'filled') ?></td>
                        <td>
                            <strong><a href="torrents.php?<?= (strtotime($Request['TimeFilled']) < $TimeCompare ? 'id=' : 'torrentid=') . $Request['TorrentID'] ?>"><?= Lang::get('requests', 'yes') ?></a></strong><?= Lang::get('requests', 'by_user') ?>
                            <?= Users::format_username($Request['FillerID'], false, false, false) ?>
                            <? if ($LoggedUser['ID'] == $Request['UserID'] || $LoggedUser['ID'] == $Request['FillerID'] || check_perms('site_moderate_requests')) { ?>
                                <strong><a href="requests.php?action=unfill&amp;id=<?= $RequestID ?>" class="brackets"><?= Lang::get('requests', 'unfill') ?></a></strong>
                                <?= Lang::get('requests', 'unfilling_a_request_without_reason') ?>
                            <?      } ?>
                        </td>
                    </tr>
                <?  } else { ?>
                    <tr>
                        <td class="label" valign="top"><?= Lang::get('requests', 'fill_request') ?></td>
                        <td>
                            <strong style="margin-bottom: 10px;">[<a href="javascript:void(0);" onclick="$('#fill_a_request_how_to_blockquote').toggle();"><strong class="how_to_toggle"><?= Lang::get('requests', 'fill_a_request_how_to_toggle') ?></strong></a>]</strong>
                            <blockquote id="fill_a_request_how_to_blockquote" style="display: none; margin: 5px 0;"><?= Lang::get('requests', 'fill_a_request_how_to_blockquote') ?></blockquote><br />
                            <a href="upload.php?requestid=<?= $RequestID ?><?= ($Request['GroupID'] ? "&amp;groupid=$Request[GroupID]" : '') ?>"><input type="button" id="upload" value="Upload request" /></a> <?= Lang::get('requests', 'fill_request_explanation') ?>
                            <form class="edit_form" name="request" action="" method="post">
                                <div class="field_div">
                                    <input type="hidden" name="action" value="takefill" />
                                    <input type="hidden" name="auth" value="<?= $LoggedUser['AuthKey'] ?>" />
                                    <input type="hidden" name="requestid" value="<?= $RequestID ?>" />
                                    <input type="text" size="50" name="link" <?= (!empty($Link) ? " value=\"$Link\"" : '') ?> />
                                    <br />
                                    <strong><?= Lang::get('requests', 'should_be_pl_to_the_torrent') ?>
                                        <?= site_url() ?>torrents.php?torrentid=xxxx).</strong>
                                </div>
                                <? if (check_perms('site_moderate_requests')) { ?>
                                    <div class="field_div">
                                        <?= Lang::get('requests', 'for_user') ?>: <input type="text" size="25" name="user" <?= (!empty($FillerUsername) ? " value=\"$FillerUsername\"" : '') ?> />
                                    </div>
                                <?      } ?>
                                <div class="submit_div">
                                    <input type="submit" value="Fill request" />
                                </div>
                            </form>
                        </td>
                    </tr>
                <?  } ?>
            </table>
            <div class="box box2 box_request_desc requests__description">
                <div class="head"><strong><?= Lang::get('requests', 'description') ?></strong></div>
                <div class="pad">
                    <?= Text::full_format($Request['Description']); ?>
                </div>
            </div>
            <div id="request_comments">
                <div class="linkbox">
                    <a name="comments"></a>
                    <?
                    $Pages = Format::get_pages($Page, $NumComments, TORRENT_COMMENTS_PER_PAGE, 9, '#comments');
                    echo $Pages;
                    ?>
                </div>
                <?

                //---------- Begin printing
                CommentsView::render_comments($Thread, $LastRead, "requests.php?action=view&amp;id=$RequestID");

                if ($Pages) { ?>
                    <div class="linkbox pager"><?= $Pages ?></div>
                <?
                }

                View::parse('generic/reply/quickreply.php', array(
                    'InputName' => 'pageid',
                    'InputID' => $RequestID,
                    'Action' => 'comments.php?page=requests',
                    'InputAction' => 'take_post',
                    'SubscribeBox' => true
                ));
                ?>
            </div>
        </div>
    </div>
</div>
<? View::show_footer(); ?>