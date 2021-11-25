<?
//Include the header
View::show_header(Lang::get('rules', 'rules'));
?>
<!-- General Rules -->
<div class="thin">
    <? include('jump.php'); ?>
    <div class="header">
        <h2 id="general"><?= Lang::get('rules', 'golden_rules') ?></h2>
        <p><?= Lang::get('rules', 'golden_rules_used') ?></p>
    </div>
    <div class="box pad rule_summary" style="padding: 10px 10px 10px 20px;">
        <? Rules::display_golden_rules(); ?>
    </div>
    <!-- END General Rules -->

</div>
<?
View::show_footer();
?>