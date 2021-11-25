<?

/**
 * Hello there. If you are refactoring this code, please note that this functionality also sort of exists in /classes/referral.class.php
 * Super sorry for doing that, but this is totally not reusable.
 */

if (!$UserCount = $Cache->get_value('stats_user_count')) {
    $DB->query("
		SELECT COUNT(ID)
		FROM users_main
		WHERE Enabled = '1'");
    list($UserCount) = $DB->next_record();
    $Cache->cache_value('stats_user_count', $UserCount, 0);
}

$UserID = $LoggedUser['ID'];

//This is where we handle things passed to us
authorize();

$DB->query("
	SELECT can_leech
	FROM users_main
	WHERE ID = $UserID");
list($CanLeech) = $DB->next_record();
//SELECT count(1) FROM `invites_history` WHERE `UserID` = 198 and `Time` > date_sub(now(), INTERVAL 72 HOUR)
$TimeSpace = array();

if (isset($TimeSpace[$LoggedUser['Class']])) {
    $DB->query("
		SELECT count(1) , date_add(`Time`,INTERVAL " . $TimeSpace[$LoggedUser['Class']] . " HOUR)
		FROM `invites_history` 
		WHERE `UserID` = $UserID and 
			`Time` > date_sub(now(), INTERVAL " . $TimeSpace[$LoggedUser['Class']] . " HOUR)");
    list($HasInvinte, $Time) = $DB->next_record();
} else {
    $HasInvinte = false;
}
if (
    $LoggedUser['RatioWatch']
    || !$CanLeech
    || $LoggedUser['DisableInvites'] == '1'
    || $LoggedUser['Invites'] == 0
    && !check_perms('site_send_unlimited_invites')
    || ($UserCount >= USER_LIMIT
        && USER_LIMIT != 0
        && !check_perms('site_can_invite_always'))
) {
    error(403);
}
if ($HasInvinte) {
    error("You could invite others after $Time.", false, false, "Warning!");
}
$Email = $_POST['email'];
$Username = $LoggedUser['Username'];
$SiteName = SITE_NAME;
$SiteURL = site_url();
$InviteExpires = time_plus(60 * 60 * 24 * 3); // 3 days
$InviteReason = check_perms('users_invite_notes') ? db_string($_POST['reason']) : '';

//MultiInvite
if (strpos($Email, '|') !== false && check_perms('site_send_unlimited_invites')) {
    $Emails = explode('|', $Email);
} else {
    $Emails = array($Email);
}

foreach ($Emails as $CurEmail) {
    if (!preg_match("/^" . EMAIL_REGEX . "$/i", $CurEmail)) {
        if (count($Emails) > 1) {
            continue;
        } else {
            error('Invalid email.');
            header('Location: user.php?action=invite');
            die();
        }
    }
    $DB->query("
		SELECT Expires, InviterID
		FROM invites
		WHERE InviterID = " . $LoggedUser['ID'] . "
			AND Email LIKE '$CurEmail'");
    if ($DB->has_results()) {
        list($Expires, $InviterID) = $DB->next_record();
        if ($InviterID == $LoggedUser['ID']) {
            error('You already have a pending invite to that address!');
            header('Location: user.php?action=invite');
        } else {
            error('This email has already had an account at our site, <a href="/rules.php">do not create more than one account</a>.');
            header('Location: user.php?action=invite');
        }
        die();
    }
    $InviteKey = db_string(Users::make_secret());

    $DisabledChan = BOT_DISABLED_CHAN;
    $IRCServer = BOT_SERVER;

    $Message = <<<EOT
<style type="text/css">
#invite_mail_background{
    background-image: linear-gradient(#bbbbbc, #c4c4c4);
    padding: 20px;
}
#invite_mail_container{
	background-color: #fff;
	border-radius: 5px;
	max-width: 600px;
	margin: 0 auto;
	box-shadow: 0 0 6px 0 rgba(0, 0, 0, .1);
}
#invite_mail_head{
	border-top-left-radius: 5px;
    border-top-right-radius: 5px;
	text-align: center;
	padding: 20px 0;
	background-color: #1e2538;
}
#invite_mail_head>img{
	width: 50%;
}
#invite_mail_body{
	padding: 15px;
}
#invite_mail_body>ol{
	padding-top: 0;
	margin-top: 0;
	margin-left: 10px;
	padding-left: 10px;
}
.button_container{
	text-align: center;
	margin: 10px 0;
}
.button{
    cursor: pointer;
    outline: 0;
    transition: all .1s linear;
    background: #4285f4;
    border: none;
    border-radius: 5px;
    box-shadow: 0 0 4px 0 rgba(0, 0, 0, .2);
    color: #ffffff;
    padding: 5px 10px;
    margin: 0px 2px;
	text-decoration: none !important;
	font-size: 1.1rem;
}
.button:hover{
	background: #1958bd;
}
li.important{
	color: #d8210d
}
</style>
<div id="invite_mail_background" style="background-image: linear-gradient(#bbbbbc, #c4c4c4); padding: 20px;">
<div id="invite_mail_container" style="background-color: #fff;border-radius: 5px;max-width: 600px;margin: 0 auto;box-shadow: 0 0 6px 0 rgba(0, 0, 0, .1);">
<div id="invite_mail_head" style="border-top-left-radius: 5px;
border-top-right-radius: 5px;
text-align: center;
padding: 20px 0;
background-color: #1e2538;">
<img src="https://greatposterwall.com/static/styles/public/images/loginlogo.png" style="width: 50%;">
</div>
<div id="invite_mail_body" style="padding: 15px;">用户 $Username 邀请你加入 $SiteName 且指定了你的邮箱地址 ($CurEmail)。欲确认邀请，请单击下方的按钮：<br/>
<p class="button_container" style="text-align: center;
margin: 10px 0;"><a class="button" target="_blank" href='{$SiteURL}register.php?invite=$InviteKey' style="cursor: pointer;
outline: 0;
transition: all .1s linear;
background: #4285f4;
border: none;
border-radius: 5px;
box-shadow: 0 0 4px 0 rgba(0, 0, 0, .2);
color: #ffffff;
padding: 5px 10px;
margin: 0px 2px;
text-decoration: none !important;
font-size: 1.1rem;">注册</a></p>
注意事项：
<ol style="padding-top: 0;
margin-top: 0;
margin-left: 10px;
padding-left: 10px">
<li>此邮件的有效期为 72 小时</li>
<li class="important" style="color: #d8210d">一人一生一号。如果你已有账号且无法登录，请加入 <a href="https://t.me/joinchat/77iO6zI6CF85Zjll" target="_blank">账号问题咨询群</a>（需挂梯）</li>
<li class="important" style="color: #d8210d">通过交易获取邀请注册的用户会被封禁</li>
<li class="important" style="color: #d8210d">请使用家庭、本地网络注册，使用代理注册的用户会被封禁</li>
<li>如果你对本邮件的内容感到莫名其妙，请无视</li>
</ol>
<hr/>
<br/>
The user $Username has invited you to join $SiteName and has specified this address ($CurEmail) as your email address. To confirm your invite, click on the following button:<br/>
<p class="button_container" style="text-align: center;
margin: 10px 0;"><a class="button" target="_blank" href='{$SiteURL}register.php?invite=$InviteKey' style="cursor: pointer;
outline: 0;
transition: all .1s linear;
background: #4285f4;
border: none;
border-radius: 5px;
box-shadow: 0 0 4px 0 rgba(0, 0, 0, .2);
color: #ffffff;
padding: 5px 10px;
margin: 0px 2px;
text-decoration: none !important;
font-size: 1.1rem;">Register</a></p>
Note:
<ol style="padding-top: 0;
margin-top: 0;
margin-left: 10px;
padding-left: 10px">
<li>It will expire if you do not use this invite in the next 72 hours</li>
<li class="important" style="color: #d8210d">One person should NOT create more than one account. If you already had an account and cannot log in, please join <a href="https://t.me/joinchat/77iO6zI6CF85Zjll" target="_blank">GPW - Disabled</a> for help</li>
<li class="important" style="color: #d8210d">Users who get the account by trade will be disabled</li>
<li class="important" style="color: #d8210d">Please use the home connection to register. Users who register by proxy will be disabled</li>
<li>Please ignore this email if you don't understand</li>
</ol><br/>
Thank you, <br/>
$SiteName Staff</div>
</div></div>
EOT;

    $DB->query("
		INSERT INTO invites
			(InviterID, InviteKey, Email, Expires, Reason)
		VALUES
			('$LoggedUser[ID]', '$InviteKey', '" . db_string($CurEmail) . "', '$InviteExpires', '$InviteReason')");
    $DB->query("
		INSERT INTO invites_history
			(UserID, Email, InviteKey)
		VALUES
			('$LoggedUser[ID]', '" . db_string($CurEmail) . "', '$InviteKey')");

    if (!check_perms('site_send_unlimited_invites')) {
        $DB->query("
			UPDATE users_main
			SET Invites = GREATEST(Invites, 1) - 1
			WHERE ID = '$LoggedUser[ID]'");
        $DB->query(
            "select ID from (SELECT ID FROM `invites_typed` WHERE UserID='$LoggedUser[ID]' and Type='time' and Used = 0 ORDER BY `EndTime`) a
			union all
			select ID from (select ID FROM `invites_typed` WHERE UserID='$LoggedUser[ID]' and Type='count' and Used = 0) b 
			limit 1"
        );
        $UsedInviteID = $DB->collect('ID');
        if (count($UsedInviteID) > 0) {
            $DB->query("UPDATE `invites_typed` set Used=1 WHERE ID = '$UsedInviteID[0]'");
            $DB->query("UPDATE `invites` set InviteID='$UsedInviteID[0]' WHERE InviteKey = '$InviteKey'");
        }
        $Cache->begin_transaction('user_info_heavy_' . $LoggedUser['ID']);
        $Cache->update_row(false, array('Invites' => '-1'));
        if (count($UsedInviteID) > 0) {
            $Cache->update_row(false, array('TimedInvites' => '-1'));
        }
        $Cache->commit_transaction(0);
    }

    Misc::send_email($CurEmail, '你有一封来自 ' . SITE_NAME . ' 的邀请函 | You have been invited to ' . SITE_NAME, $Message, 'noreply', 'text/html');
}

header('Location: user.php?action=invite');
