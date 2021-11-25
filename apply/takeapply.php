<?
fromIndex();
function applyError($Label) {
?>
    <div class="card" id="status-feedback-warning">
        <p><?= Lang::get('pub', $Label) ?></p>
    </div>
    </div>
    <? include('footer.php'); ?>
    </div>
    </body>

    </html>
<?
    die();
}
$Keys = ['email', 'site', 'screenshot1', 'screenshot2', 'introduction'];
foreach ($Keys as $Key) {
    if (!isset($_POST[$Key])) {
        header('HTTP/1.1 403 Forbidden');
        die();
    }
}
$Email = $_POST['email'];
$site = $_POST['site'];
$site_ss = $_POST['screenshot1'];
$client_ss = $_POST['screenshot2'];
$introduction = $_POST['introduction'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US" lang="en-US">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?= Lang::get('pub', 'apply_to_join') ?> :: GreatPosterWall</title>
    <link rel="stylesheet" href="style/join.css" type="text/css" />
    <script type="text/javascript" src="scripts/gen_validatorv31.js"></script>
    <script src="scripts/lang.js" type="text/javascript"></script>
    <link rel="STYLESHEET" type="text/css" href="style/pwdwidget.css" />
    <script src="scripts/pwdwidget.js" type="text/javascript"></script>
</head>

<body>
    <div id="head">
        <select name="language" id="language" onchange="change_lang(this.options[this.options.selectedIndex].value)">
            <option value="chs" <? if (empty($_COOKIE['lang']) || $_COOKIE['lang'] == 'chs') { ?>selected<? } ?>>简体中文</option>
            <option value="en" <? if (!empty($_COOKIE['lang']) && $_COOKIE['lang'] == 'en') { ?>selected<? } ?>>English</option>
        </select>
    </div>
    <!-- Form Code Start -->
    <div id="container">
        <header>
            <div id="header">
                <img id="logo" src="https://greatposterwall.com/static/styles/public/images/loginlogo.png" />
                <h1 id="web-title"><?= Lang::get('pub', 'application_status') ?></h1>
            </div>
        </header>
        <div id="main">
            <div class="container">
                <?
                $c_red = 0;
                $c_ops = 0;
                $c_nwcd = 0;
                $c_opencd = 0;
                $c_others = 0;
                if (isset($_POST['red'])) {
                    $c_red = $_POST['red'] == 'on' ? '1' : '0';
                }
                if (isset($_POST['ops'])) {
                    $c_ops = $_POST['ops'] == 'on' ? '1' : '0';
                }
                if (isset($_POST['nwcd'])) {
                    $c_nwcd = $_POST['nwcd'] == 'on' ? '1' : '0';
                }
                if (isset($_POST['opencd'])) {
                    $c_opencd = $_POST['opencd'] == 'on' ? '1' : '0';
                }
                if (isset($_POST['other'])) {
                    $c_others = $_POST['other'] == 'on' ? '1' : '0';
                }

                if (!isEmail($Email)) {
                    applyError('wrong_email');
                }
                $NotAllowedEmails = ['protonmail'];
                $EmailBox = explode('@', $Email);
                if (in_array($EmailBox[1], $NotAllowedEmails)) {
                    applyError('not_allowed_email');
                }
                if (!isURL($site_ss) || !isURL($client_ss)) {
                    applyError('wrong_image_urls');
                }
                if (mb_strlen($introduction, 'utf8') >= 25) {
                    $introduction = base64_encode($introduction);
                } else {
                    applyError('less_than_25');
                }
                if ($Email != $ApplyEmail) {
                    applyError('email_neq_applyemail');
                }

                $IP = getRealIp();
                if ($IP != -1) {
                    $IP = db_string($IP);
                    if (!strpos($IP, ":")) {
                        $ipv4 = $IP;
                        $ipv6 = null;
                        $DB->query("SELECT `ipv4`, `ID` FROM `register_apply` WHERE `ipv4` = '$ipv4';");
                        $HasApply = $DB->record_count();
                        if ($HasApply) {
                            applyError('duplicated_ip');
                        }
                    } else {
                        $ipv4 = null;
                        $ipv6 = $IP;
                        $DB->query("SELECT `ipv6`, `ID` FROM `register_apply` WHERE `ipv6` = '$ipv6';");
                        $HasApply = $DB->record_count();
                        if ($HasApply) {
                            applyError('duplicated_ip');
                        }
                    }
                } else {
                    applyError('wrong_ip');
                }

                $DB->query("SELECT `email` FROM `register_apply` WHERE `email` = '" . db_string($Email) . "' and `ts` > '" . OPEN_APPLY_FROM . "'");
                $HasApply = $DB->record_count();
                if ($HasApply) {
                    applyError('existed_email');
                }

                $DB->query(
                    "INSERT INTO `register_apply`
            (`email`, `site`,`ipv4`, `ipv6`, `site_ss`, `client_ss`, `introduction`, `apply_pw`, `ts`, `c_red`, `c_ops`, `c_nwcd`, `c_opencd`, `c_others`)
    VALUES ('" . db_string($Email) . "', '" . db_string($site) . "','" . db_string($ipv4) . "', '" . db_string($ipv6) . "', '" . db_string($site_ss) . "', '" . db_string($client_ss) . "', '" . db_string($introduction) . "', '" . db_string($ApplyKey) . "', now(), '" . db_string($c_red) . "', '" . db_string($c_ops) . "', '" . db_string($c_nwcd) . "', '" . db_string($c_opencd) . "', '" . db_string($c_others) . "');"
                );
                $DB->query("UPDATE register_apply_link set Used=1 where ApplyKey='$ApplyKey'");
                ?>
                <div class="card" id="status-feedback">
                    <p style="font-size: 20px;"><?= Lang::get('pub', 'application_submitted_successfully') ?></p>
                    <br />
                    <p><?= Lang::get('pub', 'application_submitted_successfully_feedback') ?></p>
                    <ul>
                        <li><?= Lang::get('pub', 'application_email') ?>: <?= $Email ?></li>
                    </ul>
                    <p><?= Lang::get('pub', 'query_information_note') ?></p>
                </div>
            </div>
            <? include('footer.php'); ?>
        </div>
</body>

</html>