<?

use Gazelle\Manager\Donation;

if (empty($_GET['id']) || !is_number($_GET['id']) || (!empty($_GET['preview']) && !is_number($_GET['preview']))) {
    error(404);
}
$UserID = (int)$_GET['id'];
$Bonus = new \Gazelle\Bonus($DB, $Cache);

if (!empty($_POST)) {
    authorize();
    foreach (['action', 'flsubmit', 'fltype'] as $arg) {
        if (!isset($_POST[$arg])) {
            error(403);
        }
    }
    if ($_POST['action'] !== 'fltoken') {
        error(403);
    }
    if ($_POST['flsubmit'] !== 'Send') {
        error(403);
    }
    if (!preg_match('/^fl-(other-[123])$/', $_POST['fltype'], $match)) {
        error(403);
    }
    $FL_OTHER_tokens = $Bonus->purchaseTokenOther($LoggedUser['ID'], $UserID, $match[1], $LoggedUser);
    echo json_encode(array("tokens" => $FL_OTHER_tokens));
    return;
}
$Preview = isset($_GET['preview']) ? $_GET['preview'] : 0;
if ($UserID == $LoggedUser['ID']) {
    $OwnProfile = true;
    if ($Preview == 1) {
        $OwnProfile = false;
        $ParanoiaString = $_GET['paranoia'];
        $CustomParanoia = explode(',', $ParanoiaString);
    }
    $FL_Items = [];
} else {
    $OwnProfile = false;
    //Don't allow any kind of previewing on others' profiles
    $Preview = 0;
    $FL_Items = $Bonus->getListOther($UserID, G::$LoggedUser['BonusPoints']);
}

// 捐赠信息
$donation = new Donation();
$donationInfo = $donation->info($UserID);
$leaderBoardRank = $donation->leaderboardRank($UserID);
$donorVisible = $donation->isVisible($UserID);
$isDonor = $donation->isDonor($UserID);
$EnableRewards = $donation->enabledRewards($UserID);
$ProfileRewards = $donation->profileRewards($UserID);
$donationHistroy = $donation->history($UserID);

$FA_Key = null;

if (check_perms('users_mod')) { // Person viewing is a staff member
    $DB->query("
		SELECT
			m.Username,
			m.Email,
			m.LastAccess,
			m.IP,
			p.Level AS Class,
			m.Uploaded,
			m.Downloaded,
			m.BonusPoints,
			m.RequiredRatio,
			m.Title,
			m.torrent_pass,
			m.Enabled,
			m.Paranoia,
			m.Invites,
			m.can_leech,
			m.Visible,
			i.JoinDate,
			i.Info,
			i.Avatar,
			i.AdminComment,
			i.Donor,
			i.Found,
			i.Artist,
			i.Warned,
			i.SupportFor,
			i.RestrictedForums,
			i.PermittedForums,
			i.Inviter,
			inviter.Username,
			COUNT(posts.id) AS ForumPosts,
			i.RatioWatchEnds,
			i.RatioWatchDownload,
			i.DisableAvatar,
			i.DisableInvites,
			i.DisablePosting,
			i.DisablePoints,
			i.DisableForums,
			i.DisableTagging,
			i.DisableUpload,
			i.DisableWiki,
			i.DisablePM,
			i.DisableIRC,
			i.DisableRequests," . "
			m.FLTokens,
			m.2FA_Key,
			SHA1(i.AdminComment),
			i.InfoTitle,
			la.Type AS LockedAccount,
			i.DisableCheckAll,
			i.DisableCheckSelf,
			m.TotalUploads,
            m.BonusUploaded
		FROM users_main AS m
			JOIN users_info AS i ON i.UserID = m.ID
			LEFT JOIN users_main AS inviter ON i.Inviter = inviter.ID
			LEFT JOIN permissions AS p ON p.ID = m.PermissionID
			LEFT JOIN forums_posts AS posts ON posts.AuthorID = m.ID
			LEFT JOIN locked_accounts AS la ON la.UserID = m.ID
		WHERE m.ID = '$UserID'
		GROUP BY AuthorID");

    if (!$DB->has_results()) { // If user doesn't exist
        header("Location: log.php?search=User+$UserID");
    }

    list($Username, $Email, $LastAccess, $IP, $Class, $Uploaded, $Downloaded, $BonusPoints, $RequiredRatio, $CustomTitle, $torrent_pass, $Enabled, $Paranoia, $Invites, $DisableLeech, $Visible, $JoinDate, $Info, $Avatar, $AdminComment, $Donor, $Found, $Artist, $Warned, $SupportFor, $RestrictedForums, $PermittedForums, $InviterID, $InviterName, $ForumPosts, $RatioWatchEnds, $RatioWatchDownload, $DisableAvatar, $DisableInvites, $DisablePosting, $DisablePoints, $DisableForums, $DisableTagging, $DisableUpload, $DisableWiki, $DisablePM, $DisableIRC, $DisableRequests, $FLTokens, $FA_Key, $CommentHash, $InfoTitle, $LockedAccount, $DisableCheckAll, $DisableCheckSelf, $TotalUploads, $BonusUploaded) = $DB->next_record(MYSQLI_NUM, array(9, 12));
} else { // Person viewing is a normal user
    $DB->query("
		SELECT
			m.Username,
			m.Email,
			m.LastAccess,
			m.IP,
			p.Level AS Class,
			m.Uploaded,
			m.Downloaded,
			m.BonusPoints,
			m.RequiredRatio,
			m.Enabled,
			m.Paranoia,
			m.Invites,
			m.Title,
			m.torrent_pass,
			m.can_leech,
			i.JoinDate,
			i.Info,
			i.Avatar,
			m.FLTokens,
			i.Donor,
			i.Found,
			i.Warned,
			COUNT(posts.id) AS ForumPosts,
			i.Inviter,
			i.DisableInvites,
			inviter.username,
			i.InfoTitle,
            m.BonusUploaded
		FROM users_main AS m
			JOIN users_info AS i ON i.UserID = m.ID
			LEFT JOIN permissions AS p ON p.ID = m.PermissionID
			LEFT JOIN users_main AS inviter ON i.Inviter = inviter.ID
			LEFT JOIN forums_posts AS posts ON posts.AuthorID = m.ID
		WHERE m.ID = $UserID
		GROUP BY AuthorID");

    if (!$DB->has_results()) { // If user doesn't exist
        header("Location: log.php?search=User+$UserID");
    }

    list(
        $Username, $Email, $LastAccess, $IP, $Class, $Uploaded, $Downloaded, $BonusPoints,
        $RequiredRatio, $Enabled, $Paranoia, $Invites, $CustomTitle, $torrent_pass,
        $DisableLeech, $JoinDate, $Info, $Avatar, $FLTokens, $Donor, $Found, $Warned,
        $ForumPosts, $InviterID, $DisableInvites, $InviterName, $InfoTitle, $BonusUploaded
    ) = $DB->next_record(MYSQLI_NUM, array(10, 12));
}

$DB->query("
SELECT
	IFNULL(SUM(t.Size / (1024 * 1024 * 1024) * 1 *(
		0.025 + (
			(0.06 * LN(1 + (xfh.seedtime / (24)))) / (POW(GREATEST(t.Seeders, 1), 0.6))
		)
	)),0)
FROM
	(SELECT DISTINCT uid,fid FROM xbt_files_users WHERE active=1 AND remaining=0 AND mtime > unix_timestamp(NOW() - INTERVAL 1 HOUR) AND uid = {$UserID}) AS xfu
	JOIN xbt_files_history AS xfh ON xfh.uid = xfu.uid AND xfh.fid = xfu.fid
	JOIN torrents AS t ON t.ID = xfu.fid
WHERE
	xfu.uid = {$UserID}");
list($BonusPointsPerHour) = $DB->next_record(MYSQLI_NUM);

// Image proxy CTs
$DisplayCustomTitle = $CustomTitle;
if (check_perms('site_proxy_images') && !empty($CustomTitle)) {
    $DisplayCustomTitle = preg_replace_callback(
        '~src=("?)(http.+?)(["\s>])~',
        function ($Matches) {
            return 'src=' . $Matches[1] . ImageTools::process($Matches[2]) . $Matches[3];
        },
        $CustomTitle
    );
}

if ($Preview == 1) {
    if (strlen($ParanoiaString) == 0) {
        $Paranoia = array();
    } else {
        $Paranoia = $CustomParanoia;
    }
} else {
    $Paranoia = unserialize($Paranoia);
    if (!is_array($Paranoia)) {
        $Paranoia = array();
    }
}
$ParanoiaLevel = 0;
foreach ($Paranoia as $P) {
    $ParanoiaLevel++;
    if (strpos($P, '+') !== false) {
        $ParanoiaLevel++;
    }
}

$JoinedDate = time_diff($JoinDate);
$LastAccess = time_diff($LastAccess);

function check_paranoia_here($Setting) {
    global $Paranoia, $Class, $UserID, $Preview;
    if ($Preview == 1) {
        return check_paranoia($Setting, $Paranoia, $Class);
    } else {
        return check_paranoia($Setting, $Paranoia, $Class, $UserID);
    }
}

View::show_header($Username, "jquery.imagesloaded,jquery.wookmark,user,bbcode,requests,lastfm,comments,info_paster", "tiles");

if (check_paranoia_here('artistsadded')) {
    $DB->query("
		SELECT COUNT(ArtistID)
		FROM torrents_artists
		WHERE UserID = $UserID");
    list($ArtistsAdded) = $DB->next_record();
} else {
    $ArtistsAdded = 0;
}

if (check_paranoia_here('requestsvoted_count') || check_paranoia_here('requestsvoted_bounty')) {
    $DB->query("
		SELECT COUNT(RequestID), SUM(Bounty)
		FROM requests_votes
		WHERE UserID = $UserID");
    list($RequestsVoted, $TotalSpent) = $DB->next_record();
    $DB->query("
		SELECT COUNT(r.ID), SUM(rv.Bounty)
		FROM requests AS r
			LEFT JOIN requests_votes AS rv ON rv.RequestID = r.ID AND rv.UserID = r.UserID
		WHERE r.UserID = $UserID");
    list($RequestsCreated, $RequestsCreatedSpent) = $DB->next_record();
} else {
    $RequestsVoted = $TotalSpent = $RequestsCreated = $RequestsCreatedSpent = 0;
}


if (check_paranoia_here('requestsfilled_count') || check_paranoia_here('requestsfilled_bounty')) {
    $DB->query("
		SELECT
			COUNT(DISTINCT r.ID),
			SUM(rv.Bounty)
		FROM requests AS r
			LEFT JOIN requests_votes AS rv ON r.ID = rv.RequestID
		WHERE r.FillerID = $UserID");
    list($RequestsFilled, $TotalBounty) = $DB->next_record();
} else {
    $RequestsFilled = $TotalBounty = 0;
}

//Do the ranks
$UploadedRank = UserRank::get_rank('uploaded', $Uploaded);
$DownloadedRank = UserRank::get_rank('downloaded', $Downloaded);
$UploadsRank = UserRank::get_rank('uploads', $Uploads);
$RequestRank = UserRank::get_rank('requests', $RequestsFilled);
$PostRank = UserRank::get_rank('posts', $ForumPosts);
$BountyRank = UserRank::get_rank('bounty', $TotalSpent);
$ArtistsRank = UserRank::get_rank('artists', $ArtistsAdded);

$DB->query("select count(1) from thumb where ToUserID = $UserID");
list($ThumbCount) = $DB->next_record();
?>
<div class="thin">
    <div class="header">
        <h2><?= Users::format_username($UserID, true, true, true, false, true, false, true) ?>
            <span id="thumb"><i class="far fa-thumbs-up"></i><?= $ThumbCount ? ' ' . $ThumbCount : '' ?></span>
        </h2>
    </div>
    <div class="linkbox">
        <?
        if (!$OwnProfile) {
        ?>
            <a href="inbox.php?action=compose&amp;to=<?= $UserID ?>" class="brackets"><?= Lang::get('user', 'compose') ?></a>
            <?
            $DB->query("
		SELECT FriendID
		FROM friends
		WHERE UserID = '$LoggedUser[ID]'
			AND FriendID = '$UserID'");
            if (!$DB->has_results()) {
            ?>
                <a href="friends.php?action=add&amp;friendid=<?= $UserID ?>&amp;auth=<?= $LoggedUser['AuthKey'] ?>" class="brackets"><?= Lang::get('user', 'add_friend') ?></a>
            <?  } ?>
            <a href="reports.php?action=report&amp;type=user&amp;id=<?= $UserID ?>" class="brackets"><?= Lang::get('user', 'report') ?></a>
        <?

        }

        if (check_perms('users_edit_profiles', $Class) || $LoggedUser['ID'] == $UserID) {
        ?>
            <a href="user.php?action=edit&amp;userid=<?= $UserID ?>" class="brackets"><?= Lang::get('user', 'setting') ?></a>
        <?
        }
        if (check_perms('users_view_invites', $Class)) {
        ?>
            <a href="user.php?action=invite&amp;userid=<?= $UserID ?>" class="brackets"><?= Lang::get('user', 'invite') ?></a>
        <?
        }
        if (check_perms('admin_manage_permissions', $Class)) {
        ?>
            <a href="user.php?action=permissions&amp;userid=<?= $UserID ?>" class="brackets"><?= Lang::get('user', 'permissions') ?></a>
        <?
        }
        if (check_perms('users_view_ips', $Class)) {
        ?>
            <a href="user.php?action=sessions&amp;userid=<?= $UserID ?>" class="brackets"><?= Lang::get('user', 'sessions') ?></a>
            <a href="userhistory.php?action=copypaste&amp;userid=<?= $UserID ?>" class="brackets"><?= Lang::get('user', 'copypaste') ?></a>
        <?
        }
        if (check_perms('admin_reports')) {
        ?>
            <a href="reportsv2.php?view=reporter&amp;id=<?= $UserID ?>" class="brackets"><?= Lang::get('user', 'reporter') ?></a>
        <?
        }
        if (check_perms('users_mod')) {
        ?>
            <a href="userhistory.php?action=token_history&amp;userid=<?= $UserID ?>" class="brackets"><?= Lang::get('user', 'token_history') ?></a>
        <?
        }
        if (check_perms('admin_clear_cache') && check_perms('users_override_paranoia')) {
        ?>
            <a href="user.php?action=clearcache&amp;id=<?= $UserID ?>" class="brackets"><?= Lang::get('user', 'clearcache') ?></a>
        <?
        }
        if (check_perms('users_mod')) {
        ?>
            <a href="#staff_tools" class="brackets"><?= Lang::get('user', 'staff_tools') ?></a>
        <?
        }
        ?>
    </div>
    <div class="grid_container">
        <div class="sidebar">
            <?
            if ($Avatar && Users::has_avatars_enabled()) {
            ?>
                <div class="box box_image box_image_avatar">
                    <div class="head colhead_dark"><?= Lang::get('user', 'avatar') ?></div>
                    <div class="pad">
                        <?= Users::show_avatar($Avatar, $UserID, $Username, $HeavyInfo['DisableAvatars']) ?>
                    </div>
                </div>
            <?
            }
            if ($Enabled == 1 && (count($FL_Items) || isset($FL_OTHER_tokens))) {
            ?>
                <div class="box box_info box_userinfo_give_FL">
                    <?
                    if (isset($FL_OTHER_tokens)) {
                    ?>
                        <div class="head colhead_dark">Freeleech Tokens Given</div>
                        <ul class="stats nobullet">
                            <?
                            if ($FL_OTHER_tokens > 0) {
                                $s = $FL_OTHER_tokens > 1 ? 's' : '';
                            ?>
                                <li>You gave <?= $FL_OTHER_tokens ?> token<?= $s ?> to <?= $Username ?>. Your generosity is most appreciated!</li>
                            <?
                            } else {
                            ?>
                                <li>You attempted to give some tokens to <?= $Username ?> but something didn't work out.
                                    No points were spent.</li>
                            <?
                            }
                            ?>
                        </ul>
                    <?
                    } else {
                    ?>
                        <div class="head colhead_dark"><?= Lang::get('user', 'send_fltoken') ?></div>
                        <form class="fl_form" name="user" id="fl_form" action="user.php?id=<?= $UserID ?>" method="post">
                            <ul class="stats nobullet">
                                <?
                                foreach ($FL_Items as $data) {
                                    $label_title = Lang::get('user', 'this_costs', false, $data['Price'], $data['After']);
                                ?>
                                    <li><input type="radio" name="fltype" id="fl-<?= $data['Label'] ?>" value="fl-<?= $data['Label'] ?>" />
                                        <label title="<?= $label_title ?>" for="fl-<?= $data['Label'] ?>"> <?= $data['Name'] ?></label>
                                    </li>
                                <?
                                }
                                ?>
                                <!--
            <li><input type="submit" name="flsubmit" value="Send" /></li>
-->
                                <li><input type="button" name="flsubmit" onclick="sendTokens(<?= $UserID ?>, '<?= $LoggedUser['AuthKey'] ?>')" value="Send" /></li>
                            </ul>
                            <input type="hidden" name="action" value="fltoken" />
                            <input type="hidden" name="auth" value="<?= $LoggedUser['AuthKey'] ?>" />
                        </form>
                    <?
                    }
                    ?>
                </div>
            <?
            }
            ?>

            <div class="box box_info box_userinfo_stats">
                <div class="head colhead_dark"><?= Lang::get('user', 'statistics') ?></div>
                <ul class="stats nobullet">
                    <li><?= Lang::get('user', 'joineddate') ?>: <?= $JoinedDate ?></li>
                    <? if (($Override = check_paranoia_here('lastseen'))) { ?>
                        <li <?= ($Override === 2 ? ' class="paranoia_override"' : '') ?>><?= Lang::get('user', 'lastaccess') ?>: <?= $LastAccess ?></li>
                    <?
                    }
                    if (($Override = check_paranoia_here('uploaded'))) {
                    ?>
                        <li class="<?= ($Override === 2 ? 'paranoia_override' : '') ?>"><?= Lang::get('user', 'uploaded') ?>: <span class="tooltip" title="<?= Format::get_size($Uploaded, 5) ?>"><?= Format::get_size($Uploaded) ?></span> <span class="tooltip" title="<?= Lang::get('user', 'true_uploaded_title') ?>">(<?= Format::get_size($Uploaded - $BonusUploaded) ?>)</span></li>
                    <?
                    }
                    if (($Override = check_paranoia_here('downloaded'))) {
                        $DB->query("SELECT (SELECT IFNULL(sum(`Downloaded`), 0) FROM `users_freeleeches` WHERE `UserID`=$UserID)+(SELECT IFNULL(sum(`Downloaded`), 0) FROM `users_freetorrents` where `UserID`=$UserID)");
                        list($AllDownloaded) = $DB->next_record();
                        $AllDownloaded += $Downloaded;
                    ?>
                        <li class="<?= ($Override === 2 ? 'paranoia_override' : '') ?>"><?= Lang::get('user', 'downloaded') ?>: <span class="tooltip" title="<?= Format::get_size($Downloaded, 5) ?>"><?= Format::get_size($Downloaded) ?></span> <span class="tooltip" title="<?= Lang::get('user', 'true_downloaded_title') ?>">(<?= Format::get_size($AllDownloaded) ?>)</span></li>
                    <?
                    }
                    if (($Override = (check_paranoia_here('uploaded') && check_paranoia_here('downloaded')))) {
                    ?>
                        <li class="tooltip<?= ($Override === 2 ? ' paranoia_override' : '') ?>" title="<?= Format::get_size($Uploaded - $Downloaded, 5) ?>"><?= Lang::get('user', 'buffer') ?>: <?= Format::get_size($Uploaded / 0.65 - $Downloaded) ?></li>
                    <?
                    }
                    if (($Override = check_paranoia_here('ratio'))) {
                    ?>
                        <li <?= ($Override === 2 ? ' class="paranoia_override"' : '') ?>><?= Lang::get('user', 'ratio') ?>: <?= Format::get_ratio_html($Uploaded, $Downloaded) ?></li>
                    <?
                    }
                    if (($Override = check_paranoia_here('requiredratio')) && isset($RequiredRatio)) {
                    ?>
                        <li <?= ($Override === 2 ? ' class="paranoia_override"' : '') ?>><?= Lang::get('user', 'required_ratio') ?>: <span class="tooltip" title="<?= number_format((float)$RequiredRatio, 5) ?>"><?= number_format((float)$RequiredRatio, 2) ?></span></li>
                    <?
                    }
                    if (($Override = check_paranoia_here('bonuspoints')) && isset($BonusPoints)) {
                    ?>
                        <li <?= ($Override === 2 ? ' class="paranoia_override"' : '') ?>><?= Lang::get('user', 'bonus_points') ?>: <?= number_format($BonusPoints) ?><?
                                                                                                                                                                        if (check_perms('admin_bp_history')) {
                                                                                                                                                                            printf('&nbsp;<a href="bonus.php?action=history&amp;id=%d" class="brackets">View</a>', $UserID);
                                                                                                                                                                        }
                                                                                                                                                                        ?></li>
                        <li <?= ($Override === 2 ? ' class="paranoia_override"' : '') ?>><a href="bonus.php?action=bprates&userid=<?= $UserID ?>"><?= Lang::get('user', 'bprates') ?></a>: <?= number_format($BonusPointsPerHour) ?></li>
                    <?php
                    }
                    if ($OwnProfile || ($Override = check_paranoia_here(false)) || check_perms('users_mod')) {
                    ?>
                        <li class="tooltip" <?= ($Override === 2 ? ' class="paranoia_override"' : '') ?> <?
                                                                                                            $DB->query("select count(*), EndTime from tokens_typed where UserID=" . $UserID . " and Type='time' group by EndTime");
                                                                                                            $TimeAndCnts = $DB->to_array(false, MYSQLI_NUM, false);
                                                                                                            if (count($TimeAndCnts) > 0) echo "title=\"";
                                                                                                            $num = 0;
                                                                                                            foreach ($TimeAndCnts as $TAC) {
                                                                                                                if ($num != 0) echo "\n";
                                                                                                                $num += $TAC[0];
                                                                                                                echo $TAC[0] . " (" . $TAC[1] . Lang::get('user', 'space_expired');
                                                                                                            }
                                                                                                            if ($num != 0) echo "\"";
                                                                                                            ?>><a href="userhistory.php?action=token_history&amp;userid=<?= $UserID ?>"><?= Lang::get('user', 'token_number') ?></a>: <?
                                                                                                                                                                                                                                        echo $num == 0 ? number_format($FLTokens) : number_format($FLTokens - $num) . '+' . number_format($num);
                                                                                                                                                                                                                                        ?></li>
                    <?
                    }
                    if (($OwnProfile || check_perms('users_mod')) && $Warned != '0000-00-00 00:00:00') {
                    ?>
                        <li><?= ($Override === 2 ? ' class="paranoia_override"' : '') ?>><?= Lang::get('user', 'warning_expires_in') ?>: <?= time_diff((date('Y-m-d H:i', strtotime($Warned)))) ?></li>
                    <?  } ?>
                </ul>
            </div>
            <? if ($OwnProfile || check_perms('users_override_paranoia')) { ?>
                <?
                $DB->query("SELECT xs.uid, xs.tstamp, xs.fid, t.Size 
FROM xbt_snatched AS xs left join torrents AS t ON t.ID = xs.fid 
WHERE xs.uid =" . $UserID . " and xs.tstamp >= unix_timestamp(date_format(now(),'%Y-%m-01')) order by 2");
                $Requests = $DB->to_array();
                $SnatchedByUser;
                foreach ($Requests as $Request) {
                    list($userID, $Time, $TorrentID, $Size) = $Request;
                    if (!isset($SnatchedByUser[$userID][$TorrentID])) {
                        $SnatchedByUser[$userID][$TorrentID]['size'] = $Size;
                        $SnatchedByUser[$userID][$TorrentID]['free'] = 0;
                        $SnatchedByUser[$userID][$TorrentID]['unfree'] = 0;
                    }
                    $SnatchedByUser[$userID][$TorrentID]['time'][] = $Time;
                }
                $DB->query("SELECT `UserID`, `TorrentID`, unix_timestamp(`Time`) 
                FROM `users_freeleeches_time` 
                WHERE UserID=$UserID and unix_timestamp(Time) >= unix_timestamp(date_format(now(),'%Y-%m-01')) order by 3");
                $Requests = $DB->to_array();
                $TokenByUser;
                foreach ($Requests as $Request) {
                    list($UserID, $TorrentID, $Time) = $Request;
                    $TokenByUser[$UserID][$TorrentID][] = $Time;
                }
                $UsersCnt;
                foreach ($SnatchedByUser as $UserID => &$Torrents) {
                    $UsersCnt[$UserID]['size'] = 0;
                    $UsersCnt[$UserID]['cnt'] = 0;
                    foreach ($Torrents as $TorrentID => &$Torrent) {
                        if (isset($TokenByUser[$UserID][$TorrentID])) {
                            foreach ($Torrent['time'] as $Time) {
                                $free = false;
                                foreach ($TokenByUser[$UserID][$TorrentID] as $key => $TokenTime) {
                                    if ($Time < $TokenTime + 345600) {
                                        unset($TokenByUser[$UserID][$TorrentID][$key]);
                                        $Torrent['free'] += 1;
                                        $free = true;
                                        break;
                                    }
                                }
                                if (!$free) {
                                    $Torrent['unfree'] += 1;
                                }
                            }
                        } else {
                            $Torrent['unfree'] = 1;
                        }
                    }
                }
                $DB->query("select u.ID, u.Downloaded ND, l.Downloaded LD, TorrentCnt LT from users_main as u left join users_last_month as l on u.ID=l.ID where u.ID = $UserID");
                $Requests = $DB->to_array();
                foreach ($Requests as $User) {
                    list($ID, $ND, $LD, $LT) = $User;
                    $UsersCnt[$ID]['dt'] = $ND - $LD;
                }
                unset($Torrents, $Torrent);
                foreach ($SnatchedByUser as $UserID => $Torrents) {
                    foreach ($Torrents as $Torrent) {
                        if ($Torrent['unfree']) {
                            $UsersCnt[$UserID]['size'] += $Torrent['size'];
                            if ($Torrent['size']) {
                                $UsersCnt[$UserID]['cnt']++;
                            }
                        }
                    }
                }



                $Criteria = array();
                $Criteria[] = array('ddt' => 500 * 250 * 1024 * 1024, 'tdt' => 500, 'token' => 50, 'bonus' => 6000);
                $Criteria[] = array('ddt' => 320 * 250 * 1024 * 1024, 'tdt' => 320, 'token' => 32, 'bonus' => 2800);
                $Criteria[] = array('ddt' => 200 * 250 * 1024 * 1024, 'tdt' => 200, 'token' => 20, 'bonus' => 1300);
                $Criteria[] = array('ddt' => 120 * 250 * 1024 * 1024, 'tdt' => 120, 'token' => 12, 'bonus' => 600);
                $Criteria[] = array('ddt' => 60 * 250 * 1024 * 1024, 'tdt' => 60, 'token' => 6, 'bonus' => 240);
                $Criteria[] = array('ddt' => 25 * 250 * 1024 * 1024, 'tdt' => 25, 'token' => 2, 'bonus' => 100);
                $Criteria[] = array('ddt' => 10 * 250 * 1024 * 1024, 'tdt' => 10, 'token' => 1, 'bonus' => 0);
                foreach ($UsersCnt as $UserID => $User) {
                    $LogSize = min($User['size'], $User['dt']);
                    foreach ($Criteria as $L) {
                        if ($LogSize >= $L['ddt'] && $User['cnt'] >= $L['tdt']) {
                            break;
                        }
                    }
                }
                ?>

            <? } ?>
            <div class="box box_info box_userinfo_percentile">
                <div class="head colhead_dark"><?= Lang::get('user', 'u_percentile') ?></div>
                <ul class="stats nobullet">
                    <? if (($Override = check_paranoia_here('uploaded'))) { ?>
                        <li class="tooltip<?= ($Override === 2 ? ' paranoia_override' : '') ?>" title="<?= Format::get_size($Uploaded) ?>"><?= Lang::get('user', 'u_uploaded') ?>: <?= $UploadedRank === false ? 'Server busy' : number_format($UploadedRank) ?></li>
                    <?
                    }
                    if (($Override = check_paranoia_here('downloaded'))) { ?>
                        <li class="tooltip<?= ($Override === 2 ? ' paranoia_override' : '') ?>" title="<?= Format::get_size($Downloaded) ?>"><?= Lang::get('user', 'u_downloaded') ?>: <?= $DownloadedRank === false ? 'Server busy' : number_format($DownloadedRank) ?></li>
                    <?
                    }
                    if (($Override = check_paranoia_here('uploads+'))) { ?>
                        <li class="tooltip<?= ($Override === 2 ? ' paranoia_override' : '') ?>" title="<?= number_format($Uploads) ?>"><?= Lang::get('user', 'u_uploads') ?>: <?= $UploadsRank === false ? 'Server busy' : number_format($UploadsRank) ?></li>
                    <?
                    }
                    if (($Override = check_paranoia_here('requestsfilled_count'))) { ?>
                        <li class="tooltip<?= ($Override === 2 ? ' paranoia_override' : '') ?>" title="<?= number_format($RequestsFilled) ?>"><?= Lang::get('user', 'u_filled') ?>: <?= $RequestRank === false ? 'Server busy' : number_format($RequestRank) ?></li>
                    <?
                    }
                    if (($Override = check_paranoia_here('requestsvoted_bounty'))) { ?>
                        <li class="tooltip<?= ($Override === 2 ? ' paranoia_override' : '') ?>" title="<?= Format::get_size($TotalSpent) ?>"><?= Lang::get('user', 'u_bounty') ?>: <?= $BountyRank === false ? 'Server busy' : number_format($BountyRank) ?></li>
                    <?  } ?>
                    <li class="tooltip" title="<?= number_format($ForumPosts) ?>"><?= Lang::get('user', 'u_post') ?>: <?= $PostRank === false ? 'Server busy' : number_format($PostRank) ?></li>
                    <? if (($Override = check_paranoia_here('artistsadded'))) { ?>
                        <li class="tooltip<?= ($Override === 2 ? ' paranoia_override' : '') ?>" title="<?= number_format($ArtistsAdded) ?>"><?= Lang::get('user', 'u_artist') ?>: <?= $ArtistsRank === false ? 'Server busy' : number_format($ArtistsRank) ?></li>
                    <?
                    }
                    if (check_paranoia_here(array('uploaded', 'downloaded', 'uploads+', 'requestsfilled_count', 'requestsvoted_bounty', 'artistsadded'))) { ?>
                        <li><strong><?= Lang::get('user', 'u_total') ?>: <?= $OverallRank === false ? 'Server busy' : number_format($OverallRank) ?></strong></li>
                    <?  } ?>
                </ul>
            </div>
            <?
            if ($OwnProfile || check_perms('users_override_paranoia', $Class)) {
                $DB->prepared_query("
			SELECT IRCKey
			FROM users_main
			WHERE ID = ?", $UserID);
                list($IRCKey) = $DB->next_record();
            }
            if (check_perms('users_mod', $Class) || check_perms('users_view_ips', $Class) || check_perms('users_view_keys', $Class)) {
                $DB->query("
			SELECT COUNT(*)
			FROM users_history_passwords
			WHERE UserID = '$UserID'");
                list($PasswordChanges) = $DB->next_record();
                if (check_perms('users_view_keys', $Class)) {
                    $DB->query("
				SELECT COUNT(*)
				FROM users_history_passkeys
				WHERE UserID = '$UserID'");
                    list($PasskeyChanges) = $DB->next_record();
                }
                if (check_perms('users_view_ips', $Class)) {
                    $DB->query("
				SELECT COUNT(DISTINCT IP)
				FROM users_history_ips
				WHERE UserID = '$UserID'");
                    list($IPChanges) = $DB->next_record();
                    $DB->query("
				SELECT COUNT(DISTINCT IP)
				FROM xbt_snatched
				WHERE uid = '$UserID'
					AND IP != ''");
                    list($TrackerIPs) = $DB->next_record();
                }
                if (check_perms('users_view_email', $Class)) {
                    $DB->query("
				SELECT COUNT(*)
				FROM users_history_emails
				WHERE UserID = '$UserID'");
                    list($EmailChanges) = $DB->next_record();
                }
            ?>
                <div class="box box_info box_userinfo_history">
                    <div class="head colhead_dark"><?= Lang::get('user', 'history') ?></div>
                    <ul class="stats nobullet">
                        <? if (check_perms('users_view_email', $Class)) { ?>
                            <li><?= Lang::get('user', 'emails') ?>: <?= number_format($EmailChanges) ?> <a href="userhistory.php?action=email2&amp;userid=<?= $UserID ?>" class="brackets"><?= Lang::get('user', 'view') ?></a>&nbsp;&nbsp;<a href="userhistory.php?action=email&amp;userid=<?= $UserID ?>" class="brackets"><?= Lang::get('user', 'legacy_view') ?></a></li>
                        <?
                        }
                        if (check_perms('users_view_ips', $Class)) {
                        ?>
                            <li>IPs: <?= number_format($IPChanges) ?> <a href="userhistory.php?action=ips&amp;userid=<?= $UserID ?>" class="brackets"><?= Lang::get('user', 'view') ?></a>&nbsp;&nbsp;<a href="userhistory.php?action=ips&amp;userid=<?= $UserID ?>&amp;usersonly=1" class="brackets"><?= Lang::get('user', 'view_users') ?></a></li>
                            <? if (check_perms('users_view_ips', $Class) && check_perms('users_mod', $Class)) { ?>
                                <li>Tracker IPs: <?= number_format($TrackerIPs) ?> <a href="userhistory.php?action=tracker_ips&amp;userid=<?= $UserID ?>" class="brackets"><?= Lang::get('user', 'view') ?></a></li>
                            <?
                            }
                        }
                        if (check_perms('users_view_keys', $Class)) {
                            ?>
                            <li><?= Lang::get('user', 'passkeys') ?>: <?= number_format($PasskeyChanges) ?> <a href="userhistory.php?action=passkeys&amp;userid=<?= $UserID ?>" class="brackets"><?= Lang::get('user', 'view') ?></a></li>
                        <?
                        }
                        if (check_perms('users_mod', $Class)) {
                        ?>
                            <li><?= Lang::get('user', 'passwords') ?>: <?= number_format($PasswordChanges) ?> <a href="userhistory.php?action=passwords&amp;userid=<?= $UserID ?>" class="brackets"><?= Lang::get('user', 'view') ?></a></li>
                            <li><?= Lang::get('user', 'stats') ?>: N/A <a href="userhistory.php?action=stats&amp;userid=<?= $UserID ?>" class="brackets"><?= Lang::get('user', 'view') ?></a></li>
                        <?      } ?>
                    </ul>
                </div>
            <?  } ?>
            <div class="box box_info box_userinfo_personal">
                <div class="head colhead_dark"><?= Lang::get('user', 'p_personal') ?></div>
                <ul class="stats nobullet">
                    <li><?= Lang::get('user', 'p_class') ?>: <?= $ClassLevels[$Class]['Name'] ?></li>
                    <?
                    $UserInfo = Users::user_info($UserID);
                    if (!empty($UserInfo['ExtraClasses'])) {
                    ?>
                        <li>
                            <ul class="stats">
                                <?
                                foreach ($UserInfo['ExtraClasses'] as $PermID => $Val) {
                                ?>
                                    <li><?= $Classes[$PermID]['Name'] ?></li>
                                <?  } ?>
                            </ul>
                        </li>
                    <?
                    }
                    // An easy way for people to measure the paranoia of a user, for e.g. contest eligibility
                    if ($ParanoiaLevel == 0) {
                        $ParanoiaLevelText = 'Off';
                    } elseif ($ParanoiaLevel == 1) {
                        $ParanoiaLevelText = 'Very Low';
                    } elseif ($ParanoiaLevel <= 5) {
                        $ParanoiaLevelText = 'Low';
                    } elseif ($ParanoiaLevel <= 20) {
                        $ParanoiaLevelText = 'High';
                    } else {
                        $ParanoiaLevelText = 'Very high';
                    }
                    ?>
                    <li><?= Lang::get('user', 'p_paranoiaLevel') ?>: <span class="tooltip" title="<?= $ParanoiaLevel ?>"><?= $ParanoiaLevelText ?></span></li>
                    <? if ((check_perms('users_view_email') && in_array("emailshowtotc", $Paranoia)) || check_perms("users_override_paranoia") || $OwnProfile) { ?>
                        <li><?= Lang::get('user', 'p_email') ?>: <a href="mailto:<?= display_str($Email) ?>"><?= display_str($Email) ?></a>
                            <? if (check_perms('users_view_email', $Class)) { ?>
                                <a href="user.php?action=search&amp;email_history=on&amp;email=<?= display_str($Email) ?>" title="Search" class="brackets tooltip">S</a>
                            <?      } ?>
                        </li>
                    <?  }

                    if (check_perms('users_view_ips', $Class)) {
                    ?>
                        <li><?= Lang::get('user', 'p_ip') ?>: <?= Tools::display_ip($IP) ?></li>
                        <li><?= Lang::get('user', 'p_host') ?>: <?= Tools::get_host_by_ajax($IP) ?></li>
                    <?
                    }

                    if (check_perms('users_view_keys', $Class) || $OwnProfile) {
                    ?>
                        <li><?= Lang::get('user', 'p_passkey') ?>: <a href="#" id="passkey" onclick="togglePassKey('<?= display_str($torrent_pass) ?>'); return false;" class="brackets"><?= Lang::get('user', 'view') ?></a></li>
                        <?
                    }
                    if (check_perms('users_view_invites') || $OwnProfile) {
                        if (check_perms('users_view_invites')) {
                            if (!$InviterID) {
                                $Invited = '<span style="font-style: italic;">Nobody</span>';
                            } else {
                                $Invited = "<a href=\"user.php?id=$InviterID\">$InviterName</a>";
                            } ?>
                            <li><?= Lang::get('user', 'p_inviter') ?>: <?= $Invited ?></li><?
                                                                                        } ?>
                        <li <?
                            $DB->query("select count(*), EndTime from invites_typed where UserID=" . $UserID . " and Type='time' and Used=0 group by EndTime");
                            $TimeAndCnts = $DB->to_array(false, MYSQLI_NUM, false);
                            if (count($TimeAndCnts) > 0) echo "title=\"";
                            $num = 0;
                            foreach ($TimeAndCnts as $TAC) {
                                if ($num != 0) echo "\n";
                                $num += $TAC[0];
                                echo $TAC[0] . " (" . $TAC[1] . Lang::get('user', 'space_expired');
                            }
                            if ($num != 0) echo "\"";
                            ?>><?= Lang::get('user', 'p_invites') ?>: <?
                                                                        $DB->query("
					SELECT COUNT(InviterID)
					FROM invites
					WHERE InviterID = '$UserID'");
                                                                        list($Pending) = $DB->next_record();
                                                                        if ($DisableInvites) {
                                                                            echo 'X';
                                                                        } else {
                                                                            echo $num == 0 ? number_format($Invites) : number_format($Invites - $num) . '+' . number_format($num);
                                                                        }
                                                                        echo " ($Pending)";
                                                                        ?></li>
                    <?
                    }
                    if (Applicant::user_is_applicant($UserID) && (check_perms('admin_manage_applicants') || $OwnProfile)) {
                    ?>
                        <li><?= Lang::get('user', 'p_inviter') ?>: <a href="/apply.php?action=view" class="brackets"><?= Lang::get('user', 'view') ?></a></li>
                    <?
                    }

                    if (!isset($SupportFor)) {
                        $DB->query('
		SELECT SupportFor
		FROM users_info
		WHERE UserID = ' . $LoggedUser['ID']);
                        list($SupportFor) = $DB->next_record();
                    }
                    if ($Override = check_perms('users_mod') || $OwnProfile || !empty($SupportFor)) {
                    ?>
                        <li <?= (($Override === 2 || $SupportFor) ? ' class="paranoia_override"' : '') ?>><?= Lang::get('user', 'p_clients') ?>: <?
                                                                                                                                                    $DB->query("
			SELECT DISTINCT useragent
			FROM xbt_files_users
			WHERE uid = $UserID");
                                                                                                                                                    $Clients = $DB->collect(0);
                                                                                                                                                    echo implode('; ', $Clients);
                                                                                                                                                    ?></li>
                    <?
                    }

                    if ($OwnProfile || check_perms('users_mod')) {
                        $DB->query("SELECT MAX(uhp.ChangeTime), ui.JoinDate
				FROM users_info ui
				LEFT JOIN users_history_passwords uhp ON uhp.UserID = $UserID
				WHERE ui.UserID = $UserID");
                        list($PasswordHistory, $JoinDate) = G::$DB->next_record();
                        $Age = (empty($PasswordHistory)) ? time_diff($JoinDate) : time_diff($PasswordHistory);
                        $PasswordAge = substr($Age, 0, strpos($Age, " ago"));
                    ?>
                        <li><?= Lang::get('user', 'p_passwordage') ?>: <?= $PasswordAge ?></li>
                    <? }
                    if ($OwnProfile || check_perms('users_override_paranoia', $Class)) { ?>
                        <li><?= Lang::get('user', 'p_irc') ?>: <?= empty($IRCKey) ? Lang::get('user', 'irc_no') : Lang::get('user', 'irc_yes') ?></li>
                    <? } ?>
                </ul>
            </div>

            <?
            // TODO 丧心病狂，这里又定义一遍，展示在个人页上
            $NextLevel = array();
            $NextLevel[$Classes[USER]['Level']] = array(
                'To' => $Classes[MEMBER]['Name'],
                'MinUpload' => 0,
                'MinDownload' => 80 * 1024 * 1024 * 1024,
                'MinRatio' => 0.8,
                'MinUploads' => 0,
                'MaxTime' => time_minus(3600 * 24 * 7)
            );
            $NextLevel[$Classes[MEMBER]['Level']] = array(
                'To' => $Classes[POWER]['Name'],
                'MinUpload' => 0,
                'MinDownload' => 200 * 1024 * 1024 * 1024,
                'MinRatio' => 1.2,
                'MinUploads' => 1,
                'MaxTime' => time_minus(3600 * 24 * 7 * 2)
            );
            $NextLevel[$Classes[POWER]['Level']] = array(
                'To' => $Classes[ELITE]['Name'],
                'MinUpload' => 0,
                'MinDownload' => 500 * 1024 * 1024 * 1024,
                'MinRatio' => 1.2,
                'MinUploads' => 25,
                'MaxTime' => time_minus(3600 * 24 * 7 * 4)
            );
            $NextLevel[$Classes[ELITE]['Level']] = array(
                'To' => $Classes[TORRENT_MASTER]['Name'],
                'MinUpload' => 0,
                'MinDownload' => 1 * 1024 * 1024 * 1024 * 1024,
                'MinRatio' => 1.2,
                'MinUploads' => 100,
                'MaxTime' => time_minus(3600 * 24 * 7 * 8)
            );
            $NextLevel[$Classes[TORRENT_MASTER]['Level']] = array(
                'To' => $Classes[POWER_TM]['Name'],
                'MinUpload' => 0,
                'MinDownload' => 2 * 1024 * 1024 * 1024 * 1024,
                'MinRatio' => 1.2,
                'MinUploads' => 250,
                'MaxTime' => time_minus(3600 * 24 * 7 * 12)
            );
            $NextLevel[$Classes[POWER_TM]['Level']] = array(
                'To' => $Classes[ELITE_TM]['Name'],
                'MinUpload' => 0,
                'MinDownload' => 5 * 1024 * 1024 * 1024 * 1024,
                'MinRatio' => 1.2,
                'MinUploads' => 500,
                'MaxTime' => time_minus(3600 * 24 * 7 * 16),
            );

            $NextLevel[$Classes[ELITE_TM]['Level']] = array(
                'To' => $Classes[GURU]['Name'],
                'MinUpload' => 0,
                'MinDownload' => 10 * 1024 * 1024 * 1024 * 1024,
                'MinRatio' => 1.2,
                'MinUploads' => 1000,
                'MaxTime' => time_minus(3600 * 24 * 7 * 20),
            );

            if (isset($NextLevel[$Class]) && $OwnProfile) {
            ?>
                <div class="box box_info box_userinfo_nextclass">
                    <div class="head colhead_dark"><?= Lang::get('user', 'next_userclass') ?></div>
                    <ul class="stats nobullet">
                        <li><?= Lang::get('user', 'next_userclass_title1') ?>: <?= $NextLevel[$Class]['To'] ?></li>
                        <li><?= Lang::get('user', 'next_userclass_title6') ?>: <?
                                                                                $p = $AllDownloaded / $NextLevel[$Class]['MinDownload'] * 100;
                                                                                echo Format::get_size($AllDownloaded) . ' / ' . Format::get_size($NextLevel[$Class]['MinDownload']) . " (<span class=\"" . ($p >= 100 ? "important_text_alt\">" : "important_text\">") . number_format($p) . "%</span>)"
                                                                                ?></li>
                        <li><?= Lang::get('user', 'next_userclass_title3') ?>: <?
                                                                                $p = $Uploaded / $Downloaded / $NextLevel[$Class]['MinRatio'] * 100;
                                                                                echo number_format($Uploaded / $Downloaded, 2) . ' / ' . $NextLevel[$Class]['MinRatio'] . " (<span class=\"" . ($p >= 100 ? "important_text_alt\">" : "important_text\">") . number_format($p) . "%</span>)"
                                                                                ?></li>
                        <li><?= Lang::get('user', 'next_userclass_title4') ?>: <?
                                                                                //$p = $JoinDate / $NextLevel[$Class]['MaxTime'] * 100;
                                                                                echo time_diff($JoinDate, $Levels = 2, $Span = true, $Lowercase = false, $StartTime = false, $HideAgo = true) . ' / ' . time_diff($NextLevel[$Class]['MaxTime'], $Levels = 2, $Span = true, $Lowercase = false, $StartTime = false, $HideAgo = true)
                                                                                ?></li>
                        <?
                        $DB->query("SELECT COUNT(ID)
				FROM torrents
				WHERE UserID = " . $UserID);
                        list($MinUploads) = $DB->next_record();
                        // test
                        $p = $MinUploads / $NextLevel[$Class]['MinUploads'] * 100;
                        echo "<li>" . Lang::get('user', 'next_userclass_title5') . ": $MinUploads / " . $NextLevel[$Class]['MinUploads'];
                        echo ($NextLevel[$Class]['MinUploads'] ? (" (<span class=\"" . ($p >= 100 ? "important_text_alt\">" : "important_text\">") . number_format($p) . "%</span>)") : "") . "</li>";
                        ?>
                    </ul>
                </div>
            <?
            }



            $CanViewUploads = check_perms("users_view_uploaded");
            if (check_paranoia_here('uploads+') || $CanViewUploads) {
                $DB->query("
		SELECT COUNT(ID)
		FROM torrents
		WHERE UserID = '$UserID'");
                list($Uploads) = $DB->next_record();
            } else {
                $Uploads = 0;
            }



            if ($Downloaded == 0) {
                $Ratio = 1;
            } elseif ($Uploaded == 0) {
                $Ratio = 0.5;
            } else {
                $Ratio = round($Uploaded / $Downloaded, 2);
            }
            $OverallRank = UserRank::overall_score($UploadedRank, $DownloadedRank, $UploadsRank, $RequestRank, $PostRank, $BountyRank, $ArtistsRank, $Ratio);

            ?>

            <?
            include(SERVER_ROOT . '/sections/user/community_stats.php');

            DonationsView::render_donor_stats($OwnProfile, $donationInfo, $leaderBoardRank, $donorVisible, $isDonor);
            ?>
        </div>
        <div class="main_column">
            <?
            if (
                $RatioWatchEnds != '0000-00-00 00:00:00'
                && (time() < strtotime($RatioWatchEnds))
                && ($Downloaded * $RequiredRatio) > $Uploaded
            ) {
            ?>
                <div class="box">
                    <div class="head"><?= Lang::get('user', 'ratio_watch') ?></div>
                    <div class="pad">This user is currently on ratio watch and must upload <?= Format::get_size(($Downloaded * $RequiredRatio) - $Uploaded) ?> in the next <?= time_diff($RatioWatchEnds) ?>, or their leeching privileges will be revoked. Amount downloaded while on ratio watch: <?= Format::get_size($Downloaded - $RatioWatchDownload) ?></div>
                </div>
            <?
            }
            $WearOrDisplay = Badges::get_wear_badges($UserID);
            $BadgesLabels = Badges::get_badge_labels();
            $BadgesByUserID = Badges::get_badges_by_userid($UserID);
            $MaxBadges = array();
            foreach ($BadgesByUserID as $BadgeID => $BadgeInfo) {
                $Badge = Badges::get_badges_by_id($BadgeID);
                if (isset($MaxBadges[$Badge['Label']])) {
                    if ($MaxBadges[$Badge['Label']]['Level'] < $Badge['Level']) {
                        $MaxBadges[$Badge['Label']] = array('ID' => $BadgeID, 'Level' => $Badge['Level']);
                    }
                } else {
                    $MaxBadges[$Badge['Label']] = array('ID' => $BadgeID, 'Level' => $Badge['Level']);
                }
            }
            if (ENABLE_BADGE) {
            ?>
                <div class="box">
                    <script>
                        var i = 0;

                        function badgesDisplay() {
                            switch (i) {
                                case 0:
                                    i++
                                    $(".badge_display").show()
                                    break
                                case 1:
                                    $("#badge_display_all").hide();
                                    i++
                                    break
                                case 2:
                                    $(".badge_display").hide()
                                    i = 0
                                    break
                            }
                        }
                    </script>

                    <div class="head"><a href="/badges.php"><?= Lang::get('user', 'badge_center') ?></a>
                        <span style="float: right;"><a href="#" onclick="badgesDisplay()" class="brackets"><?= Lang::get('global', 'hide') ?></a></span>
                    </div>

                    <div id="badge_display_head" class="pad badge_display">
                        <?

                        foreach ($WearOrDisplay['Profile'] as $BadgeID) {
                            $Badge = Badges::get_badges_by_id($BadgeID);
                        ?>
                            <div class="badge_container">
                                <img src="<?= $Badge['BigImage'] ?>" title="<?= Badges::get_text($Badge['Label'], "badge_name") ?>">
                            </div>

                        <?
                        }
                        if ($OwnProfile && count($WearOrDisplay['Profile']) == 0) {
                            if (count($BadgesByUserID) == 0) {
                                echo '<span>' . Lang::get('badges', 'you_do_not_have_any_badge') . '</span>';
                            } else {
                                echo '<span>' . Lang::get('badges', 'you_do_not_display_any_badge') . '</span>';
                            }
                        }
                        ?>
                    </div>
                    <div id="badge_display_all" class="pad badge_display" style="display: none;">
                        <?
                        if (check_paranoia_here('badgedisplay') || check_perms('users_override_paranoia')) {
                            foreach ($MaxBadges as $Label => $BadgeInfo) {
                                if (in_array($BadgeInfo['ID'], $WearOrDisplay['Profile'])) continue;
                                $Badge = Badges::get_badges_by_id($BadgeInfo['ID']);
                        ?>
                                <div class="badge_container">
                                    <img src="<?= $Badge['BigImage'] ?>" title="<?= Badges::get_text($Label, "badge_name") ?>">
                                </div>

                        <?
                            }
                        }
                        ?>
                    </div>




                </div>
            <?
            }
            ?>

            <div class="box">
                <div class="head">
                    <?= !empty($InfoTitle) ? $InfoTitle : Lang::get('user', 'infotitle'); ?>
                    <span style="float: right;"><a href="#" onclick="$('#profilediv').gtoggle(); this.innerHTML = (this.innerHTML == '<?= Lang::get('global', 'hide') ?>' ? '<?= Lang::get('global', 'show') ?>' : '<?= Lang::get('global', 'hide') ?>'); return false;" class="brackets"><?= Lang::get('global', 'hide') ?></a></span>
                </div>
                <div class="pad profileinfo" id="profilediv">
                    <?
                    if (!$Info) {
                    ?>
                        <?= Lang::get('user', 'no_infotitle') ?>
                    <?
                    } else {
                        echo Text::full_format($Info);
                    }
                    ?>
                </div>
            </div>
            <?
            DonationsView::render_profile_rewards($EnabledRewards, $ProfileRewards);

            if (check_paranoia_here('snatched')) {
                $RecentSnatches = $Cache->get_value("recent_snatches_$UserID");
                if ($RecentSnatches === false) {
                    $DB->query("
			SELECT
				g.ID,
				g.Name,
				g.WikiImage,
                g.SubName,
                g.Year
			FROM xbt_snatched AS s
				INNER JOIN torrents AS t ON t.ID = s.fid
				INNER JOIN torrents_group AS g ON t.GroupID = g.ID
			WHERE s.uid = '$UserID'
				AND t.UserID != '$UserID'
				AND g.CategoryID = '1'
				AND g.WikiImage != ''
			GROUP BY g.ID
			ORDER BY s.tstamp DESC
			LIMIT 5");
                    $RecentSnatches = $DB->to_array();
                    $Cache->cache_value("recent_snatches_$UserID", $RecentSnatches, 0); //inf cache
                }
                if (!empty($RecentSnatches)) {
            ?>
                    <table class="layout recent" id="recent_snatches" cellpadding="0" cellspacing="0" border="0">
                        <tr class="colhead">
                            <td colspan="5">
                                <?= Lang::get('user', 'last_torrents') ?>
                            </td>
                        </tr>
                        <tr>
                            <? foreach ($RecentSnatches as $RS) {
                                $Name = Torrents::torrent_group_name($RS, true);
                            ?>
                                <td>
                                    <a href="torrents.php?id=<?= $RS['ID'] ?>">
                                        <img class="tooltip" title="<?= $Name ?>" src="<?= ImageTools::process($RS['WikiImage'], true) ?>" alt="<?= $Name ?>" width="107" />
                                    </a>
                                </td>
                            <?      } ?>
                        </tr>
                    </table>
                <?
                }
            }

            if (check_paranoia_here('uploads')) {
                $RecentUploads = $Cache->get_value("recent_uploads_$UserID");
                if ($RecentUploads === false) {
                    $DB->query("
			SELECT
				g.ID,
				g.Name,
                g.SubName,
                g.Year,
				g.WikiImage
			FROM torrents_group AS g
				INNER JOIN torrents AS t ON t.GroupID = g.ID
			WHERE t.UserID = '$UserID'
				AND g.CategoryID = '1'
				AND g.WikiImage != ''
			GROUP BY g.ID
			ORDER BY t.Time DESC
			LIMIT 5");
                    $RecentUploads = $DB->to_array();
                    $Cache->cache_value("recent_uploads_$UserID", $RecentUploads, 0); //inf cache
                }
                if (!empty($RecentUploads)) {
                ?>
                    <table class="layout recent" id="recent_uploads" cellpadding="0" cellspacing="0" border="0">
                        <tr class="colhead">
                            <td colspan="5">
                                <?= Lang::get('user', 'last_uploads') ?>
                            </td>
                        </tr>
                        <tr>
                            <? foreach ($RecentUploads as $RU) {
                                $Name = Torrents::torrent_group_name($RU, true);
                            ?>
                                <td>
                                    <a href="torrents.php?id=<?= $RU['ID'] ?>">
                                        <img class="tooltip" title="<?= $Name ?>" src="<?= ImageTools::process($RU['WikiImage'], true) ?>" alt="<?= $Name ?>" width="107" />
                                    </a>
                                </td>
                            <?      } ?>
                        </tr>
                    </table>
                <?
                }
            }

            $DB->query("
	SELECT ID, Name
	FROM collages
	WHERE UserID = '$UserID'
		AND CategoryID = '0'
		AND Deleted = '0'
	ORDER BY Featured DESC,
		Name ASC");
            $Collages = $DB->to_array(false, MYSQLI_NUM, false);
            $FirstCol = true;
            foreach ($Collages as $CollageInfo) {
                list($CollageID, $CName) = $CollageInfo;
                $DB->query("
		SELECT ct.GroupID,
			tg.WikiImage,
			tg.CategoryID
		FROM collages_torrents AS ct
			JOIN torrents_group AS tg ON tg.ID = ct.GroupID
		WHERE ct.CollageID = '$CollageID'
		ORDER BY ct.Sort
		LIMIT 5");
                $Collage = $DB->to_array(false, MYSQLI_ASSOC, false);
                ?>
                <table class="layout recent" id="collage<?= $CollageID ?>_box" cellpadding="0" cellspacing="0" border="0">
                    <tr class="colhead">
                        <td colspan="5">
                            <span style="float: left;">
                                <?= display_str($CName) ?> - <a href="collages.php?id=<?= $CollageID ?>" class="brackets">See full</a>
                            </span>
                            <span style="float: right;">
                                <a href="#" onclick="$('#collage<?= $CollageID ?>_box .images').gtoggle(); this.innerHTML = (this.innerHTML == 'Hide' ? 'Show' : 'Hide'); return false;" class="brackets"><?= $FirstCol ? 'Hide' : 'Show' ?></a>
                            </span>
                        </td>
                    </tr>
                    <tr class="images<?= $FirstCol ? '' : ' hidden' ?>">
                        <? foreach ($Collage as $C) {
                            $Group = Torrents::get_groups(array($C['GroupID']), true, true, false);
                            extract(Torrents::array_group($Group[$C['GroupID']]));
                            $Name = Torrents::torrent_group_name($Group, true);

                        ?>
                            <td>
                                <a href="torrents.php?id=<?= $GroupID ?>">
                                    <img class="tooltip" title="<?= $Name ?>" src="<?= ImageTools::process($C['WikiImage'], true) ?>" alt="<?= $Name ?>" width="107" />
                                </a>
                            </td>
                        <?  } ?>
                    </tr>
                </table>
            <?
                $FirstCol = false;
            }
            ?>
            <!-- for the "jump to staff tools" button -->
            <a id="staff_tools"></a>
            <?

            // Linked accounts
            if (check_perms('users_mod')) {
                include(SERVER_ROOT . '/sections/user/linkedfunctions.php');
                user_dupes_table($UserID);
            }

            if ((check_perms('users_view_invites')) && $Invited > 0) {
                include(SERVER_ROOT . '/classes/invite_tree.class.php');
                $Tree = new INVITE_TREE($UserID, array('visible' => false));
            ?>
                <div class="box" id="invitetree_box">
                    <div class="head">
                        <?= Lang::get('user', 'invite_tree') ?> <a href="#" onclick="$('#invitetree').gtoggle(); return false;" class="brackets"><?= Lang::get('user', 'view') ?></a>
                    </div>
                    <div id="invitetree" class="hidden">
                        <? $Tree->make_tree(); ?>
                    </div>
                </div>
                <?
            }

            if (check_perms('users_mod')) {
                DonationsView::render_donation_history($donation->history($UserID));
            }

            // Requests
            if (empty($LoggedUser['DisableRequests']) && check_paranoia_here('requestsvoted_list')) {
                $SphQL = new SphinxqlQuery();
                $SphQLResult = $SphQL->select('id, votes, bounty')
                    ->from('requests, requests_delta')
                    ->where('userid', $UserID)
                    ->where('torrentid', 0)
                    ->order_by('votes', 'desc')
                    ->order_by('bounty', 'desc')
                    ->limit(0, 100, 100) // Limit to 100 requests
                    ->query();
                if ($SphQLResult->has_results()) {
                    $SphRequests = $SphQLResult->to_array('id', MYSQLI_ASSOC);
                ?>
                    <div class="box" id="requests_box">
                        <div class="head">
                            <?= Lang::get('global', 'requests') ?> <a href="#" onclick="$('#requests').gtoggle(); return false;" class="brackets"><?= Lang::get('user', 'view') ?></a>
                        </div>
                        <div id="requests" class="request_table hidden">
                            <table cellpadding="6" cellspacing="1" border="0" class="border" width="100%">
                                <tr class="colhead_dark">
                                    <td style="width: 48%;">
                                        <strong><?= Lang::get('user', 'name') ?></strong>
                                    </td>
                                    <td>
                                        <strong><?= Lang::get('user', 'vote') ?></strong>
                                    </td>
                                    <td>
                                        <strong><?= Lang::get('user', 'bounty') ?></strong>
                                    </td>
                                    <td>
                                        <strong><?= Lang::get('user', 'add_time') ?></strong>
                                    </td>
                                </tr>
                                <?
                                $Row = 'a';
                                $Requests = Requests::get_requests(array_keys($SphRequests));
                                foreach ($SphRequests as $RequestID => $SphRequest) {
                                    $Request = $Requests[$RequestID];
                                    $VotesCount = $SphRequest['votes'];
                                    $Bounty = $SphRequest['bounty'] * 1024; // Sphinx stores bounty in kB
                                    $CategoryName = $Categories[$Request['CategoryID'] - 1];

                                    if ($CategoryName == 'Music') {
                                        $ArtistForm = Requests::get_artists($RequestID);
                                        $ArtistLink = Artists::display_artists($ArtistForm, true, true);
                                        $FullName = "$ArtistLink<a href=\"requests.php?action=view&amp;id=$RequestID\">$Request[Title] [$Request[Year]]</a>";
                                    } elseif ($CategoryName == 'Audiobooks' || $CategoryName == 'Comedy') {
                                        $FullName = "<a href=\"requests.php?action=view&amp;id=$RequestID\">$Request[Title] [$Request[Year]]</a>";
                                    } else {
                                        $FullName = "<a href=\"requests.php?action=view&amp;id=$RequestID\">$Request[Title]</a>";
                                    }
                                ?>
                                    <tr class="row<?= $Row === 'b' ? 'a' : 'b' ?>">
                                        <td>
                                            <?= $FullName ?>
                                            <div class="tags">
                                                <?
                                                $Tags = $Request['Tags'];
                                                $TagList = array();
                                                foreach ($Tags as $TagID => $TagName) {
                                                    $TagList[] = "<a href=\"requests.php?tags=$TagName\">" . display_str($TagName) . '</a>';
                                                }
                                                $TagList = implode(', ', $TagList);
                                                ?>
                                                <?= $TagList ?>
                                            </div>
                                        </td>
                                        <td>
                                            <span id="vote_count_<?= $RequestID ?>"><?= $VotesCount ?></span>
                                            <? if (check_perms('site_vote')) { ?>
                                                &nbsp;&nbsp; <a href="javascript:Vote(0, <?= $RequestID ?>)" class="brackets">+</a>
                                            <?          } ?>
                                        </td>
                                        <td>
                                            <span id="bounty_<?= $RequestID ?>"><?= Format::get_size($Bounty) ?></span>
                                        </td>
                                        <td>
                                            <?= time_diff($Request['TimeAdded']) ?>
                                        </td>
                                    </tr>
                                <?      } ?>
                            </table>
                        </div>
                    </div>
                <?
                }
            }

            $IsFLS = isset($LoggedUser['ExtraClasses'][FLS_TEAM]);
            if (check_perms('users_mod', $Class) || $IsFLS) {
                $UserLevel = $LoggedUser['EffectiveClass'];
                $DB->query("
		SELECT
			SQL_CALC_FOUND_ROWS
			spc.ID,
			spc.Subject,
			spc.Status,
			spc.Level,
			spc.AssignedToUser,
			spc.Date,
			COUNT(spm.ID) AS Resplies,
			spc.ResolverID
		FROM staff_pm_conversations AS spc
		JOIN staff_pm_messages spm ON spm.ConvID = spc.ID
		WHERE spc.UserID = $UserID
			AND (spc.Level <= $UserLevel OR spc.AssignedToUser = '" . $LoggedUser['ID'] . "')
		GROUP BY spc.ID
		ORDER BY spc.Date DESC");
                if ($DB->has_results()) {
                    $StaffPMs = $DB->to_array();
                ?>
                    <div class="box" id="staffpms_box">
                        <div class="head">
                            <?= Lang::get('user', 'staff_note') ?> <a href="#" onclick="$('#staffpms').gtoggle(); return false;" class="brackets"><?= Lang::get('user', 'view') ?></a>
                        </div>
                        <table width="100%" class="message_table hidden" id="staffpms">
                            <tr class="colhead">
                                <td><?= Lang::get('user', 'subject') ?></td>
                                <td><?= Lang::get('user', 'date') ?></td>
                                <td><?= Lang::get('user', 'assigned_to') ?></td>
                                <td><?= Lang::get('user', 'replies') ?></td>
                                <td><?= Lang::get('user', 'resolved_by') ?></td>
                            </tr>
                            <?
                            foreach ($StaffPMs as $StaffPM) {
                                list($ID, $Subject, $Status, $Level, $AssignedToUser, $Date, $Replies, $ResolverID) = $StaffPM;
                                // Get assigned
                                if ($AssignedToUser == '') {
                                    // Assigned to class
                                    $Assigned = ($Level == 0) ? 'First Line Support' : $ClassLevels[$Level]['Name'];
                                    // No + on Sysops
                                    if ($Assigned != 'Sysop') {
                                        $Assigned .= '+';
                                    }
                                } else {
                                    // Assigned to user
                                    $Assigned = Users::format_username($UserID, true, true, true, true);
                                }

                                if ($ResolverID) {
                                    $Resolver = Users::format_username($ResolverID, true, true, true, true);
                                } else {
                                    $Resolver = '(unresolved)';
                                }

                            ?>
                                <tr>
                                    <td><a href="staffpm.php?action=viewconv&amp;id=<?= $ID ?>"><?= display_str($Subject) ?></a></td>
                                    <td><?= time_diff($Date, 2, true) ?></td>
                                    <td><?= $Assigned ?></td>
                                    <td><?= $Replies - 1 ?></td>
                                    <td><?= $Resolver ?></td>
                                </tr>
                            <?      } ?>
                        </table>
                    </div>
                <?
                }
            }

            // Displays a table of forum warnings viewable only to Forum Moderators
            if ($LoggedUser['Class'] == 650 && check_perms('users_warn', $Class)) {
                $DB->query("
		SELECT Comment
		FROM users_warnings_forums
		WHERE UserID = '$UserID'");
                list($ForumWarnings) = $DB->next_record();
                if ($DB->has_results()) {
                ?>
                    <div class="box">
                        <div class="head"><?= Lang::get('user', 'forum_warnings') ?></div>
                        <div class="pad">
                            <div id="forumwarningslinks" class="AdminComment" style="width: 98%;"><?= Text::full_format($ForumWarnings) ?></div>
                        </div>
                    </div>
                <?
                }
            }
            if (check_perms('users_mod', $Class)) { ?>
                <form class="manage_form" name="user" id="form" action="user.php" method="post">
                    <input type="hidden" name="action" value="moderate" />
                    <input type="hidden" name="userid" value="<?= $UserID ?>" />
                    <input type="hidden" name="auth" value="<?= $LoggedUser['AuthKey'] ?>" />

                    <div class="box box2" id="staff_notes_box">
                        <div class="head">
                            <?= Lang::get('user', 'staff_note') ?>
                            <a href="#" name="admincommentbutton" onclick="ChangeTo('text'); return false;" class="brackets"><?= Lang::get('user', 'staff_edit') ?></a>
                            <a href="#" onclick="$('#staffnotes').gtoggle(); return false;" class="brackets"><?= Lang::get('global', 'toggle') ?></a>
                        </div>
                        <div id="staffnotes" class="pad">
                            <input type="hidden" name="comment_hash" value="<?= $CommentHash ?>" />
                            <div id="admincommentlinks" class="AdminComment" style="width: 98%;"><?= Text::full_format($AdminComment) ?></div>
                            <textarea id="admincomment" onkeyup="resize('admincomment');" class="AdminComment hidden" name="AdminComment" cols="65" rows="26" style="width: 98%;"><?= display_str($AdminComment) ?></textarea>
                            <a href="#" name="admincommentbutton" onclick="ChangeTo('text'); return false;" class="brackets"><?= Lang::get('user', 'toggle_staff_edit') ?></a>
                            <script type="text/javascript">
                                resize('admincomment');
                            </script>
                        </div>
                    </div>

                    <table class="layout" id="user_info_box">
                        <tr class="colhead">
                            <td colspan="2">
                                <?= Lang::get('user', 'info') ?>
                            </td>
                        </tr>
                        <? if (check_perms('users_edit_usernames', $Class)) { ?>
                            <tr>
                                <td class="label"><?= Lang::get('user', 'account') ?></td>
                                <td><input type="text" size="20" name="Username" value="<?= display_str($Username) ?>" /></td>
                            </tr>
                        <?
                        }
                        if (check_perms('users_edit_titles')) {
                        ?>
                            <tr>
                                <td class="label"><?= Lang::get('user', 'customtitle') ?></td>
                                <td><input type="text" class="wide_input_text" name="Title" value="<?= display_str($CustomTitle) ?>" /></td>
                            </tr>
                        <?
                        }

                        if (check_perms('users_promote_below', $Class) || check_perms('users_promote_to', $Class - 1)) {
                        ?>
                            <tr>
                                <td class="label"><?= Lang::get('user', 'promote_class') ?></td>
                                <td>
                                    <select name="Class">
                                        <?
                                        foreach ($ClassLevels as $CurClass) {
                                            if ($CurClass['Secondary']) {
                                                continue;
                                            } elseif ($LoggedUser['ID'] != $UserID && !check_perms('users_promote_to', $Class - 1) && $CurClass['Level'] == $LoggedUser['EffectiveClass']) {
                                                break;
                                            } elseif ($CurClass['Level'] > $LoggedUser['EffectiveClass']) {
                                                break;
                                            }
                                            if ($Class === $CurClass['Level']) {
                                                $Selected = ' selected="selected"';
                                            } else {
                                                $Selected = '';
                                            }
                                        ?>
                                            <option value="<?= $CurClass['ID'] ?>" <?= $Selected ?>><?= $CurClass['Name'] . ' (' . $CurClass['Level'] . ')' ?></option>
                                        <?      } ?>
                                    </select>
                                </td>
                            </tr>
                        <?
                        }

                        if (check_perms('users_give_donor')) {
                        ?>
                            <tr>
                                <td class="label"><?= Lang::get('user', 'donor') ?></td>
                                <td><input type="checkbox" name="Donor" <? if ($Donor == 1) { ?> checked="checked" <? } ?> /></td>
                            </tr>
                        <?
                        }
                        if (check_perms('users_promote_below') || check_perms('users_promote_to')) { ?>
                            <tr>
                                <td class="label"><?= Lang::get('user', 'se_class') ?></td>
                                <td>
                                    <?
                                    $DB->query("
			SELECT p.ID, p.Name, l.UserID
			FROM permissions AS p
				LEFT JOIN users_levels AS l ON l.PermissionID = p.ID AND l.UserID = '$UserID'
			WHERE p.Secondary = 1
			ORDER BY p.Name");
                                    $i = 0;
                                    while (list($PermID, $PermName, $IsSet) = $DB->next_record()) {
                                        $i++;
                                    ?>
                                        <input type="checkbox" id="perm_<?= $PermID ?>" name="secondary_classes[]" value="<?= $PermID ?>" <? if ($IsSet) { ?> checked="checked" <? } ?> />&nbsp;<label for="perm_<?= $PermID ?>" style="margin-right: 10px;"><?= $PermName ?></label>
                                    <? if ($i % 3 == 0) {
                                            echo "\t\t\t\t<br />\n";
                                        }
                                    } ?>
                                </td>
                            </tr>
                        <?  }
                        if (check_perms('users_make_invisible')) {
                        ?>
                            <tr>
                                <td class="label"><?= Lang::get('user', 'view_list') ?></td>
                                <td><input type="checkbox" name="Visible" <? if ($Visible == 1) { ?> checked="checked" <? } ?> /></td>
                            </tr>
                        <?
                        }

                        if (check_perms('users_edit_ratio', $Class) || (check_perms('users_edit_own_ratio') && $UserID == $LoggedUser['ID'])) {
                        ?>
                            <tr>
                                <td class="label tooltip" title="<?= Lang::get('user', 'uploaded_title') ?>"><?= Lang::get('user', 'uploaded') ?></td>
                                <td>
                                    <input type="hidden" name="OldUploaded" value="<?= $Uploaded ?>" />
                                    <input type="text" size="20" name="Uploaded" value="<?= $Uploaded ?>" />
                                </td>
                            </tr>
                            <tr>
                                <td class="label tooltip" title="<?= Lang::get('user', 'downloaded_title') ?>"><?= Lang::get('user', 'downloaded') ?></td>
                                <td>
                                    <input type="hidden" name="OldDownloaded" value="<?= $Downloaded ?>" />
                                    <input type="text" size="20" name="Downloaded" value="<?= $Downloaded ?>" />
                                </td>
                            </tr>
                            <tr>
                                <td class="label tooltip" title="<?= Lang::get('user', 'bonus_points_title') ?>"><?= Lang::get('user', 'bonus_points') ?></td>
                                <td>
                                    <input type="hidden" name="OldBonusPoints" value="<?= $BonusPoints ?>" />
                                    <input type="text" size="20" name="BonusPoints" value="<?= $BonusPoints ?>" />
                                </td>
                            </tr>
                            <tr>
                                <td class="label tooltip" title="<?= Lang::get('user', 'merge_from_title') ?>"><?= Lang::get('user', 'merge_from') ?></td>
                                <td>
                                    <input type="text" size="40" name="MergeStatsFrom" />
                                </td>
                            </tr>
                        <?
                        }

                        if (check_perms('users_edit_invites')) {
                        ?>
                            <tr>
                                <td class="label tooltip" title="Number of invites"><?= Lang::get('user', 'invite') ?></td>
                                <td><input type="text" size="5" name="Invites" value="<?= $Invites ?>" /></td>
                            </tr>
                        <?
                        }

                        if (check_perms('admin_manage_user_fls')) {
                        ?>
                            <tr>
                                <td class="label tooltip" title="Number of FL tokens"><?= Lang::get('user', 'token') ?></td>
                                <td><input type="text" size="5" name="FLTokens" value="<?= $FLTokens ?>" /></td>
                            </tr>
                        <?
                        }

                        if (check_perms('admin_manage_fls') || (check_perms('users_mod') && $OwnProfile)) {
                        ?>
                            <tr>
                                <td class="label tooltip" title="<?= Lang::get('user', 'staff_mark_title') ?>"><?= Lang::get('user', 'staff_mark') ?></td>
                                <td><input type="text" class="wide_input_text" name="SupportFor" value="<?= display_str($SupportFor) ?>" /></td>
                            </tr>
                        <?
                        }

                        if (check_perms('users_edit_reset_keys')) {
                        ?>
                            <tr>
                                <td class="label"><?= Lang::get('user', 'reset') ?></td>
                                <td id="reset_td">
                                    <input type="checkbox" name="ResetRatioWatch" id="ResetRatioWatch" /> <label for="ResetRatioWatch"><?= Lang::get('user', 'ratio_watch') ?></label>
                                    <input type="checkbox" name="ResetPasskey" id="ResetPasskey" /> <label for="ResetPasskey"><?= Lang::get('user', 'passkey') ?></label>
                                    <input type="checkbox" name="ResetAuthkey" id="ResetAuthkey" /> <label for="ResetAuthkey"><?= Lang::get('user', 'authkey') ?></label>
                                    <input type="checkbox" name="ResetIPHistory" id="ResetIPHistory" /> <label for="ResetIPHistory"><?= Lang::get('user', 'ip_history') ?></label>
                                    <input type="checkbox" name="ResetEmailHistory" id="ResetEmailHistory" /> <label for="ResetEmailHistory"><?= Lang::get('user', 'email_history') ?></label>
                                    <br />
                                    <input type="checkbox" name="ResetSnatchList" id="ResetSnatchList" /> <label for="ResetSnatchList"><?= Lang::get('user', 'snatch_list') ?></label>
                                    <input type="checkbox" name="ResetDownloadList" id="ResetDownloadList" /> <label for="ResetDownloadList"><?= Lang::get('user', 'download_list') ?></label>
                                </td>
                            </tr>
                        <?
                        }

                        if (check_perms('users_edit_password')) {
                        ?>
                            <tr>
                                <td class="label"><?= Lang::get('user', 'new_password') ?></td>
                                <td>
                                    <input type="text" size="30" id="change_password" name="ChangePassword" />
                                    <button type="button" id="random_password"><?= Lang::get('user', 'generate') ?></button>
                                </td>
                            </tr>

                            <tr>
                                <td class="label"><?= Lang::get('user', '2fa') ?></td>
                                <td>
                                    <? if ($FA_Key) { ?>
                                        <a href="user.php?action=2fa&page=user&do=disable&userid=<?= $UserID ?>"><?= Lang::get('user', 'close') ?></a>
                                    <?      } else { ?>
                                        <?= Lang::get('user', 'closed') ?>
                                    <?      } ?>
                                </td>
                            </tr>

                        <?  } ?>
                    </table>

                    <? if (check_perms('users_warn')) { ?>
                        <table class="layout" id="warn_user_box">
                            <tr class="colhead">
                                <td colspan="2">
                                    <?= Lang::get('user', 'warn') ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="label"><?= Lang::get('user', 'warned') ?></td>
                                <td>
                                    <input type="checkbox" name="Warned" <? if ($Warned != '0000-00-00 00:00:00') { ?> checked="checked" <? } ?> />
                                </td>
                            </tr>
                            <? if ($Warned == '0000-00-00 00:00:00') { /* user is not warned */ ?>
                                <tr>
                                    <td class="label"><?= Lang::get('user', 'warn_time') ?></td>
                                    <td>
                                        <select name="WarnLength">
                                            <option value="">---</option>
                                            <option value="1"><?= Lang::get('user', '1_week') ?></option>
                                            <option value="2"><?= Lang::get('user', '2_week') ?></option>
                                            <option value="4"><?= Lang::get('user', '4_week') ?></option>
                                            <option value="8"><?= Lang::get('user', '8_week') ?></option>
                                        </select>
                                    </td>
                                </tr>
                            <?      } else { /* user is warned */ ?>
                                <tr>
                                    <td class="label"><?= Lang::get('user', 'warn_time') ?></td>
                                    <td>
                                        <select name="ExtendWarning" onchange="ToggleWarningAdjust(this);">
                                            <option>---</option>
                                            <option value="1"><?= Lang::get('user', '1_week') ?></option>
                                            <option value="2"><?= Lang::get('user', '2_week') ?></option>
                                            <option value="4"><?= Lang::get('user', '4_week') ?></option>
                                            <option value="8"><?= Lang::get('user', '8_week') ?></option>
                                        </select>
                                    </td>
                                </tr>
                                <tr id="ReduceWarningTR">
                                    <td class="label"><?= Lang::get('user', 'free_time') ?></td>
                                    <td>
                                        <select name="ReduceWarning">
                                            <option>---</option>
                                            <option value="1"><?= Lang::get('user', '1_week') ?></option>
                                            <option value="2"><?= Lang::get('user', '2_week') ?></option>
                                            <option value="4"><?= Lang::get('user', '4_week') ?></option>
                                            <option value="8"><?= Lang::get('user', '8_week') ?></option>
                                        </select>
                                    </td>
                                </tr>
                            <?      } ?>
                            <tr>
                                <td class="label tooltip" title="<?= Lang::get('user', 'warn_reason_title') ?>"><?= Lang::get('user', 'warn_reason') ?></td>
                                <td>
                                    <input type="text" class="wide_input_text" name="WarnReason" />
                                </td>
                            </tr>
                        <?  } ?>
                        </table>
                        <? if (check_perms('users_disable_any')) { ?>
                            <table class="layout">
                                <tr class="colhead">
                                    <td colspan="2">
                                        <?= Lang::get('user', 'disable_account') ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="label"><?= Lang::get('user', 'account_disable') ?></td>
                                    <td>
                                        <input type="checkbox" name="LockAccount" id="LockAccount" <? if ($LockedAccount) { ?> checked="checked" <? } ?> />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="label"><?= Lang::get('user', 'reason') ?></td>
                                    <td>
                                        <select name="LockReason">
                                            <option value="---">---</option>
                                            <option value="<?= STAFF_LOCKED ?>" <? if ($LockedAccount == STAFF_LOCKED) { ?> selected <? } ?>><?= Lang::get('user', 'admin_account') ?></option>
                                        </select>
                                    </td>
                                </tr>
                            </table>
                        <?  }  ?>
                        <table class="layout" id="user_privs_box">
                            <tr class="colhead">
                                <td colspan="2">
                                    <?= Lang::get('user', 'user_po') ?>
                                </td>
                            </tr>
                            <? if (check_perms('users_disable_posts') || check_perms('users_disable_any')) {
                                $DB->query("
			SELECT DISTINCT Email, IP
			FROM users_history_emails
			WHERE UserID = $UserID
			ORDER BY Time ASC");
                                $Emails = $DB->to_array();
                            ?>
                                <tr>
                                    <td class="label"><?= Lang::get('user', 'user_disable') ?></td>
                                    <td>
                                        <input type="checkbox" name="DisablePosting" id="DisablePosting" <? if ($DisablePosting == 1) { ?> checked="checked" <? } ?> /> <label for="DisablePosting"><?= Lang::get('user', 'posting') ?></label>
                                        <? if (check_perms('users_disable_any')) { ?>
                                            <input type="checkbox" name="DisableAvatar" id="DisableAvatar" <? if ($DisableAvatar == 1) { ?> checked="checked" <? } ?> /> <label for="DisableAvatar"><?= Lang::get('user', 'avatar') ?></label>
                                            <input type="checkbox" name="DisableForums" id="DisableForums" <? if ($DisableForums == 1) { ?> checked="checked" <? } ?> /> <label for="DisableForums"><?= Lang::get('user', 'forums') ?></label>
                                            <input type="checkbox" name="DisableIRC" id="DisableIRC" <? if ($DisableIRC == 1) { ?> checked="checked" <? } ?> /> <label for="DisableIRC"><?= Lang::get('user', 'irc') ?></label>
                                            <input type="checkbox" name="DisablePM" id="DisablePM" <? if ($DisablePM == 1) { ?> checked="checked" <? } ?> /> <label for="DisablePM"><?= Lang::get('user', 'pm') ?></label>
                                            <br />
                                            <input type="checkbox" name="DisableLeech" id="DisableLeech" <? if ($DisableLeech == 0) { ?> checked="checked" <? } ?> /> <label for="DisableLeech"><?= Lang::get('user', 'leech') ?></label>
                                            <input type="checkbox" name="DisableRequests" id="DisableRequests" <? if ($DisableRequests == 1) { ?> checked="checked" <? } ?> /> <label for="DisableRequests"><?= Lang::get('global', 'requests') ?></label>
                                            <input type="checkbox" name="DisableUpload" id="DisableUpload" <? if ($DisableUpload == 1) { ?> checked="checked" <? } ?> /> <label for="DisableUpload"><?= Lang::get('user', 'torrent_upload') ?></label>
                                            <input type="checkbox" name="DisablePoints" id="DisablePoints" <? if ($DisablePoints == 1) { ?> checked="checked" <? } ?> /> <label for="DisablePoints"><?= Lang::get('user', 'bonus_points') ?></label>
                                            <br />
                                            <input type="checkbox" name="DisableTagging" id="DisableTagging" <? if ($DisableTagging == 1) { ?> checked="checked" <? } ?> /> <label for="DisableTagging" class="tooltip" title="<?= Lang::get('user', 'tagging_title') ?>"><?= Lang::get('user', 'tagging') ?></label>
                                            <input type="checkbox" name="DisableWiki" id="DisableWiki" <? if ($DisableWiki == 1) { ?> checked="checked" <? } ?> /> <label for="DisableWiki"><?= Lang::get('user', 'wiki') ?></label>
                                            <br />
                                            <input type="checkbox" name="DisableInvites" id="DisableInvites" <? if ($DisableInvites == 1) { ?> checked="checked" <? } ?> /> <label for="DisableInvites"><?= Lang::get('user', 'invites') ?></label>
                                            <br />
                                            <input type="checkbox" name="DisableCheckAll" id="DisableCheckAll" <? if ($DisableCheckAll == 1) { ?> checked="checked" <? } ?> /> <label for="DisableCheckAll"><?= Lang::get('user', 'check_all_torrents') ?></label>
                                            <input type="checkbox" name="DisableCheckSelf" id="DisableCheckSelf" <? if ($DisableCheckSelf == 1) { ?> checked="checked" <? } ?> /> <label for="DisableCheckSelf"><?= Lang::get('user', 'check_his_her_torrents') ?></label>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="label"><?= Lang::get('user', 'hacked') ?></td>
                                    <td>
                                        <input type="checkbox" name="SendHackedMail" id="SendHackedMail" /> <label for="SendHackedMail"><?= Lang::get('user', 'send_hacked_account_email_to') ?></label><select name="HackedEmail">
                                            <?
                                            foreach ($Emails as $Email) {
                                                list($Address, $IP) = $Email;
                                            ?>
                                                <option value="<?= display_str($Address) ?>"><?= display_str($Address) ?> - <?= display_str($IP) ?></option>
                                            <?          } ?>
                                        </select>
                                    </td>
                                </tr>

                            <?
                                        }
                                    }

                                    if (check_perms('users_disable_any')) {
                            ?>
                            <tr>
                                <td class="label"><?= Lang::get('user', 'account') ?></td>
                                <td>
                                    <select name="UserStatus">
                                        <option value="0" <? if ($Enabled == '0') { ?> selected="selected" <? } ?>><?= Lang::get('user', 'unconfirmed') ?></option>
                                        <option value="1" <? if ($Enabled == '1') { ?> selected="selected" <? } ?>><?= Lang::get('user', 'enabled') ?></option>
                                        <option value="2" <? if ($Enabled == '2') { ?> selected="selected" <? } ?>><?= Lang::get('user', 'disabled') ?></option>
                                        <? if (check_perms('users_delete_users')) { ?>
                                            <optgroup label="-- WARNING --">
                                                <option value="delete"><?= Lang::get('user', 'delete_account') ?></option>
                                            </optgroup>
                                        <?      } ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td class="label"><?= Lang::get('user', 'user_reason') ?></td>
                                <td>
                                    <input type="text" class="wide_input_text" name="UserReason" />
                                </td>
                            </tr>
                            <tr>
                                <td class="label tooltip" title="<?= Lang::get('user', 'restricted_forums_title') ?>"><?= Lang::get('user', 'restricted_forums') ?></td>
                                <td>
                                    <input type="text" class="wide_input_text" name="RestrictedForums" value="<?= display_str($RestrictedForums) ?>" />
                                </td>
                            </tr>
                            <tr>
                                <td class="label tooltip" title="<?= Lang::get('user', 'permitted_forums_title') ?>"><?= Lang::get('user', 'permitted_forums') ?></td>
                                <td>
                                    <input type="text" class="wide_input_text" name="PermittedForums" value="<?= display_str($PermittedForums) ?>" />
                                </td>
                            </tr>

                        <?  } ?>
                        </table>
                        <? if (check_perms('users_logout')) { ?>
                            <table class="layout" id="session_box">
                                <tr class="colhead">
                                    <td colspan="2">
                                        <?= Lang::get('user', 'session') ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="label"><?= Lang::get('user', 'reset_session') ?></td>
                                    <td><input type="checkbox" name="ResetSession" id="ResetSession" /></td>
                                </tr>
                                <tr>
                                    <td class="label"><?= Lang::get('user', 'logout') ?></td>
                                    <td><input type="checkbox" name="LogOut" id="LogOut" /></td>
                                </tr>
                            </table>
                        <?
                        }
                        if (check_perms('users_mod')) {
                            DonationsView::render_mod_donations($donationInfo['Rank'], $donationInfo['TotRank']);
                        }
                        ?>
                        <table class="layout" id="submit_box">
                            <tr class="colhead">
                                <td colspan="2"><?= Lang::get('user', 'submit') ?></td>
                            </tr>
                            <tr>
                                <td class="label tooltip" title="<?= Lang::get('user', 'reason_title') ?>"><?= Lang::get('user', 'reason') ?>:</td>
                                <td>
                                    <textarea rows="1" cols="35" class="wide_input_text" name="Reason" id="Reason" onkeyup="resize('Reason');"></textarea>
                                </td>
                            </tr>
                            <tr>
                                <td class="label"><?= Lang::get('user', 'paste_user_stats') ?>:</td>
                                <td>
                                    <button type="button" id="paster"><?= Lang::get('user', 'paste') ?></button>
                                </td>
                            </tr>

                            <tr>
                                <td align="right" colspan="2">
                                    <input type="submit" value="Save changes" />
                                </td>
                            </tr>
                        </table>
                </form>
            <?
            }
            ?>
        </div>
    </div>
</div>
<? View::show_footer(); ?>