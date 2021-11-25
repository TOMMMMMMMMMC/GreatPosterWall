<?
//Include the header
View::show_header(Lang::get('rules', 'requests_title'));
?>
<div class="thin">
    <? include('jump.php'); ?>
    <div class="header">
        <h2 class="general"><?= Lang::get('rules', 'requests_title') ?></h2>
    </div>
    <div class="box pad rule_summary" style="padding: 10px 10px 10px 20px;">
        <ul>
            <?= Lang::get('rules', 'requests_summary') ?>
        </ul>
    </div>

</div>
<?
View::show_footer();
?>