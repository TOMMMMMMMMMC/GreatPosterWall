<?
fromIndex();
if (isset($_POST['addnote'])) {
    $DB->query(
        "UPDATE register_apply
    SET apply_status = '5',addnote = '" . base64_encode($_POST['addnote']) . "'
    WHERE email = '" . db_string($_POST['email']) . "'"
    );
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
    <!-- Form Code Start -->
    <div id="container">
        <header>
            <div id="header">
                <img id="logo" src="https://greatposterwall.com/static/styles/public/images/loginlogo.png" />
                <h1 id="web-title"><?= Lang::get('pub', 'apply_status_query') ?></h1>
            </div>
        </header>
        <div id="main">
            <div class="container">
                <form action="index.php" method='post' accept-charset='UTF-8'>
                    <div class="card" id="status-searcher">
                        <div id="apply-em">
                            <?= Lang::get('pub', 'application_email') ?>: <input id="apply-em-input" type="text" name="email" value="" />
                        </div>
                        <!--
                        <div id="apply-pw">
                            <?= Lang::get('pub', 'query_password') ?>: <input id="apply-pw-input" type="text" name="applykey" value="" />
                        </div>
                        -->
                        <input type="hidden" name="action" value="takestatus">
                        <input type="submit" id="submit2" value="提交" />
                    </div>
                </form>
            </div>
        </div>
        <? include('footer.php'); ?>
    </div>
</body>

</html>