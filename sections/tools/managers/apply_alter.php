<?
authorize();

if (!check_perms('admin_interviewer')) {
    error(403);
}
$P = db_array($_POST);
if ($_POST['submit'] == 'Agree') {
    $DB->query("select * from register_apply where `ID` = '" . $P['id'] . "' and `apply_status` = '1';");
    if ($DB->record_count() == 0) {
        $DB->query("
		INSERT INTO register_apply_log (UserID, ApplyID, ApplyStatus) 
		VALUES (" . $LoggedUser['ID'] . ", " . $P['id'] . ", 1)");

        $DB->query("UPDATE `register_apply` SET `apply_status` = '1', `note` = '" . base64_encode($P['note']) . "',  `ts` = `ts`,  `ts_mod` = now(), `id_mod` = '" . $LoggedUser['ID'] . "' WHERE `ID` = '" . $P['id'] . "';");

        $InviteKey = db_string(Users::make_secret());
        $SiteName = SITE_NAME;
        $SiteURL = site_url();
        $InviteExpires = time_plus(60 * 60 * 24 * 3); // 3 days
        $InviteReason = '';
        $Email = $P['email'];

        $Message = <<<EOT
恭喜你的申请通过审核！现邀请你加入 GreatPosterWall。

$SiteName 是一个非盈利性质的音乐 PT，严禁出售、交换和公开赠送邀请。通过这些途径注册的账号将会被封禁，且永远失去合规注册的机会。

如果你以前在 $SiteName 有账号，则请勿通过此邀请再次注册。正确的做法是在网站上直接申请恢复账号。

欲确认邀请，请单击下方的链接：

{$SiteURL}register.php?invite=$InviteKey

注册并激活账号后，即可顺利登录站点。请注意，如果你在 3 天内没能确认邀请，它会过期失效。我们强烈建议你在进站后立即阅读规则和 Wiki。

-----------------------------------

Congratulations! Your application has passed our review. Now you are invited to join GreatPosterWall.

Please note that selling invites, trading invites, and giving invites away publicly (e.g. on a forum) is strictly forbidden. Accounts registered from these ways will be banned and lose chances of ever signing up legitimately.

If you have previously had an account at $SiteName, do not use this invite. Instead, please submit a re-enable request on our site.

To confirm your invite, click on the following link:

{$SiteURL}register.php?invite=$InviteKey

After you register, you will be able to use your account. Please take note that if you do not use this invite in the next 3 days, it will expire. We urge you to read the RULES and the wiki immediately after you join.


Thank you, 
$SiteName Staff
EOT;

        $DB->query("INSERT INTO invites (InviterID, InviteKey, Email, Expires, Reason) 
	VALUES ('412', '$InviteKey', '$Email', '$InviteExpires', '$InviteReason')");

        Misc::send_email($Email, '你有一封来自 ' . SITE_NAME . ' 的邀请函 | You have been invited to ' . SITE_NAME, $Message, 'noreply');
    }
} elseif ($_POST['submit'] == 'Refuse') {
    $DB->query("SELECT `apply_status` FROM `register_apply` WHERE `ID` = '" . $P['id'] . "'");
    $sort0 = $DB->to_array(false, MYSQLI_NUM, false);
    if ($sort0[0][0] != 1) {
        $DB->query("
			INSERT INTO register_apply_log (UserID, ApplyID, ApplyStatus) 
			VALUES (" . $LoggedUser['ID'] . ", " . $P['id'] . ", 2)");
        $DB->query("UPDATE `register_apply` SET `apply_status` = '2', `note` = '" . base64_encode($P['note']) . "',  `ts` = `ts`,  `ts_mod` = now(), `id_mod` = '" . $LoggedUser['ID'] . "' WHERE `ID` = '" . $P['id'] . "';");
    }
} elseif ($_POST['submit'] == 'Pending') {
    $DB->query("SELECT `apply_status` FROM `register_apply` WHERE `ID` = '" . $P['id'] . "'");
    $sort0 = $DB->to_array(false, MYSQLI_NUM, false);
    if ($sort0[0][0] != 1) {
        $DB->query("
			INSERT INTO register_apply_log (UserID, ApplyID, ApplyStatus) 
			VALUES (" . $LoggedUser['ID'] . ", " . $P['id'] . ", 3)");
        $DB->query("UPDATE `register_apply` SET `apply_status` = '3', `note` = '" . base64_encode($P['note']) . "',  `ts` = `ts`,  `ts_mod` = now(), `id_mod` = '" . $LoggedUser['ID'] . "' WHERE `ID` = '" . $P['id'] . "';");
    }
} elseif ($_POST['submit'] == 'Add') {
    $DB->query("SELECT `apply_status` FROM `register_apply` WHERE `ID` = '" . $P['id'] . "'");
    $sort0 = $DB->to_array(false, MYSQLI_NUM, false);
    if ($sort0[0][0] != 1) {
        $DB->query("
			INSERT INTO register_apply_log (UserID, ApplyID, ApplyStatus) 
			VALUES (" . $LoggedUser['ID'] . ", " . $P['id'] . ", 4)");
        $DB->query("UPDATE `register_apply` SET `apply_status` = '4', `note` = '" . base64_encode($P['note']) . "', `waring` = '" . base64_encode($P['waring']) . "', `ts` = `ts`,  `ts_mod` = now(), `id_mod` = '" . $LoggedUser['ID'] . "' WHERE `ID` = '" . $P['id'] . "';");
    }
}

// Go back
$url = "tools.php?action=apply_list";
if (isset($P['apply_status'])) {
    $url = $url . "&status=" . $P['apply_status'];
}
header("Location: $url");
