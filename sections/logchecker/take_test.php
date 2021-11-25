<?
//ini_set('upload_max_filesize',1000000);
enforce_login();

$ValidateChecksum = true;
if (isset($_FILES['log']) && is_uploaded_file($_FILES['log']['tmp_name'])) {
    $File = $_FILES['log'];
} elseif (!empty($_POST["pastelog"])) {
    $ValidateChecksum = false;
    $TmpFile = tempnam('/tmp', 'log_');
    file_put_contents($TmpFile, $_POST["pastelog"]);
    $File = array('tmp_name' => $TmpFile, 'name' => $TmpFile);
} else {
    error('No log file uploaded or file is empty.');
}


View::show_header(Lang::get('logchecker', 'logchecker'));
?>

<div class="linkbox">
    <a href="logchecker.php" class="brackets"><?= Lang::get('logchecker', 'test_another_log') ?></a>
    <a href="logchecker.php?action=upload" class="brackets"><?= Lang::get('logchecker', 'upload_missing_logs') ?></a>
</div>
<div class="thin">
    <h2 class="center"><?= Lang::get('logchecker', 'test_results') ?></h2>

    <?
    $Log = new Logchecker();
    $Log->validateChecksum($ValidateChecksum);
    $Log->new_file($File['tmp_name']);

    list($Score, $Bad, $Checksum, $Text) = $Log->parse();
    $Bad = Logchecker::translateDetail($Bad);
    //file_put_contents('/tmp/Log',date("Y-m-d h:i:s",time())." take_test.php  Score=$Score, Bad=$Bad, Checksum=$Checksum\r\n",FILE_APPEND);

    if ($Score == 100) {
        $Color = '#418B00';
    } elseif ($Score > 90) {
        $Color = '#74C42E';
    } elseif ($Score > 75) {
        $Color = '#FFAA00';
    } elseif ($Score > 50) {
        $Color = '#FF5E00';
    } else {
        $Color = '#FF0000';
    }
    /*
if (!$Checksum) {
    echo <<<HTML
    <blockquote>
        <strong>Trumpable For:</strong>
        <br /><br />
        Bad/No Checksum(s)
    </blockquote>
HTML;
}
*/
    ?>
    <blockquote>
        <strong><?= Lang::get('logchecker', 'log_score') ?>:</strong> <span style='color:<?= $Color ?>'><?= $Score ?></span> (<?= Lang::get('logchecker', 'out_of_100') ?>)
    </blockquote>
    <?

    if ($Bad) {
    ?>
        <blockquote>
            <h3><?= Lang::get('logchecker', 'log_validation_report') ?></h3>
            <ul>
                <?
                foreach ($Bad as $Property) {
                    echo "\t\t\t<li>{$Property}</li>";
                }
                ?>
            </ul>
        </blockquote>
    <?
    }

    ?>
    <blockquote>
        <pre><?= $Text ?></pre>
    </blockquote>
</div>
<?
View::show_footer();

if (!empty($TmpFile) && is_file($TmpFile)) {
    unlink($TmpFile);
}
