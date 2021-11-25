<?
$TorrentID = (int)$_GET['torrentid'];

$DB->query("
	SELECT last_action, LastReseedRequest, UserID, Time, GroupID
	FROM torrents
	WHERE ID = '$TorrentID'");
list($LastActive, $LastReseedRequest, $UploaderID, $UploadedTime, $GroupID) = $DB->next_record();

if (!check_perms('users_mod')) {
    if (time() - strtotime($LastReseedRequest) < 864000) {
        error(Lang::get('torrents', 'already_a_re_seed_request'));
    }
    if ($LastActive == '0000-00-00 00:00:00' || time() - strtotime($LastActive) < 345678) {
        error(403);
    }
}

$DB->query("
	UPDATE torrents
	SET LastReseedRequest = NOW()
	WHERE ID = '$TorrentID'");

$Group = Torrents::get_groups(array($GroupID));
extract(Torrents::array_group($Group[$GroupID]));

$Name = Artists::display_artists(array('1' => $Artists), false, true);
$Name .= $GroupName;

$usersToNotify = array();

$DB->query("
	SELECT s.uid AS id, MAX(s.tstamp) AS tstamp
	FROM xbt_snatched as s
	INNER JOIN users_main as u
	ON s.uid = u.ID
	WHERE s.fid = '$TorrentID'
	AND u.Enabled = '1'
	GROUP BY s.uid
       ORDER BY tstamp DESC
	LIMIT 100");
if ($DB->has_results()) {
    $Users = $DB->to_array();
    foreach ($Users as $User) {
        $UserID = $User['id'];
        $TimeStamp = $User['tstamp'];

        $usersToNotify[$UserID] = array("snatched", $TimeStamp);
    }
}

$usersToNotify[$UploaderID] = array("uploaded", strtotime($UploadedTime));

foreach ($usersToNotify as $UserID => $info) {
    $Username = Users::user_info($UserID)['Username'];
    list($action, $TimeStamp) = $info;

    $Request = "你好，$Username：

用户 [url=" . site_url() . "user.php?id=$LoggedUser[ID]]$LoggedUser[Username][/url] 为你在 " . date('M d Y', $TimeStamp) . " " . $action . " 的种子 [url=" . site_url() . "torrents.php?id=$GroupID&torrentid=$TorrentID]{$Name}[/url] 发送了一个续种请求。该种子现在无人做种，我们需要你来帮忙复活它！

在各个客户端上，续种操作会有所不同，但原理一致。思路是下载种子文件，然后在客户端中加载，然后指向内容文件存放的位置，然后执行完整性校验。

谢谢你！

--------------------------------------------------------------------------------
	
Hi $Username,

The user [url=" . site_url() . "user.php?id=$LoggedUser[ID]]$LoggedUser[Username][/url] has requested a re-seed for the torrent [url=" . site_url() . "torrents.php?id=$GroupID&torrentid=$TorrentID]{$Name}[/url], which you " . $action . " on " . date('M d Y', $TimeStamp) . ". The torrent is now un-seeded, and we need your help to resurrect it!

The exact process for re-seeding a torrent is slightly different for each client, but the concept is the same. The idea is to download the torrent file and open it in your client, and point your client to the location where the data files are, then initiate a hash check.

Thanks!";

    Misc::send_pm($UserID, 0, "续种请求 | Re-seed request for torrent $Name", $Request);
}

$NumUsers = count($usersToNotify);

View::show_header();
?>
<div class="thin">
    <div class="header">
        <h2><?= Lang::get('torrents', 'successfully_sent_re_seed_request') ?></h2>
    </div>
    <div class="box pad thin">
        <p><?= Lang::get('torrents', 'successfully_sent_re_seed_request_for_torrent') ?><a href="torrents.php?id=<?= $GroupID ?>&torrentid=<?= $TorrentID ?>"><?= display_str($Name) ?></a><?= Lang::get('torrents', 'space_to_space') ?><?= $NumUsers ?><?= Lang::get('torrents', 'n_user') ?><?= $NumUsers === 1 ? '' : Lang::get('torrents', 's'); ?><?= Lang::get('torrents', 'period') ?></p>
    </div>
</div>
<?
View::show_footer();
?>