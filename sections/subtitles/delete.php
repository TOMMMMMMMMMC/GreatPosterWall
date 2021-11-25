<?

if (!isset($_GET['id']) || !is_number($_GET['id'])) {
    error(404);
}

$Action = $_GET['action'];
if ($Action !== 'delete') {
    error(404);
}

if ($LoggedUser['ID'] != $UserID && !check_perms('torrents_delete')) {
    error(403);
}
View::show_header(ucwords($Action) . ' Subtitle');

?>
<div class="thin center">
    <div class="box" style="width: 600px; margin: 0px auto;">
        <div class="head colhead">
            <?= ucwords($Action) ?> Subtitle
        </div>
        <div class="pad">
            <form class="<?= (($Action === 'delete') ? 'delete_form' : 'edit_form') ?>" name="request" action="subtitles.php" method="post">
                <input type="hidden" name="action" value="take<?= $Action ?>" />
                <input type="hidden" name="auth" value="<?= $LoggedUser['AuthKey'] ?>" />
                <input type="hidden" name="id" value="<?= $_GET['id'] ?>" />
                <? if ($Action == 'delete') { ?>
                    <div class="warning"><?= Lang::get('subtitles', 'delete_subtitle_warning') ?></div>
                <?  } ?>
                <strong><?= Lang::get('subtitles', 'reason') ?>:</strong>
                <input type="text" name="reason" size="30" />
                <input value="<?= ucwords($Action) ?>" type="submit" />
            </form>
        </div>
    </div>
</div>
<?
View::show_footer();
?>