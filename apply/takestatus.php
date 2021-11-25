<?
fromIndex();
if (!isset($_POST['email']) || !isEmail($_POST['email'])) {
    header('HTTP/1.1 403 Forbidden');
    die();
}
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US" lang="en-US">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?= Lang::get('pub', 'apply_status_query') ?> :: GreatPosterWall</title>
    <link rel="stylesheet" href="style/join.css" type="text/css" />
    <script type="text/javascript" src="scripts/gen_validatorv31.js"></script>
    <link rel="STYLESHEET" type="text/css" href="style/pwdwidget.css" />
    <script src="scripts/pwdwidget.js" type="text/javascript"></script>
</head>

<body>
    <div id="container">
        <header>
            <div id="header">
                <img id="logo" src="https://greatposterwall.com/static/styles/public/images/loginlogo.png" />
                <h1 id="web-title"><?= Lang::get('pub', 'apply_status_query') ?></h1>
            </div>
        </header>
        <div id="main">
            <div class="container">
                <?
                $Email = db_string($_POST['email']);
                $DisplayStatus = Lang::get('pub', 'apply_status_queuing');
                $DB->query(
                    "SELECT count(1), `apply_status`, `waring` 
    FROM `register_apply` 
    WHERE `email` = '$Email' and apply_pw='$ApplyKey'"
                );
                list($HasApply, $ApplyStatus, $Waring) = $DB->next_record(MYSQLI_BOTH, false);
                $DB->query(
                    "SELECT count(1), `Expires` 
    FROM `invites` 
    WHERE `Email` = '$Email';"
                );
                list($HasInvites, $Expires) = $DB->next_record(MYSQLI_BOTH, false);
                $status = 0;
                if ($HasApply) {
                    switch ($ApplyStatus) {
                        case 0:
                        case 3:
                            $DisplayStatus = Lang::get('pub', 'apply_status_queuing');
                            break;
                        case 1:
                            $DisplayStatus = Lang::get('pub', 'apply_status_passed');
                            $status = 1;
                            if (!$HasInvites) $status = 2;
                            break;
                        case 2:
                            $DisplayStatus = Lang::get('pub', 'apply_status_rejected');
                            break;
                        case 4:
                            $DisplayStatus = Lang::get('pub', 'apply_status_added');
                            $status = 4;
                            break;
                        case 5:
                            $DisplayStatus = Lang::get('pub', 'apply_status_incomplete');
                            $status = 5;
                            break;
                        default:
                            $DisplayStatus = Lang::get('pub', 'apply_status_failed');
                            break;
                    }
                ?>
                    <div class="card" id="status-answer">
                        <div style="width: 100%">
                            <ul>
                                <li><?= Lang::get('pub', 'application_email') ?>: <?= $_POST['email'] ?></li>
                                <li><?= Lang::get('pub', 'apply_status') ?>: <?= $DisplayStatus ?></li>
                            </ul>
                            <?
                            if ($status == 1) {
                            ?>
                                <br><br>
                                <span><?= Lang::get('pub', 'register_link_sent') ?></span>
                                <br>
                                <span><?= Lang::get('pub', 'invite_deadline') ?>: <?= $Expires ?></span>
                            <?
                            } else if ($status == 2) {
                            ?>
                                <br><br>
                                <span><?= Lang::get('pub', 'register_successfully') ?></span>
                            <?
                            } elseif ($status == 4) {
                            ?>
                                <form method='post' accept-charset='UTF-8'>
                                    <div style="width: 100%;">
                                        <h5 style="margin-top: 40px; margin-bottom: 20px;"><?= Lang::get('pub', 'application_add_info') ?></h5>
                                        <ul>
                                            <li>
                                                <ul class="remarks">
                                                    <li><?= Lang::get('pub', 'application_screenshots_note_1') ?></li>
                                                    <li><?= Lang::get('pub', 'application_add_info_1') ?></li>
                                                    <li><?= Lang::get('pub', 'application_add_info_2') ?></li>
                                                    <li><?= Lang::get('pub', 'application_add_info_3') ?></li>
                                                    <li><?= Lang::get('pub', 'application_add_info_4') ?></li>
                                                    <li><?= Lang::get('pub', 'application_clients_note_4') ?></li>
                                                </ul>
                                                <?= base64_decode($Waring) ?>
                                                <textarea id="addnote" name="addnote" placeholder="<?= Lang::get('pub', 'application_add_info_placeholder') ?>" rows="15"></textarea>
                                            </li>
                                        </ul>
                                    </div>
                                    <div align="center">
                                        <input type="hidden" name="action" value="status" />
                                        <input type="hidden" name="email" value="<?= $_POST['email'] ?>" />
                                        <input type="submit" id="submit3" value="提交" />
                                    </div>
                                </form>
                            <?
                            }
                            ?>
                        </div>
                    </div>
                <?
                } else {
                ?>
                    <div class="card" id="wrong-email-or-pw"><?= Lang::get('pub', 'email_does_not_exist') ?></div>
                <?
                }
                ?>

            </div>
        </div>
        <? include('footer.php'); ?>
    </div>
</body>

</html>