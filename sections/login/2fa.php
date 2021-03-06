<? View::show_header('Two-factor Authentication'); ?>
<span id="no-cookies" class="hidden warning">You appear to have cookies disabled.<br /><br /></span>
<noscript><span class="warning"><?= SITE_NAME ?> requires JavaScript to function properly. Please enable JavaScript in your browser.</span><br /><br />
</noscript>
<?
if (strtotime($BannedUntil) < time()) {
?>
    <form class="auth_form" name="login" id="loginform" method="post" action="login.php?act=2fa">
        <?

        if (!empty($BannedUntil) && $BannedUntil != '0000-00-00 00:00:00') {
            $DB->query("
			UPDATE login_attempts
			SET BannedUntil = '0000-00-00 00:00:00', Attempts = '0'
			WHERE ID = '" . db_string($AttemptID) . "'");
            $Attempts = 0;
        }
        if (isset($Err)) {
        ?>
            <span class="warning"><?= $Err ?><br /><br /></span>
        <? } ?>
        <? if ($Attempts > 0) { ?>
            You have <span class="info"><?= (6 - $Attempts) ?></span> attempts remaining.<br /><br />
            <strong>WARNING:</strong> You will be banned for 6 hours after your login attempts run out!<br /><br />
        <? } ?>
        <table class="layout">
            <tr>
                <td>2FA Key&nbsp;</td>
                <td colspan="2">
                    <input type="text" name="2fa" id="2fa" class="inputtext" required="required" maxlength="6" pattern="[0-9]{6}" autofocus="autofocus" placeholder="Two-factor Auth Key" />
                </td>
            </tr>

            <tr>
                <td></td>
                <td><input type="submit" name="login" value="Log in" class="submit" /></td>
            </tr>
        </table>
    </form>
    <br /><br />
    <a href="login.php?act=2fa_recovery" class="tooltip" title="Use 2FA Recovery Code">Use a recovery key?</a>
<?
} else {
?>
    <span class="warning">You are banned from logging in for another <?= time_diff($BannedUntil) ?>.</span>
<?
}

?>
<script type="text/javascript">
    cookie.set('cookie_test', 1, 1);
    if (cookie.get('cookie_test') != null) {
        cookie.del('cookie_test');
    } else {
        $('#no-cookies').gshow();
    }
</script>
<? View::show_footer(); ?>