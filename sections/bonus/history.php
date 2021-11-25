<?php

View::show_header(Lang::get('bonus', 'bonus_points_purchase_history'), 'bonus');

if (check_perms('admin_bp_history') && isset($_GET['id']) && is_number($_GET['id'])) {
    $ID = (int)$_GET['id'];
    $Header = Lang::get('bonus', 'bonus_points_spending_history_for1') . Users::format_username($ID) . Lang::get('bonus', 'bonus_points_spending_history_for2');
    $WhoSpent = Users::format_username($ID) . ' has spent';
} else {
    $ID = G::$LoggedUser['ID'];
    $Header = Lang::get('bonus', 'bonus_points_spending_history');
    $WhoSpent = Lang::get('bonus', 'you_have_spent');
}

$Summary = $Bonus->getUserSummary($ID);

$Page  = max(1, isset($_GET['page']) ? intval($_GET['page']) : 1);
$Pages = Format::get_pages($Page, $Summary['nr'], TORRENTS_PER_PAGE);

if ($Summary['nr'] > 0) {
    $History = $Bonus->getUserHistory($ID, $Page, TORRENTS_PER_PAGE);
}

?>
<div class="header">
    <h2><?= $Header ?></h2>
</div>
<div class="linkbox">
    <a href="wiki.php?action=article&id=47" class="brackets"><?= Lang::get('bonus', 'about_bonus_points') ?></a>
    <a href="bonus.php" class="brackets"><?= Lang::get('bonus', 'bonus_points_shop') ?></a>
    <a href="bonus.php?action=bprates<?= check_perms('admin_bp_history') && $ID != G::$LoggedUser['ID'] ? "&userid=$ID" : '' ?>" class="brackets"><?= Lang::get('bonus', 'bonus_point_rates') ?></a>
</div>

<div class="thin">
    <? if ($Summary['total']) { ?>
        <h3><?= $WhoSpent ?> <?= number_format($Summary['total']) ?> <?= Lang::get('bonus', 'bonus_points_to_purchase') ?> <?= $Summary['nr'] ?> <?= $Summary['nr'] == 1 ? Lang::get('bonus', 'item') : Lang::get('bonus', 'items') ?><?= Lang::get('bonus', 'period') ?></h3>
    <? } else { ?>
        <h3><?= Lang::get('bonus', 'no_purchase_history') ?></h3>
    <?
    }
    if (isset($History)) {
    ?>
        <div class="linkbox">
            <?= $Pages ?>
        </div>
        <table id="bonus_purchase_history">
            <thead>
                <tr class="colhead">
                    <td><?= Lang::get('bonus', 'th_item') ?></td>
                    <td width="50px" align="right"><?= Lang::get('bonus', 'th_price') ?></td>
                    <td width="150px"><?= Lang::get('bonus', 'th_purchase_date') ?></td>
                    <td><?= Lang::get('bonus', 'th_for') ?></td>
                </tr>
            </thead>
            <tbody>
                <? foreach ($History as $Item) { ?>
                    <tr>
                        <td><?= Lang::get('bonus', $Item['Label']) ?></td>
                        <td align="right"><?= number_format($Item['Price']) ?></td>
                        <td><?= time_diff($Item['PurchaseDate']) ?></td>
                        <td><?= !$Item['OtherUserID'] ? '&nbsp;' : Users::format_username($Item['OtherUserID']) ?></td>
                    </tr>
                <?  } ?>
            </tbody>
        </table>
    <? } ?>
    <div class="linkbox">
        <?= $Pages ?>
    </div>
</div>
<?

View::show_footer();
