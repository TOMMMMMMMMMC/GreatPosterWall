<?php

View::show_header('积分商城', 'bonus');

if (isset($_GET['complete'])) {
    $label = $_GET['complete'];
    $item = $Bonus->getItem($label);
?>
    <div class="alertbar blend">
        &quot;<?= Lang::get('bonus', $item['Label']) ?>&quot;&nbsp;<?= Lang::get('bonus', 'purchased') ?>
    </div>
<?
}

?>
<div class="header">
    <h2><?= Lang::get('bonus', 'bonus_points_shop') ?></h2>
</div>
<div class="linkbox">
    <a href="wiki.php?action=article&id=47" class="brackets"><?= Lang::get('bonus', 'about_bonus_points') ?></a>
    <a href="bonus.php?action=bprates" class="brackets"><?= Lang::get('bonus', 'bonus_point_rates') ?></a>
    <a href="bonus.php?action=history" class="brackets"><?= Lang::get('bonus', 'history') ?></a>
</div>

<div class="thin">
    <div class="table_container border">
        <table id="bonus_goods_list">
            <thead>
                <tr class="colhead">
                    <td width="30px">#</td>
                    <td><?= Lang::get('bonus', 'description') ?></td>
                    <td width="45px"><?= Lang::get('bonus', 'points_price') ?></td>
                    <td width="70px"><?= Lang::get('bonus', 'checkout') ?></td>
                </tr>
            </thead>
            <tbody>
                <?php

                $Cnt = 0;
                $Items = $Bonus->getList();

                foreach ($Items as $Label => $Item) {
                    /*
    if ($Item['MinClass'] >  G::$LoggedUser['EffectiveClass']) {
        continue;
    }
    */
                    $Cnt++;
                    $RowClass = ($Cnt % 2 === 0) ? 'rowb' : 'rowa';
                    $Price = $Bonus->getEffectivePrice($Label, G::$LoggedUser['EffectiveClass']);
                    $FormattedPrice = number_format($Price);
                ?>
                    <tr class="$RowClass">
                        <td><?= $Cnt ?></td>
                        <td><?= Lang::get('bonus', $Item['Label']) ?></td>
                        <td><?= $FormattedPrice ?></td>
                        <td>
                            <?
                            if ($Item['MinClass'] >  $LoggedUser['EffectiveClass']) {
                            ?>
                                <span style="font-style: italic"><?= Lang::get('bonus', 'need_higher_user_class') ?></span>
                                <?
                            } else {
                                if (G::$LoggedUser['BonusPoints'] >= $Price) {
                                    $NextFunction = preg_match('/^other-\d$/',          $Label) ? 'ConfirmOther' : 'null';
                                    $OnClick      = preg_match('/^title-bbcode-[yn]$/', $Label) ? "NoOp" : "ConfirmPurchase";
                                ?>
                                    <a id="bonusconfirm" href="bonus.php?action=purchase&label=<?= $Label ?>&auth=<?= $LoggedUser['AuthKey'] ?>" onclick="<?= $OnClick ?>(event, '<?= Lang::get('bonus', $Item['Label']) ?>', <?= $NextFunction ?>, this);"><?= Lang::get('bonus', 'purchase') ?></a>
                                <?
                                } else {
                                ?>
                                    <span style="font-style: italic"><?= Lang::get('bonus', 'too_expensive') ?></span>
                        <?
                                }
                            }

                            print <<<HTML
				</td>
	</tr>
HTML;
                        }
                        ?>
            </tbody>
        </table>
    </div>
</div>
<?php

View::show_footer();
