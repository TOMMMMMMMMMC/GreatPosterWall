<?

use Gazelle\Manager\Donation;

define('FOOTER_FILE', SERVER_ROOT . '/design/privatefooter.php');

$donation = new Donation();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html data-theme="<?= is_dev() ? 'auto' : '' ?>" xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <title><?= display_str($PageTitle) ?></title>
    <meta http-equiv="X-UA-Compatible" content="chrome=1;IE=edge" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="msapplication-config" content="none" />
    <link rel="stylesheet" href="<?= STATIC_SERVER ?>styles/all.css">
    <link href="https://cdn.bootcdn.net/ajax/libs/font-awesome/5.13.1/css/all.css" rel="stylesheet">
    <link rel="shortcut icon" href="favicon.ico" />
    <link rel="apple-touch-icon" href="favicon.ico" />
    <link rel="search" type="application/opensearchdescription+xml" title="<?= SITE_NAME ?> Torrents" href="opensearch.php?type=torrents" />
    <link rel="search" type="application/opensearchdescription+xml" title="<?= SITE_NAME ?> Artists" href="opensearch.php?type=artists" />
    <link rel="search" type="application/opensearchdescription+xml" title="<?= SITE_NAME ?> Requests" href="opensearch.php?type=requests" />
    <link rel="search" type="application/opensearchdescription+xml" title="<?= SITE_NAME ?> Forums" href="opensearch.php?type=forums" />
    <link rel="search" type="application/opensearchdescription+xml" title="<?= SITE_NAME ?> Log" href="opensearch.php?type=log" />
    <link rel="search" type="application/opensearchdescription+xml" title="<?= SITE_NAME ?> Users" href="opensearch.php?type=users" />
    <link rel="search" type="application/opensearchdescription+xml" title="<?= SITE_NAME ?> Wiki" href="opensearch.php?type=wiki" />
    <link rel="alternate" type="application/rss+xml" href="feeds.php?feed=feed_news&amp;user=<?= G::$LoggedUser['ID'] ?>&amp;auth=<?= G::$LoggedUser['RSS_Auth'] ?>&amp;passkey=<?= G::$LoggedUser['torrent_pass'] ?>&amp;authkey=<?= G::$LoggedUser['AuthKey'] ?>" title="<?= SITE_NAME ?> - News" />
    <link rel="alternate" type="application/rss+xml" href="feeds.php?feed=feed_blog&amp;user=<?= G::$LoggedUser['ID'] ?>&amp;auth=<?= G::$LoggedUser['RSS_Auth'] ?>&amp;passkey=<?= G::$LoggedUser['torrent_pass'] ?>&amp;authkey=<?= G::$LoggedUser['AuthKey'] ?>" title="<?= SITE_NAME ?> - Blog" />
    <link rel="alternate" type="application/rss+xml" href="feeds.php?feed=feed_changelog&amp;user=<?= G::$LoggedUser['ID'] ?>&amp;auth=<?= G::$LoggedUser['RSS_Auth'] ?>&amp;passkey=<?= G::$LoggedUser['torrent_pass'] ?>&amp;authkey=<?= G::$LoggedUser['AuthKey'] ?>" title="<?= SITE_NAME ?> - Gazelle Change Log" />
    <link rel="alternate" type="application/rss+xml" href="feeds.php?feed=torrents_notify_<?= G::$LoggedUser['torrent_pass'] ?>&amp;user=<?= G::$LoggedUser['ID'] ?>&amp;auth=<?= G::$LoggedUser['RSS_Auth'] ?>&amp;passkey=<?= G::$LoggedUser['torrent_pass'] ?>&amp;authkey=<?= G::$LoggedUser['AuthKey'] ?>" title="<?= SITE_NAME ?> - P.T.N." />

    <?
    if (isset(G::$LoggedUser['Notify'])) {
        foreach (G::$LoggedUser['Notify'] as $Filter) {
            list($FilterID, $FilterName) = $Filter;
    ?>
            <link rel="alternate" type="application/rss+xml" href="feeds.php?feed=torrents_notify_<?= $FilterID ?>_<?= G::$LoggedUser['torrent_pass'] ?>&amp;user=<?= G::$LoggedUser['ID'] ?>&amp;auth=<?= G::$LoggedUser['RSS_Auth'] ?>&amp;passkey=<?= G::$LoggedUser['torrent_pass'] ?>&amp;authkey=<?= G::$LoggedUser['AuthKey'] ?>&amp;name=<?= urlencode($FilterName) ?>" title="<?= SITE_NAME ?> - <?= display_str($FilterName) ?>" />
    <?
        }
    }
    ?>
    <link rel="alternate" type="application/rss+xml" href="feeds.php?feed=torrents_all&amp;user=<?= G::$LoggedUser['ID'] ?>&amp;auth=<?= G::$LoggedUser['RSS_Auth'] ?>&amp;passkey=<?= G::$LoggedUser['torrent_pass'] ?>&amp;authkey=<?= G::$LoggedUser['AuthKey'] ?>" title="<?= SITE_NAME ?> - All Torrents" />
    <link rel="alternate" type="application/rss+xml" href="feeds.php?feed=torrents_music&amp;user=<?= G::$LoggedUser['ID'] ?>&amp;auth=<?= G::$LoggedUser['RSS_Auth'] ?>&amp;passkey=<?= G::$LoggedUser['torrent_pass'] ?>&amp;authkey=<?= G::$LoggedUser['AuthKey'] ?>" title="<?= SITE_NAME ?> - Music Torrents" />
    <link rel="alternate" type="application/rss+xml" href="feeds.php?feed=torrents_apps&amp;user=<?= G::$LoggedUser['ID'] ?>&amp;auth=<?= G::$LoggedUser['RSS_Auth'] ?>&amp;passkey=<?= G::$LoggedUser['torrent_pass'] ?>&amp;authkey=<?= G::$LoggedUser['AuthKey'] ?>" title="<?= SITE_NAME ?> - Application Torrents" />
    <link rel="alternate" type="application/rss+xml" href="feeds.php?feed=torrents_ebooks&amp;user=<?= G::$LoggedUser['ID'] ?>&amp;auth=<?= G::$LoggedUser['RSS_Auth'] ?>&amp;passkey=<?= G::$LoggedUser['torrent_pass'] ?>&amp;authkey=<?= G::$LoggedUser['AuthKey'] ?>" title="<?= SITE_NAME ?> - E-Book Torrents" />
    <link rel="alternate" type="application/rss+xml" href="feeds.php?feed=torrents_abooks&amp;user=<?= G::$LoggedUser['ID'] ?>&amp;auth=<?= G::$LoggedUser['RSS_Auth'] ?>&amp;passkey=<?= G::$LoggedUser['torrent_pass'] ?>&amp;authkey=<?= G::$LoggedUser['AuthKey'] ?>" title="<?= SITE_NAME ?> - Audiobooks Torrents" />
    <link rel="alternate" type="application/rss+xml" href="feeds.php?feed=torrents_evids&amp;user=<?= G::$LoggedUser['ID'] ?>&amp;auth=<?= G::$LoggedUser['RSS_Auth'] ?>&amp;passkey=<?= G::$LoggedUser['torrent_pass'] ?>&amp;authkey=<?= G::$LoggedUser['AuthKey'] ?>" title="<?= SITE_NAME ?> - E-Learning Video Torrents" />
    <link rel="alternate" type="application/rss+xml" href="feeds.php?feed=torrents_comedy&amp;user=<?= G::$LoggedUser['ID'] ?>&amp;auth=<?= G::$LoggedUser['RSS_Auth'] ?>&amp;passkey=<?= G::$LoggedUser['torrent_pass'] ?>&amp;authkey=<?= G::$LoggedUser['AuthKey'] ?>" title="<?= SITE_NAME ?> - Comedy Torrents" />
    <link rel="alternate" type="application/rss+xml" href="feeds.php?feed=torrents_comics&amp;user=<?= G::$LoggedUser['ID'] ?>&amp;auth=<?= G::$LoggedUser['RSS_Auth'] ?>&amp;passkey=<?= G::$LoggedUser['torrent_pass'] ?>&amp;authkey=<?= G::$LoggedUser['AuthKey'] ?>" title="<?= SITE_NAME ?> - Comic Torrents" />
    <link rel="alternate" type="application/rss+xml" href="feeds.php?feed=torrents_mp3&amp;user=<?= G::$LoggedUser['ID'] ?>&amp;auth=<?= G::$LoggedUser['RSS_Auth'] ?>&amp;passkey=<?= G::$LoggedUser['torrent_pass'] ?>&amp;authkey=<?= G::$LoggedUser['AuthKey'] ?>" title="<?= SITE_NAME ?> - MP3 Torrents" />
    <link rel="alternate" type="application/rss+xml" href="feeds.php?feed=torrents_flac&amp;user=<?= G::$LoggedUser['ID'] ?>&amp;auth=<?= G::$LoggedUser['RSS_Auth'] ?>&amp;passkey=<?= G::$LoggedUser['torrent_pass'] ?>&amp;authkey=<?= G::$LoggedUser['AuthKey'] ?>" title="<?= SITE_NAME ?> - FLAC Torrents" />
    <link rel="alternate" type="application/rss+xml" href="feeds.php?feed=torrents_vinyl&amp;user=<?= G::$LoggedUser['ID'] ?>&amp;auth=<?= G::$LoggedUser['RSS_Auth'] ?>&amp;passkey=<?= G::$LoggedUser['torrent_pass'] ?>&amp;authkey=<?= G::$LoggedUser['AuthKey'] ?>" title="<?= SITE_NAME ?> - Vinyl Sourced Torrents" />
    <link rel="alternate" type="application/rss+xml" href="feeds.php?feed=torrents_lossless&amp;user=<?= G::$LoggedUser['ID'] ?>&amp;auth=<?= G::$LoggedUser['RSS_Auth'] ?>&amp;passkey=<?= G::$LoggedUser['torrent_pass'] ?>&amp;authkey=<?= G::$LoggedUser['AuthKey'] ?>" title="<?= SITE_NAME ?> - Lossless Torrents" />
    <link rel="alternate" type="application/rss+xml" href="feeds.php?feed=torrents_lossless24&amp;user=<?= G::$LoggedUser['ID'] ?>&amp;auth=<?= G::$LoggedUser['RSS_Auth'] ?>&amp;passkey=<?= G::$LoggedUser['torrent_pass'] ?>&amp;authkey=<?= G::$LoggedUser['AuthKey'] ?>" title="<?= SITE_NAME ?> - 24bit Lossless Torrents" />
    <link rel="stylesheet" type="text/css" media="screen" href="css/global-bundle.css?v=<?= filemtime(SERVER_ROOT . static_prefix() . '/css/global-bundle.css') ?>" />
    <meta name="viewport" content="width=device-width" />
    <?
    if (empty(G::$LoggedUser['StyleURL'])) {
    ?>
        <? if (G::$LoggedUser['StyleName'] == 'gpw_dark_mono') { ?>
            <link rel="stylesheet" type="text/css" title="<?= G::$LoggedUser['StyleName'] ?>" media="screen" href="<?= (is_dev() ? '/src' : '') . '/css/' ?><?= G::$LoggedUser['StyleName'] ?>/style.css?v=<?= filemtime(SERVER_ROOT . static_prefix() . '/css/' . G::$LoggedUser['StyleName'] . '/style.css') ?>" />
        <? } else { ?>
            <link rel="stylesheet" type="text/css" href="<?= STATIC_SERVER ?>styles/global.css?v=<?= filemtime(SERVER_ROOT . '/public/static/styles/global.css') ?>" />
            <link rel="stylesheet" type="text/css" title="<?= G::$LoggedUser['StyleName'] ?>" media="screen" href="/static/styles/<?= G::$LoggedUser['StyleName'] ?>/style.css?v=<?= filemtime(SERVER_ROOT . '/public/static/styles/' . G::$LoggedUser['StyleName'] . '/style.css') ?>" />
        <? } ?>
    <?
    } else {
        $StyleURLInfo = parse_url(G::$LoggedUser['StyleURL']);
        if (
            substr(G::$LoggedUser['StyleURL'], -4) == '.css'
            && $StyleURLInfo['query'] . $StyleURLInfo['fragment'] == ''
            && in_array($StyleURLInfo['host'], array(SITE_HOST))
            && file_exists(SERVER_ROOT . $StyleURLInfo['path'])
        ) {
            $StyleURL = G::$LoggedUser['StyleURL'] . '?v=' . filemtime(SERVER_ROOT . '/public' . $StyleURLInfo['path']);
        } else {
            $StyleURL = G::$LoggedUser['StyleURL'];
        }
    ?>
        <link rel="stylesheet" type="text/css" media="screen" href="<?= $StyleURL ?>" title="External CSS" />
    <?
    }
    if (!empty(G::$LoggedUser['UseOpenDyslexic'])) {
        // load the OpenDyslexic font
    ?>
        <link rel="stylesheet" type="text/css" charset="utf-8" href="<?= STATIC_SERVER ?>styles/opendyslexic/style.css?v=<?= filemtime(SERVER_ROOT . '/public/static/styles/opendyslexic/style.css') ?>" />
    <?
    }
    $ExtraCSS = explode(',', $CSSIncludes);
    foreach ($ExtraCSS as $CSS) {
        if (trim($CSS) == '') {
            continue;
        }
    ?>
        <link rel="stylesheet" type="text/css" media="screen" href="<?= STATIC_SERVER . "styles/$CSS/style.css?v=" . filemtime(SERVER_ROOT . "/public/static/styles/$CSS/style.css") ?>" />
    <?
    }
    ?>
    <script type="text/javascript">
        //<![CDATA[
        var authkey = "<?= G::$LoggedUser['AuthKey'] ?>";
        var userid = <?= G::$LoggedUser['ID'] ?>;
        //]]>
    </script>
    <?

    $Scripts = array_merge(array('jquery', 'script_start', 'ajax.class', 'cookie.class', 'global', 'jquery.autocomplete', 'autocomplete', 'jquery.countdown.min', 'bbcode'), explode(',', $JSIncludes));
    foreach ($Scripts as $Script) {
        if (trim($Script) == '') {
            continue;
        }
    ?>
        <script src="<?= STATIC_SERVER ?>functions/<?= $Script ?>.js?v=<?= filemtime(SERVER_ROOT . '/public/static/functions/' . $Script . '.js') ?>" type="text/javascript"></script>
    <?
    }

    global $ClassLevels;
    // Get notifications early to change menu items if needed
    global $NotificationSpans;
    $NotificationsManager = new NotificationsManager(G::$LoggedUser['ID']);
    $Notifications = $NotificationsManager->get_notifications();
    $UseNoty = $NotificationsManager->use_noty();
    $NewSubscriptions = false;
    $NotificationSpans = array();
    foreach ($Notifications as $Type => $Notification) {
        if ($Type === NotificationsManager::SUBSCRIPTIONS) {
            $NewSubscriptions = true;
        }
        if ($UseNoty) {
            $NotificationSpans[] = "<span class=\"noty-notification\" style=\"display: none;\" data-noty-type=\"$Type\" data-noty-id=\"$Notification[id]\" data-noty-importance=\"$Notification[importance]\" data-noty-url=\"$Notification[url]\">$Notification[message]</span>";
        }
    }
    if ($UseNoty && !empty($NotificationSpans)) {
        NotificationsManagerView::load_js();
    }
    if ($NotificationsManager->is_skipped(NotificationsManager::SUBSCRIPTIONS)) {
        $NewSubscriptions = Subscriptions::has_new_subscriptions();
    }
    $Scripts = array_merge(['jquery', 'script_start', 'ajax.class', 'cookie.class', 'global', 'jquery.autocomplete', 'autocomplete', 'jquery.countdown.min', 'bbcode'], explode(',', $JSIncludes));
    ?>
</head>

<?
//Start handling alert bars
$Alerts = array();
$ModBar = array();
// Important banner
if (defined('BANNER_URL') && defined('BANNER_TEXT') && !empty(BANNER_URL) && !empty(BANNER_TEXT)) {
    $Alerts[] = "<a class='cmp-alert-info' href='" . BANNER_URL . "'>" . BANNER_TEXT . "</a>";
}

// Staff blog
if (check_perms('users_mod')) {
    global $SBlogReadTime, $LatestSBlogTime;
    if (!$SBlogReadTime && ($SBlogReadTime = G::$Cache->get_value('staff_blog_read_' . G::$LoggedUser['ID'])) === false) {
        G::$DB->query("
			SELECT Time
			FROM staff_blog_visits
			WHERE UserID = " . G::$LoggedUser['ID']);
        if (list($SBlogReadTime) = G::$DB->next_record()) {
            $SBlogReadTime = strtotime($SBlogReadTime);
        } else {
            $SBlogReadTime = 0;
        }
        G::$Cache->cache_value('staff_blog_read_' . G::$LoggedUser['ID'], $SBlogReadTime, 1209600);
    }
    if (!$LatestSBlogTime && ($LatestSBlogTime = G::$Cache->get_value('staff_blog_latest_time')) === false) {
        G::$DB->query("
			SELECT MAX(Time)
			FROM staff_blog");
        list($LatestSBlogTime) = G::$DB->next_record();
        if ($LatestSBlogTime) {
            $LatestSBlogTime = strtotime($LatestSBlogTime);
        } else {
            $LatestSBlogTime = 0;
        }
        G::$Cache->cache_value('staff_blog_latest_time', $LatestSBlogTime, 1209600);
    }
    if ($SBlogReadTime < $LatestSBlogTime) {
        $Alerts[] = '<a class="button" href="staffblog.php">New staff blog post!</a>';
    }
}

// Inbox
if ($NotificationsManager->is_traditional(NotificationsManager::INBOX)) {
    $NotificationsManager->load_inbox();
    $NewMessages = $NotificationsManager->get_notifications();
    if (isset($NewMessages[NotificationsManager::INBOX])) {
        $Alerts[] = NotificationsManagerView::format_traditional($NewMessages[NotificationsManager::INBOX]);
    }
    $NotificationsManager->clear_notifications_array();
}

if (G::$LoggedUser['RatioWatch']) {
    $Alerts[] = Lang::get('pub', 'ratio_watch_you_have_before') . time_diff(G::$LoggedUser['RatioWatchEnds'], 3) . Lang::get('pub', 'ratio_watch_you_have_after');
} elseif (G::$LoggedUser['CanLeech'] != 1) {
    $Alerts[] = Lang::get('pub', 'ratio_watch_your_dl_privileges');
}

// Torrents
if ($NotificationsManager->is_traditional(NotificationsManager::TORRENTS)) {
    $NotificationsManager->load_torrent_notifications();
    $NewTorrents = $NotificationsManager->get_notifications();
    if (isset($NewTorrents[NotificationsManager::TORRENTS])) {
        $Alerts[] = NotificationsManagerView::format_traditional($NewTorrents[NotificationsManager::TORRENTS]);
    }
    $NotificationsManager->clear_notifications_array();
}

if (check_perms('users_give_donor')) {
    $Count = $donation->getPendingDonationCount();
    if ($Count > 0) {
        $Alerts[] = "<a class='button' href='tools.php?action=prepaid_card'>" . $Count . Lang::get('donate', 'has_pending_donation') . "</a>";
    }
}
if (check_perms('admin_interviewer')) {
    // Interviewer code
    G::$DB->query("SELECT count(*) FROM `register_apply` WHERE `apply_status`=0 or `apply_status`=3");
    list($ApplyCount) = G::$DB->next_record();
    $ModBar[] = '<a class="cmp-btn-primary button" href="tools.php?action=apply_list">' . ($ApplyCount ? "$ApplyCount " : "") . Lang::get('pub', 'user_manage') . '</a>';
}
if (check_perms('users_mod')) {
    $ModBar[] = '<a class="cmp-btn-primary button" href="tools.php">' . Lang::get('pub', 'toolbox') . '</a>';
}
if (check_perms('staff_award')) {
    $ModBar[] = '<a class="cmp-btn-primary button" href="tools.php?action=award">' . Lang::get('pub', 'statistics') . '</a>';
}
if (
    check_perms('users_mod')
    || G::$LoggedUser['PermissionID'] == FORUM_MOD
    || G::$LoggedUser['PermissionID'] == TORRENT_MOD
    || isset(G::$LoggedUser['ExtraClasses'][FLS_TEAM])
) {
    $NumStaffPMsArray = G::$Cache->get_value('num_staff_pms_' . G::$LoggedUser['ID']);
    if ($NumStaffPMsArray === false) {
        if (check_perms('users_mod')) {
            $LevelCap = 1000;
            G::$DB->query("
                            SELECT COUNT(ID)
                            FROM staff_pm_conversations
                            WHERE Status = 'Unanswered'
                            AND (AssignedToUser = " . G::$LoggedUser['ID'] . "
                                OR (LEAST('$LevelCap', Level) <= '" . G::$LoggedUser['EffectiveClass'] . "'))");
            list($NumStaffPMs) = G::$DB->next_record();
            G::$DB->query("
                        SELECT COUNT(ID)
                        FROM staff_pm_conversations
                        WHERE Status = 'Unanswered'
                        AND (AssignedToUser = " . G::$LoggedUser['ID'] . "
                            OR (LEAST('$LevelCap', Level) <= '" . G::$LoggedUser['EffectiveClass'] . "' AND Level >= " . $Classes[MOD]['Level'] . "))");
            list($NumMyStaffPMs) = G::$DB->next_record();
            $NumStaffPMsArray = array($NumStaffPMs, $NumMyStaffPMs);
        }
        if (isset(G::$LoggedUser['ExtraClasses'][FLS_TEAM])) {
            G::$DB->query("
                            SELECT COUNT(ID)
                            FROM staff_pm_conversations
                            WHERE Status='Unanswered'
                                AND (AssignedToUser = " . G::$LoggedUser['ID'] . "
                                    OR Level = 0)");

            list($NumStaffPMs) = G::$DB->next_record();
            $NumStaffPMsArray = array($NumStaffPMs);
        }
        if (G::$LoggedUser['PermissionID'] == FORUM_MOD || G::$LoggedUser['PermissionID'] == TORRENT_MOD) {
            G::$DB->query("
                            SELECT COUNT(ID)
                            FROM staff_pm_conversations
                            WHERE Status='Unanswered'
                                AND (AssignedToUser = " . G::$LoggedUser['ID'] . "
                                    OR Level <= '" . $Classes[G::$LoggedUser['PermissionID']]['Level'] . "')");

            list($NumStaffPMs) = G::$DB->next_record();
            G::$DB->query("
                        SELECT COUNT(ID)
                        FROM staff_pm_conversations
                        WHERE Status='Unanswered'
                            AND (AssignedToUser = " . G::$LoggedUser['ID'] . "
                                OR (Level <= '" . $Classes[G::$LoggedUser['PermissionID']]['Level'] . "' and level >= " . $Classes[FORUM_MOD]['Level'] . "))");

            list($NumMyStaffPMs) = G::$DB->next_record();
            $NumStaffPMsArray = array($NumStaffPMs, $NumMyStaffPMs);
        }
        G::$Cache->cache_value('num_staff_pms_' . G::$LoggedUser['ID'], $NumStaffPMsArray, 1000);
    }

    if ($NumStaffPMsArray[0] > 0) {
        if (isset($NumStaffPMsArray[1])) {
            $ModBar[] = '<a class="cmp-btn-primary button" href="staffpm.php">' . $NumStaffPMsArray[1] . "/" . $NumStaffPMsArray[0] . ' Staff PMs</a>';
        } else {
            $ModBar[] = '<a class="cmp-btn-primary button" href="staffpm.php">' . $NumStaffPMsArray[0] . ' Staff PMs</a>';
        }
    }
}
if (check_perms('admin_reports')) {
    // Torrent reports code
    $NumTorrentReports = G::$Cache->get_value('num_torrent_reportsv2');
    if ($NumTorrentReports === false) {
        G::$DB->query("
			SELECT COUNT(ID)
			FROM reportsv2
			WHERE Status = 'New'");
        list($NumTorrentReports) = G::$DB->next_record();
        G::$Cache->cache_value('num_torrent_reportsv2', $NumTorrentReports, 0);
    }

    $ModBar[] = '<a class="cmp-btn-primary button" href="reportsv2.php">' . $NumTorrentReports . Lang::get('pub', 'report') . '</a>';

    // Other reports code
    $NumOtherReports = G::$Cache->get_value('num_other_reports');
    if ($NumOtherReports === false) {
        G::$DB->query("
			SELECT COUNT(ID)
			FROM reports
			WHERE Status = 'New'");
        list($NumOtherReports) = G::$DB->next_record();
        G::$Cache->cache_value('num_other_reports', $NumOtherReports, 0);
    }

    if ($NumOtherReports > 0) {
        $ModBar[] = '<a class="cmp-btn-primary button" href="reports.php">' . $NumOtherReports . (($NumTorrentReports == 1) ? Lang::get('pub', 'other_report') : Lang::get('pub', 'other_reports')) . '</a>';
    }
} elseif (check_perms('project_team')) {
    $NumUpdateReports = G::$Cache->get_value('num_update_reports');
    if ($NumUpdateReports === false) {
        G::$DB->query("
			SELECT COUNT(ID)
			FROM reports
			WHERE Status = 'New'
				AND Type = 'request_update'");
        list($NumUpdateReports) = G::$DB->next_record();
        G::$Cache->cache_value('num_update_reports', $NumUpdateReports, 0);
    }

    if ($NumUpdateReports > 0) {
        $ModBar[] = '<a class="cmp-btn-primary button" href="reports.php">Request update reports</a>';
    }
} elseif (check_perms('site_moderate_forums')) {
    $NumForumReports = G::$Cache->get_value('num_forum_reports');
    if ($NumForumReports === false) {
        G::$DB->query("
			SELECT COUNT(ID)
			FROM reports
			WHERE Status = 'New'
				AND Type IN('artist_comment', 'collages_comment', 'post', 'requests_comment', 'thread', 'torrents_comment')");
        list($NumForumReports) = G::$DB->next_record();
        G::$Cache->cache_value('num_forum_reports', $NumForumReports, 0);
    }

    if ($NumForumReports > 0) {
        $ModBar[] = '<a class="cmp-btn-primary button" href="reports.php">' . $NumForumReports . (($NumForumReports == 1) ? ' Forum report' : ' Forum reports') . '</a>';
    }
}

if (check_perms('admin_manage_applicants')) {
    $NumNewApplicants = Applicant::new_applicant_count();
    if ($NumNewApplicants > 0) {
        $ModBar[] = sprintf(
            '<a class="cmp-btn-primary button" href="apply.php?action=view">%d new Applicant%s</a>',
            $NumNewApplicants,
            ($NumNewApplicants == 1 ? '' : 's')
        );
    }

    $NumNewReplies = Applicant::new_reply_count();
    if ($NumNewReplies > 0) {
        $ModBar[] = sprintf(
            '<a class="cmp-btn-primary button" href="apply.php?action=view">%d new Applicant %s</a>',
            $NumNewReplies,
            ($NumNewReplies == 1 ? 'Reply' : 'Replies')
        );
    }
}

if (check_perms('users_mod') && FEATURE_EMAIL_REENABLE) {
    $NumEnableRequests = G::$Cache->get_value(AutoEnable::CACHE_KEY_NAME);
    if ($NumEnableRequests === false) {
        G::$DB->query("SELECT COUNT(1) FROM users_enable_requests WHERE Outcome IS NULL");
        list($NumEnableRequests) = G::$DB->next_record();
        G::$Cache->cache_value(AutoEnable::CACHE_KEY_NAME, $NumEnableRequests);
    }

    if ($NumEnableRequests > 0) {
        $ModBar[] = '<a class="cmp-btn-primary button" href="tools.php?action=enable_requests">' . $NumEnableRequests . Lang::get('global', 'enable_requests') . "</a>";
    }
}
?>

<?
$BodyClass = 'index';
if (isset($_REQUEST['action'])) {
    $BodyClass = $_REQUEST['action'];
} elseif (isset($_REQUEST['type'])) {
    $BodyClass = $_REQUEST['type'];
} elseif (isset($_REQUEST['id'])) {
    $BodyClass = 'details';
}
?>

<body id="<?= $Document == 'collages' ? 'collage' : $Document ?>" class="<?= $BodyClass ?>">
    <input id="extracb1" class="hidden" type="checkbox">
    <input id="extracb2" class="hidden" type="checkbox">
    <input id="extracb3" class="hidden" type="checkbox">
    <input id="extracb4" class="hidden" type="checkbox">
    <input id="extracb5" class="hidden" type="checkbox">
    <div>

        <?
        $Avatar = G::$LoggedUser['Avatar'] ?: STATIC_SERVER . 'common/avatars/default.png';
        ?>

        <div id="wrapper">
            <div id="header" class="<?= (!empty($Alerts) || !empty($ModBar)) ? 'has-alerts' : '' ?>">
                <div id="userinfo">
                    <ul></ul>
                    <ul id="userinfo_stats">
                        <li id="stats_seeding" class="tooltip" title="<?= Lang::get('global', 'uploaded') ?>">
                            <a class="item" href="torrents.php?type=seeding&amp;userid=<?= G::$LoggedUser['ID'] ?>">
                                <svg class="uploaded icon" width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <g fill="none" fill-rule="evenodd" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M17 8l-5-5-5 5M12 3v12" />
                                    </g>
                                </svg>
                                <span class="stat"><?= Format::get_size(G::$LoggedUser['BytesUploaded']) ?></span>
                            </a>
                        </li>
                        <li id="stats_leeching" class="tooltip" title="<?= Lang::get('global', 'uploaded') ?>">
                            <a class="item" href="torrents.php?type=leeching&amp;userid=<?= G::$LoggedUser['ID'] ?>">
                                <svg class="downloaded icon" width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <g fill="none" fill-rule="evenodd" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3" />
                                    </g>
                                </svg>
                                <span class="stat"><?= Format::get_size(G::$LoggedUser['BytesDownloaded']) ?></span>
                            </a>
                        </li>

                        <li id="stats_ratio" class="tooltip" title="<?= Lang::get('global', 'ratio') ?>">
                            <a class="item">
                                <?= ICONS['ratio'] ?>
                                <span class="stat"><?= Format::get_ratio_html(G::$LoggedUser['BytesUploaded'], G::$LoggedUser['BytesDownloaded'], true, false) ?></span>
                            </a>
                        </li>
                        <? if (!empty(G::$LoggedUser['RequiredRatio'])) { ?>
                            <li id="stats_required" class="tooltip" title="<?= Lang::get('global', 'required_ratio') ?>">
                                <a class="item" href="rules.php?p=ratio">
                                    <svg class="required-ratio icon" width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <g fill="none" fill-rule="evenodd" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" transform="translate(2 2)">
                                            <circle cx="10" cy="10" r="10" />
                                            <path d="M10 6v4M10 14h.01" />
                                        </g>
                                    </svg>
                                    <span class="stat tooltip"><?= number_format(G::$LoggedUser['RequiredRatio'], 2) ?></span>
                                </a>
                            </li>
                        <?    }
                        if (true) { ?>
                            <li id="fl_tokens" class="tooltip" title="<?= Lang::get('global', 'fltoken') ?>">
                                <a class="item" href="userhistory.php?action=token_history&amp;userid=<?= G::$LoggedUser['ID'] ?>">
                                    <svg class="fl-token icon" width="24" height="25" viewBox="0 0 24 25" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M15 5.988v2m0 4v2m0 4v2m-10-14a2 2 0 0 0-2 2v3a2 2 0 1 1 0 4v3a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-3a2 2 0 1 1 0-4v-3a2 2 0 0 0-2-2H5z" fill="none" fill-rule="evenodd" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                    </svg>
                                    <span class="stat tooltip"><? $Tokens = G::$LoggedUser['TimedTokens'] == 0 ? G::$LoggedUser['FLTokens'] : (G::$LoggedUser['FLTokens'] - G::$LoggedUser['TimedTokens']) . '+' . G::$LoggedUser['TimedTokens'];
                                                                echo $Tokens; ?></span>
                                </a>
                            </li>
                        <?    }
                        if (ENABLE_HNR) { ?>
                            <li id="hit_and_runs">
                                <a href="rules.php?p=ratio">
                                    <i class="fas fa-running tooltip" title="<?= Lang::get('torrents', 'hit_and_run') ?>"></i></a>: <a href="torrents.php?type=downloaded&userid=<?= G::$LoggedUser['ID'] ?>&view=1"><?= Users::get_hnr_count(G::$LoggedUser['ID']) ?>
                                </a>
                            </li>
                        <? } ?>
                    </ul>

                    <?
                    if (check_perms('site_send_unlimited_invites')) {
                        $Invites = ' (∞)';
                    } elseif (G::$LoggedUser['Invites'] > 0) {
                        $Invites = G::$LoggedUser['TimedInvites'] == 0 ? ' (' . G::$LoggedUser['Invites'] . ')' : ' (' . (G::$LoggedUser['Invites'] - G::$LoggedUser['TimedInvites']) . '+' . G::$LoggedUser['TimedInvites'] . ')';
                    } else {
                        $Invites = '';
                    }
                    ?>
                    <ul id="userinfo_major">
                        <li></li>
                        <li id="nav_upload" class="brackets<?= Format::add_class($PageID, array('upload'), 'active', false) ?>">
                            <a href="upload.php" class="icon-container tooltip" title="<?= Lang::get('global', 'menu_upload_title') ?>">
                                <svg class="upload icon" width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <g fill="none" fill-rule="evenodd" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                                        <path d="M12 5v14M5 12h14" />
                                    </g>
                                </svg>
                            </a>
                        </li>
                        <li id="nav_bonus" class="brackets<?= Format::add_class($PageID, array('user', 'bonus'), 'active', false) ?>">
                            <a href="bonus.php" class='icon-container tooltip' title="<?= Lang::get('global', 'bonus') ?> (<?= number_format(G::$LoggedUser['BonusPoints']) ?>)">
                                <?= ICONS['bonus'] ?>
                            </a>
                        </li>
                        <li id="nav_invite" class="brackets<?= Format::add_class($PageID, array('user', 'invite'), 'active', false) ?>">
                            <a href="user.php?action=invite" class='icon-container tooltip' title="<?= Lang::get('global', 'invite') ?><?= $Invites ?>">
                                <svg class="invite icon" width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <g fill="none" fill-rule="evenodd" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" transform="translate(1 3)">
                                        <path d="M15 18v-2a4 4 0 0 0-4-4H4a4 4 0 0 0-4 4v2" />
                                        <circle cx="7.5" cy="4" r="4" />
                                        <path d="M19 5v6M22 8h-6" />
                                    </g>
                                </svg>
                            </a>
                        </li>
                        <li id="nav_image" class="brackets">
                            <a href="upload.php?action=image" class="icon-container tooltip" title="<?= Lang::get('global', 'image_host') ?>">
                                <svg class="image-host icon" width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <g fill="none" fill-rule="evenodd" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" transform="translate(3 3)">
                                        <rect width="18" height="18" rx="2" />
                                        <circle cx="5.5" cy="5.5" r="1.5" />
                                        <path d="m18 12-5-5L2 18" />
                                    </g>
                                </svg>
                            </a>
                        </li>
                        <li id="nav_donate" class="brackets<?= Format::add_class($PageID, array('donate'), 'active', false) ?> tooltip" title="<?= Lang::get('donate', 'progress', false, $donation->getYearProgress()) ?>">
                            <a href="donate.php">
                                <div class='icon-container'>
                                    <svg class="donate icon" width="30" height="30" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M4.318 5.318a4.5 4.5 0 0 0 0 6.364L12 19.364l7.682-7.682a4.5 4.5 0 0 0-6.364-6.364L12 6.636l-1.318-1.318a4.5 4.5 0 0 0-6.364 0z" fill="none" fill-rule="evenodd" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                    </svg>
                                </div>
                                <div id="donate_percent"><i><?= $donation->getYearProgress() . '%' ?></i></div>
                            </a>
                        </li>

                        <li id="nav_profile" class="brackets cmp-dropdown-menu">
                            <a href='#' class="trigger icon-container profile">
                                <img class="avatar" src="<?= $Avatar ?>" />
                                <span class="username"><?= G::$LoggedUser['Username'] ?></span>
                                <svg class="menu-extend icon" width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12 17a1.72 1.72 0 0 1-1.33-.64l-4.21-5.1a2.1 2.1 0 0 1-.26-2.21A1.76 1.76 0 0 1 7.79 8h8.42a1.76 1.76 0 0 1 1.59 1.05 2.1 2.1 0 0 1-.26 2.21l-4.21 5.1A1.72 1.72 0 0 1 12 17z" fill="currentColor" fill-rule="nonzero" />
                                </svg>
                            </a>
                            <div class="items">
                                <a class="item profile" href="user.php?id=<?= G::$LoggedUser['ID'] ?>"><?= Lang::get('global', 'profile') ?></a>
                                <a class="item settings" href="user.php?action=edit&amp;userid=<?= G::$LoggedUser['ID'] ?>"><?= Lang::get('global', 'setting') ?></a>
                                <a class="item inbox" href="<?= Inbox::get_inbox_link(); ?>"> <?= Lang::get('global', 'inbox') ?></a>
                                <a class="item staffpm" href="staffpm.php"> <?= Lang::get('global', 'staffpm') ?></a>
                                <? if (ENABLE_BADGE) { ?> <a class="item badges" href="badges.php"> <?= Lang::get('global', 'my_badges') ?></a>
                        </li> <?  } ?>
                    <a class="item uploaded" href="torrents.php?type=uploaded&amp;userid=<?= G::$LoggedUser['ID'] ?>"> <?= Lang::get('global', 'my_uploaded') ?></a>
                    <a class="item bookmarks" href="bookmarks.php?type=torrents"> <?= Lang::get('global', 'my_bookmarks') ?></a>
                    <? if (check_perms('site_torrents_notify')) { ?> <a class="item notify" href="user.php?action=notify"> <?= Lang::get('global', 'my_notify') ?></a> <?    } ?>
                    <?
                    $ClassNames = $NewSubscriptions ? 'new-subscriptions' : '';
                    $ClassNames = trim($ClassNames . Format::add_class($PageID, array('userhistory', 'subscriptions'), 'active', false));
                    ?>
                    <a class="item subscriptions <?= $ClassNames ?>" href="userhistory.php?action=subscriptions"> <?= Lang::get('global', 'my_subscriptions') ?></a>
                    <a class="item comments" href="comments.php"> <?= Lang::get('global', 'my_comments') ?></a>
                    <a class="item friends" href="friends.php"> <?= Lang::get('global', 'my_friends') ?></a>
                    <a class="item missing" href="torrents.php?type=missing"> <?= Lang::get('global', 'missing') ?></a>
                    <? if (isset(G::$LoggedUser['SSPAccess'])) { ?> <a class="item ssp" href="ssp.php"> <?= Lang::get('global', 'ssp') ?></a> <?  } ?>
                    <a class="item logout" href="logout.php?auth=<?= G::$LoggedUser['AuthKey'] ?>"> <?= Lang::get('global', 'logout') ?></a>
                </div>
                </li>
                </ul>
            </div>


            <div id="logo">
                <a href="index.php"></a>
            </div>

            <?
            if (!empty($Alerts) || !empty($ModBar)) { ?>
                <div id="alerts">
                    <? foreach ($Alerts as $Alert) { ?>
                        <div class="alertbar notice"><?= $Alert ?></div>
                    <?
                    }
                    if (!empty($ModBar)) { ?>
                        <div class="alertbar admin">
                            <?= implode(' | ', $ModBar);
                            echo "\n" ?>
                        </div>
                    <?    } ?>
                </div>
            <?  } ?>

            <?
            if (isset(G::$LoggedUser['SearchType']) && G::$LoggedUser['SearchType']) { // Advanced search
                $UseAdvancedSearch = true;
            } else {
                $UseAdvancedSearch = false;
            }
            ?>
            <div id="searchbars">
                <ul>
                    <li id="searchbar_torrents">
                        <span class="hidden">Torrents: </span>
                        <form class="search_form" name="torrents" action="torrents.php" method="get">
                            <? if ($UseAdvancedSearch) { ?>
                                <input type="hidden" name="action" value="advanced" />
                            <?    } ?>
                            <input class="cmp-input" id="torrentssearch" accesskey="t" spellcheck="false" onfocus="if (this.value == 'Torrents') { this.value = ''; }" onblur="if (this.value == '') { this.value = 'Torrents'; }" value="Torrents" placeholder="Torrents" type="text" name="<?= $UseAdvancedSearch ? 'groupname' : 'searchstr' ?>" size="17" />
                        </form>
                    </li>
                    <li id="searchbar_artists">
                        <span class="hidden">Artist: </span>
                        <form class="search_form" name="artists" action="artist.php" method="get">
                            <input class="cmp-input" id="artistsearch" <?= Users::has_autocomplete_enabled('search');
                                                                        ?> accesskey="a" spellcheck="false" autocomplete="off" onfocus="if (this.value == 'Artists') { this.value = ''; }" onblur="if (this.value == '') { this.value = 'Artists'; }" value="Artists" placeholder="Artists" type="text" name="artistname" size="17" />
                        </form>
                    </li>
                    <li id="searchbar_requests">
                        <span class="hidden">Requests: </span>
                        <form class="search_form" name="requests" action="requests.php" method="get">
                            <input class="cmp-input" id="requestssearch" spellcheck="false" accesskey="r" onfocus="if (this.value == 'Requests') { this.value = ''; }" onblur="if (this.value == '') { this.value = 'Requests'; }" value="Requests" placeholder="Requests" type="text" name="search" size="17" />
                        </form>
                    </li>
                    <li id="searchbar_forums">
                        <span class="hidden">Forums: </span>
                        <form class="search_form" name="forums" action="forums.php" method="get">
                            <input value="search" type="hidden" name="action" />
                            <input class="cmp-input" id="forumssearch" accesskey="f" onfocus="if (this.value == 'Forums') { this.value = ''; }" onblur="if (this.value == '') { this.value = 'Forums'; }" value="Forums" placeholder="Forums" type="text" name="search" size="17" />
                        </form>
                    </li>
                    <!--
                    <li id="searchbar_wiki">
                        <span class="hidden">Wiki: </span>
                        <form class="search_form" name="wiki" action="wiki.php" method="get">
                            <input type="hidden" name="action" value="search" />
                            <input
                                    onfocus="if (this.value == 'Wiki') { this.value = ''; }"
                                    onblur="if (this.value == '') { this.value = 'Wiki'; }"
                                    value="Wiki" placeholder="Wiki" type="text" name="search" size="17" />
                        </form>
                    </li>
-->
                    <li id="searchbar_log">
                        <span class="hidden">Log: </span>
                        <form class="search_form" name="log" action="log.php" method="get">
                            <input class="cmp-input" id="logsearch" accesskey="l" onfocus="if (this.value == 'Log') { this.value = ''; }" onblur="if (this.value == '') { this.value = 'Log'; }" value="Log" placeholder="Log" type="text" name="search" size="17" />
                        </form>
                    </li>
                    <li id="searchbar_users">
                        <span class="hidden">Users: </span>
                        <form class="search_form" name="users" action="user.php" method="get">
                            <input type="hidden" name="action" value="search" />
                            <input class="cmp-input" id="userssearch" accesskey="u" onfocus="if (this.value == 'Users') { this.value = ''; }" onblur="if (this.value == '') { this.value = 'Users'; }" value="Users" placeholder="Users" type="text" name="search" size="20" />
                        </form>
                    </li>
                </ul>
            </div>


            <div id="menu">
                <h4 class="hidden">Site Menu</h4>
                <ul>
                    <li id="nav_index" <?=
                                        Format::add_class($PageID, array('index'), 'active', true) ?>>
                        <a href="index.php">
                            <?= Lang::get('global', 'index') ?></a>
                    </li>
                    <li id="nav_torrents" <?=
                                            Format::add_class($PageID, array('torrents', false, false), 'active', true) ?>>
                        <a href="torrents.php">
                            <?= Lang::get('global', 'torrents') ?></a>
                    </li>
                    <?
                    if (ENABLE_COLLAGES) {
                    ?>
                        <li id="nav_collages" <?=
                                                Format::add_class($PageID, array('collages'), 'active', true) ?>>
                            <a href="collages.php">
                                <?= Lang::get('global', 'collages') ?></a>
                        </li>
                    <?
                    }
                    ?>
                    <li id="nav_requests" <?=
                                            Format::add_class($PageID, array('requests'), 'active', true) ?>>
                        <a href="requests.php">
                            <?= Lang::get('global', 'requests') ?></a>
                    </li>
                    <li id="nav_subtitles" <?=
                                            Format::add_class($PageID, array('subtitles'), 'active', true) ?>>
                        <a href="subtitles.php">
                            <?= Lang::get('global', 'subtitles') ?></a>
                    </li>
                    <li id="nav_forums" <?=
                                        Format::add_class($PageID, array('forums'), 'active', true) ?>>
                        <a href="forums.php">
                            <?= Lang::get('global', 'forums') ?></a>
                    </li>
                    <!-- <li id="nav_irc" <?=
                                            Format::add_class($PageID, array('chat'), 'active', true) ?>>
                        <a href="wiki.php?action=article&name=irc">IRC</a>
                    </li> -->
                    <li id="nav_top10" <?=
                                        Format::add_class($PageID, array('top10'), 'active', true) ?>>
                        <a href="top10.php">
                            <?= Lang::get('global', 'top_10') ?></a>
                    </li>
                    <li id="nav_rules" <?=
                                        Format::add_class($PageID, array('rules'), 'active', true) ?>>
                        <a href="rules.php">
                            <?= Lang::get('global', 'rules') ?></a>
                    </li>
                    <li id="nav_wiki" <?=
                                        Format::add_class($PageID, array('wiki'), 'active', true) ?>>
                        <a href="wiki.php">
                            <?= Lang::get('global', 'wiki') ?></a>
                    </li>
                    <li id="nav_staff" <?=
                                        Format::add_class($PageID, array('staff'), 'active', true) ?>>
                        <a href="staff.php">
                            <?= Lang::get('global', 'staff') ?></a>
                    </li>
                </ul>
            </div>

        </div>

        <div id="content">