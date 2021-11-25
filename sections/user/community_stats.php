<?
$DB->query("
	SELECT Page, COUNT(1)
	FROM comments
	WHERE AuthorID = $UserID
	GROUP BY Page");
$Comments = $DB->to_array('Page');
$NumComments = $Comments['torrents'][1];
$NumArtistComments = $Comments['artist'][1];
$NumCollageComments = $Comments['collages'][1];
$NumRequestComments = $Comments['requests'][1];

$DB->query("
	SELECT COUNT(ID)
	FROM collages
	WHERE Deleted = '0'
		AND UserID = '$UserID'");
list($NumCollages) = $DB->next_record();

$DB->query("
	SELECT COUNT(DISTINCT CollageID)
	FROM collages_torrents AS ct
		JOIN collages ON CollageID = ID
	WHERE Deleted = '0'
		AND ct.UserID = '$UserID'");
list($NumCollageContribs) = $DB->next_record();

$DB->query("
	SELECT COUNT(DISTINCT GroupID)
	FROM torrents
	WHERE UserID = '$UserID'");
list($UniqueGroups) = $DB->next_record();

$DB->query("
	SELECT COUNT(ID)
	FROM torrents
	WHERE Format IN ('FLAC','WAV','DSD')
		AND UserID = '$UserID'");
list($Lossless) = $DB->next_record();

$DB->query("
	SELECT COUNT(ID)
	FROM torrents
	WHERE Buy='1'
		AND UserID = '$UserID'
	union SELECT COUNT(ID)
	FROM torrents
	WHERE Diy='1'
		AND UserID = '$UserID'");
list($OriginalsBuy) = $DB->next_record();
list($OriginalsDiy) = $DB->next_record();
$DB->query("
	SELECT COUNT(ID)
	FROM torrents
	WHERE ((LogScore = 100 AND Format = 'FLAC' AND LogChecksum = '1' AND HasLogDB = '1')
			OR (Media = 'Vinyl' AND Format = 'FLAC')
			OR (Media = 'WEB' AND Format = 'FLAC')
			OR (Media = 'DVD' AND Format = 'FLAC')
			OR (Media = 'Soundboard' AND Format = 'FLAC')
			OR (Media = 'Cassette' AND Format = 'FLAC')
			OR (Media = 'SACD' AND Format = 'FLAC')
			OR (Media = 'Blu-ray' AND Format = 'FLAC')
			OR (Media = 'DAT' AND Format = 'FLAC'))
		AND UserID = '$UserID'");
list($PerfectFLACs) = $DB->next_record();

$DB->query("
	SELECT COUNT(ID)
	FROM torrents
	WHERE ((LogScore = 100 AND Format IN ('FLAC','WAV') AND LogChecksum = '1' AND HasLogDB = '1')
			OR (Media = 'Vinyl' AND Format IN ('FLAC','WAV'))
			OR (Media = 'WEB' AND Format IN ('FLAC','WAV','DSD'))
			OR (Media = 'DVD' AND Format IN ('FLAC','WAV'))
			OR (Media = 'Soundboard' AND Format IN ('FLAC','WAV'))
			OR (Media = 'Cassette' AND Format IN ('FLAC','WAV'))
			OR (Media = 'SACD' AND Format IN ('FLAC','WAV','DSD'))
			OR (Media = 'Blu-ray' AND Format IN ('FLAC','WAV'))
			OR (Media = 'DAT' AND Format IN ('FLAC','WAV')))
		AND UserID = '$UserID'");
list($PerfectWAVs) = $DB->next_record();

$DB->prepared_query("SELECT COUNT(*) FROM forums_topics WHERE AuthorID = ?", $UserID);
list($ForumTopics) = $DB->fetch_record();
?>
<div class="box box_info box_userinfo_community">
    <div class="head colhead_dark"><?= Lang::get('user', 'community') ?></div>
    <ul class="stats nobullet">
        <li id="comm_posts"><?= Lang::get('user', 'community_topic') ?>: <?= number_format($ForumTopics) ?> <a href="userhistory.php?action=topics&amp;userid=<?= $UserID ?>" class="brackets"><?= Lang::get('user', 'view') ?></a></li>
        <li id="comm_posts"><?= Lang::get('user', 'community_pots') ?>: <?= number_format($ForumPosts) ?> <a href="userhistory.php?action=posts&amp;userid=<?= $UserID ?>" class="brackets"><?= Lang::get('user', 'view') ?></a></li>
        <? if ($Override = check_paranoia_here('torrentcomments+')) { ?>
            <li id="comm_torrcomm" <?= ($Override === 2 ? ' class="paranoia_override"' : '') ?>><?= Lang::get('user', 'community_comms') ?>: <?= number_format($NumComments) ?>
                <? if ($Override = check_paranoia_here('torrentcomments')) { ?>
                    <a href="comments.php?id=<?= $UserID ?>" class="brackets<?= ($Override === 2 ? ' paranoia_override' : '') ?>"><?= Lang::get('user', 'view') ?></a>
                <?              } ?>
            </li>
            <li id="comm_artcomm" <?= ($Override === 2 ? ' class="paranoia_override"' : '') ?>><?= Lang::get('user', 'community_arts') ?>: <?= number_format($NumArtistComments) ?>
                <? if ($Override = check_paranoia_here('torrentcomments')) { ?>
                    <a href="comments.php?id=<?= $UserID ?>&amp;action=artist" class="brackets<?= ($Override === 2 ? ' paranoia_override' : '') ?>"><?= Lang::get('user', 'view') ?></a>
                <?              } ?>
            </li>
            <?
            if (ENABLE_COLLAGES) {
            ?>
                <li id="comm_collcomm" <?= ($Override === 2 ? ' class="paranoia_override"' : '') ?>><?= Lang::get('user', 'community_colls') ?>: <?= number_format($NumCollageComments) ?>
                    <? if ($Override = check_paranoia_here('torrentcomments')) { ?>
                        <a href="comments.php?id=<?= $UserID ?>&amp;action=collages" class="brackets<?= ($Override === 2 ? ' paranoia_override' : '') ?>"><?= Lang::get('user', 'view') ?></a>
                    <?              } ?>
                </li>
            <?
            }
            ?>
            <li id="comm_reqcomm" <?= ($Override === 2 ? ' class="paranoia_override"' : '') ?>><?= Lang::get('user', 'community_reqs') ?>: <?= number_format($NumRequestComments) ?>
                <? if ($Override = check_paranoia_here('torrentcomments')) { ?>
                    <a href="comments.php?id=<?= $UserID ?>&amp;action=requests" class="brackets<?= ($Override === 2 ? ' paranoia_override' : '') ?>"><?= Lang::get('user', 'view') ?></a>
                <?              } ?>
            </li>
        <?
        }
        if (($Override = check_paranoia_here('collages+')) && ENABLE_COLLAGES) { ?>
            <li id="comm_collstart" <?= ($Override === 2 ? ' class="paranoia_override"' : '') ?>><?= Lang::get('user', 'community_collstart') ?>: <?= number_format($NumCollages) ?>
                <? if ($Override = check_paranoia_here('collages')) { ?>
                    <a href="collages.php?userid=<?= $UserID ?>" class="brackets<?= (($Override === 2) ? ' paranoia_override' : '') ?>"><?= Lang::get('user', 'view') ?></a>
                <?              } ?>
            </li>
        <?
        }
        if (($Override = check_paranoia_here('collagecontribs+')) && ENABLE_COLLAGES) { ?>
            <li id="comm_collcontrib" <?= ($Override === 2 ? ' class="paranoia_override"' : '') ?>><?= Lang::get('user', 'community_collcontrib') ?>: <? echo number_format($NumCollageContribs); ?>
                <? if ($Override = check_paranoia_here('collagecontribs')) { ?>
                    <a href="collages.php?userid=<?= $UserID ?>&amp;contrib=1" class="brackets<?= (($Override === 2) ? ' paranoia_override' : '') ?>"><?= Lang::get('user', 'view') ?></a>
                <?              } ?>
            </li>
        <?
        }

        //Let's see if we can view requests because of reasons
        $ViewAll    = check_paranoia_here('requestsfilled_list');
        $ViewCount  = check_paranoia_here('requestsfilled_count');
        $ViewBounty = check_paranoia_here('requestsfilled_bounty');

        if ($ViewCount && !$ViewBounty && !$ViewAll) { ?>
            <li><?= Lang::get('user', 'requestsfilled') ?>: <?= number_format($RequestsFilled) ?></li>
        <?  } elseif (!$ViewCount && $ViewBounty && !$ViewAll) { ?>
            <li><?= Lang::get('user', 'requestsfilled') ?>: <?= Format::get_size($TotalBounty) ?> <?= Lang::get('user', 'collected') ?></li>
        <?  } elseif ($ViewCount && $ViewBounty && !$ViewAll) { ?>
            <li><?= Lang::get('user', 'requestsfilled') ?>: <?= number_format($RequestsFilled) ?> <?= Lang::get('user', 'for') ?> <?= Format::get_size($TotalBounty) ?></li>
        <?  } elseif ($ViewAll) { ?>
            <li>
                <span<?= ($ViewCount === 2 ? ' class="paranoia_override"' : '') ?>><?= Lang::get('user', 'requestsfilled') ?>: <?= number_format($RequestsFilled) ?></span>
                    <span<?= ($ViewBounty === 2 ? ' class="paranoia_override"' : '') ?>> <?= Lang::get('user', 'for') ?> <?= Format::get_size($TotalBounty) ?></span>
                        <a href="requests.php?type=filled&amp;userid=<?= $UserID ?>" class="brackets<?= (($ViewAll === 2) ? ' paranoia_override' : '') ?>"><?= Lang::get('user', 'view') ?></a>
            </li>
        <?
        }

        //Let's see if we can view requests because of reasons
        $ViewAll    = check_paranoia_here('requestsvoted_list');
        $ViewCount  = check_paranoia_here('requestsvoted_count');
        $ViewBounty = check_paranoia_here('requestsvoted_bounty');

        if ($ViewCount && !$ViewBounty && !$ViewAll) { ?>
            <li><?= Lang::get('user', 'requestscreated') ?>: <?= number_format($RequestsCreated) ?></li>
            <li><?= Lang::get('user', 'requestsvoted') ?> <?= number_format($RequestsVoted) ?></li>
        <?  } elseif (!$ViewCount && $ViewBounty && !$ViewAll) { ?>
            <li><?= Lang::get('user', 'requestscreated') ?>: <?= Format::get_size($RequestsCreatedSpent) ?> <?= Lang::get('user', 'spent') ?></li>
            <li><?= Lang::get('user', 'requestsvoted') ?> <?= Format::get_size($TotalSpent) ?> <?= Lang::get('user', 'spent') ?></li>
        <?  } elseif ($ViewCount && $ViewBounty && !$ViewAll) { ?>
            <li><?= Lang::get('user', 'requestscreated') ?>: <?= number_format($RequestsCreated) ?> <?= Lang::get('user', 'for') ?> <?= Format::get_size($RequestsCreatedSpent) ?></li>
            <li><?= Lang::get('user', 'requestsvoted') ?> <?= number_format($RequestsVoted) ?> <?= Lang::get('user', 'for') ?> <?= Format::get_size($TotalSpent) ?></li>
        <?  } elseif ($ViewAll) { ?>
            <li>
                <span<?= ($ViewCount === 2 ? ' class="paranoia_override"' : '') ?>><?= Lang::get('user', 'requestscreated') ?>: <?= number_format($RequestsCreated) ?></span>
                    <span<?= ($ViewBounty === 2 ? ' class="paranoia_override"' : '') ?>> <?= Lang::get('user', 'for') ?> <?= Format::get_size($RequestsCreatedSpent) ?></span>
                        <a href="requests.php?type=created&amp;userid=<?= $UserID ?>" class="brackets<?= ($ViewAll === 2 ? ' paranoia_override' : '') ?>"><?= Lang::get('user', 'view') ?></a>
            </li>
            <li>
                <span<?= ($ViewCount === 2 ? ' class="paranoia_override"' : '') ?>><?= Lang::get('user', 'requestsvoted') ?>: <?= number_format($RequestsVoted) ?></span>
                    <span<?= ($ViewBounty === 2 ? ' class="paranoia_override"' : '') ?>> <?= Lang::get('user', 'for') ?> <?= Format::get_size($TotalSpent) ?></span>
                        <a href="requests.php?type=voted&amp;userid=<?= $UserID ?>" class="brackets<?= ($ViewAll === 2 ? ' paranoia_override' : '') ?>"><?= Lang::get('user', 'view') ?></a>
            </li>
        <?
        }
        if ($CanViewUploads || $Override = check_paranoia_here('uploads+')) { ?>
            <li id="comm_upload" <?= ($Override === 2 ? ' class="paranoia_override"' : '') ?>><?= Lang::get('user', 'comm_upload') ?>: <?= number_format($Uploads) . " <span title='" . Lang::get('user', 'total_uploads_title') . "'>(" . $TotalUploads . ")</span>" ?>
                <? if ($CanViewUploads || $Override = check_paranoia_here('uploads')) { ?>
                    <a href="torrents.php?type=uploaded&amp;userid=<?= $UserID ?>" class="brackets<?= ($Override === 2 ? ' paranoia_override' : '') ?>"><?= Lang::get('user', 'view') ?></a>
                    <? if (check_perms('zip_downloader')) { ?>
                        <a href="torrents.php?action=redownload&amp;type=uploads&amp;userid=<?= $UserID ?>" onclick="return confirm('<?= Lang::get('user', 'redownloading_confirm') ?>');" class="brackets<?= ($Override === 2 ? ' paranoia_override' : '') ?>"><?= Lang::get('user', 'community_dl') ?></a>
                <?
                    }
                }
                ?>
            </li>
        <?
        }
        if ($CanViewUploads || $Override = check_paranoia_here('originals+')) { ?>
            <li id="comm_original" <?= ($Override === 2 ? ' class="paranoia_override"' : '') ?>><?= Lang::get('user', 'comm_originals') ?>: <span title="<?= Lang::get('user', 'self_purchase_number') ?>"><?= number_format($OriginalsBuy) ?></span>+<span title="<?= Lang::get('user', 'self_rip_number') ?>"><?= number_format($OriginalsDiy) ?></span>
                <? if ($CanViewUploads || $Override = check_paranoia_here('originals')) { ?>
                    <a href="torrents.php?type=uploaded&amp;userid=<?= $UserID ?>&amp;filter=original" class="brackets<?= ($Override === 2 ? ' paranoia_override' : '') ?>"><?= Lang::get('user', 'view') ?></a>
                <?                  } ?>
            </li>
        <?
        }
        if ($Override = check_paranoia_here('seeding+')) {
        ?>
            <li id="comm_seeding" <?= ($Override === 2 ? ' class="paranoia_override"' : '') ?>><?= Lang::get('user', 'comm_seeding') ?>:
                <span class="user_commstats" id="user_commstats_seeding"><a href="#" class="brackets" onclick="commStats(<?= $UserID ?>); return false;"><?= Lang::get('user', 'community_show') ?></a></span>
                <? if ($Override = check_paranoia_here('snatched+')) { ?>
                    <span<?= ($Override === 2 ? ' class="paranoia_override"' : '') ?> id="user_commstats_seedingperc"></span>
                    <?
                }
                if ($Override = check_paranoia_here('seeding')) {
                    ?>
                        <a href="torrents.php?type=seeding&amp;userid=<?= $UserID ?>" class="brackets<?= ($Override === 2 ? ' paranoia_override' : '') ?>"><?= Lang::get('user', 'view') ?></a>
                        <? if (check_perms('zip_downloader')) { ?>
                            <a href="torrents.php?action=redownload&amp;type=seeding&amp;userid=<?= $UserID ?>" onclick="return confirm('<?= Lang::get('user', 'redownloading_confirm') ?>');" class="brackets"><?= Lang::get('user', 'community_dl') ?></a></a>
                    <?
                        }
                    }
                    ?>
            </li>
        <?
        }
        if ($Override = check_paranoia_here('leeching+')) {
        ?>
            <li id="comm_leeching" <?= ($Override === 2 ? ' class="paranoia_override"' : '') ?>><?= Lang::get('user', 'comm_leeching') ?>:
                <span class="user_commstats" id="user_commstats_leeching"><a href="#" class="brackets" onclick="commStats(<?= $UserID ?>); return false;"><?= Lang::get('user', 'community_show') ?></a></a></span>
                <? if ($Override = check_paranoia_here('leeching')) { ?>
                    <a href="torrents.php?type=leeching&amp;userid=<?= $UserID ?>" class="brackets<?= ($Override === 2 ? ' paranoia_override' : '') ?>"><?= Lang::get('user', 'view') ?></a>
                <?
                }
                if ($DisableLeech == 0 && check_perms('users_view_ips')) {
                ?>
                    <strong>(Disabled)</strong>
                <?      } ?>
            </li>
        <?
        }
        if ($Override = check_paranoia_here('snatched+')) { ?>
            <li id="comm_snatched" <?= ($Override === 2 ? ' class="paranoia_override"' : '') ?>><?= Lang::get('user', 'comm_snatched') ?>:
                <span class="user_commstats" id="user_commstats_snatched"><a href="#" class="brackets" onclick="commStats(<?= $UserID ?>); return false;"><?= Lang::get('user', 'community_show') ?></a></a></span>
                <? if ($Override = check_perms('site_view_torrent_snatchlist', $Class)) { ?>
                    <span id="user_commstats_usnatched" <?= ($Override === 2 ? ' class="paranoia_override"' : '') ?>></span>
                <?
                }
            }
            if ($Override = check_paranoia_here('snatched')) { ?>
                <a href="torrents.php?type=snatched&amp;userid=<?= $UserID ?>" class="brackets<?= ($Override === 2 ? ' paranoia_override' : '') ?>"><?= Lang::get('user', 'view') ?></a>
                <? if (check_perms('zip_downloader')) { ?>
                    <a href="torrents.php?action=redownload&amp;type=snatches&amp;userid=<?= $UserID ?>" onclick="return confirm('<?= Lang::get('user', 'redownloading_confirm') ?>');" class="brackets"><?= Lang::get('user', 'community_dl') ?></a></a>
                <?                  } ?>
            </li>
        <?
            }
            if (check_perms('site_view_torrent_snatchlist', $Class)) {
        ?>
            <li id="comm_downloaded"><?= Lang::get('user', 'comm_downloaded') ?>:
                <span class="user_commstats" id="user_commstats_downloaded"><a href="#" class="brackets" onclick="commStats(<?= $UserID ?>); return false;"><?= Lang::get('user', 'community_show') ?></a></a></span>
                <span id="user_commstats_udownloaded"></span>
                <a href="torrents.php?type=downloaded&amp;userid=<?= $UserID ?>" class="brackets"><?= Lang::get('user', 'view') ?></a>
            </li>
        <?
            }
            if ($Override = check_paranoia_here('invitedcount')) {
                $DB->query("
		SELECT COUNT(UserID)
		FROM users_info
		WHERE Inviter = '$UserID'");
                list($Invited) = $DB->next_record();
        ?>
            <li id="comm_invited"><?= Lang::get('user', 'comm_invited') ?>: <?= number_format($Invited) ?></li>
        <?
            }
        ?>
    </ul>
    <? if ($LoggedUser['AutoloadCommStats']) { ?>
        <script type="text/javascript">
            commStats(<?= $UserID ?>);
        </script>
    <?  } ?>
</div>