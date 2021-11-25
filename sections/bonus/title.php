<?php

if (isset($_REQUEST['preview']) && isset($_REQUEST['title']) && isset($_REQUEST['BBCode'])) {
    echo $_REQUEST['BBCode'] === 'true'
        ? Text::full_format($_REQUEST['title'])
        : Text::strip_bbcode($_REQUEST['title']);
    die();
}

$ID = G::$LoggedUser['ID'];
$Label = $_REQUEST['label'];
if ($Label === 'title-off') {
    authorize();
    Users::removeCustomTitle($ID);
    header('Location: bonus.php?complete=' . urlencode($Label));
}
if ($Label === 'title-bb-y') {
    $BBCode = 'true';
} elseif ($Label === 'title-bb-n') {
    $BBCode = 'false';
} else {
    error(403);
}

if (isset($_POST['confirm'])) {
    authorize();
    if (!isset($_POST['title'])) {
        error(403);
    }
    if ($Bonus->purchaseTitle($ID, $Label, $_POST['title'], G::$LoggedUser['EffectiveClass'])) {
        header('Location: bonus.php?complete=' . urlencode($Label));
    } else {
        error(Lang::get('bonus', 'you_cannot_afford_this_item'));
    }
}

View::show_header(Lang::get('bonus', 'bonus_points_title'), 'bonus');
?>
<div class="thin">
    <div class="table_container border">
        <table id="custom-title-setting">
            <thead>
                <tr>
                    <td><?= Lang::get('bonus', 'custom_title') ?>, <?= ($BBCode === 'true') ? Lang::get('bonus', 'custom_title') : Lang::get('bonus', 'no_bbcode_allowed') ?> - <?= number_format($Price) ?> <?= Lang::get('bonus', 'custom_title') ?></td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <form action="bonus.php?action=purchase&label=<?= $Label ?>" method="post">
                            <input type="hidden" name="auth" value="<?= G::$LoggedUser['AuthKey'] ?>" />
                            <input type="hidden" name="confirm" value="true" />
                            <input type="text" style="width: 98%" id="title" name="title" placeholder="<?= Lang::get('bonus', 'custom_title') ?>" /> <br />
                            <input type="submit" onclick="ConfirmPurchase(event, '<?= $Item['Title'] ?>')" value="Submit" />&nbsp;<input type="button" onclick="PreviewTitle(<?= $BBCode ?>);" value="Preview" />
                            <div id="preview"></div>
                        </form>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<? View::show_footer();
