<?
//Include the header
View::show_header(Lang::get('rules', 'chat_title'));
?>

<div class="thin">
    <? include('jump.php'); ?>
    <div class="header">
        <h2 id="general"><?= Lang::get('rules', 'chat_general') ?></h2>
    </div>
    <div class="box pad rule_summary" style="padding: 10px 10px 10px 20px;">
        <?= Lang::get('rules', 'chat_general_rules') ?>
    </div>
    <!-- <br /> -->
    <!-- Forum Rules -->
    <div class="header">
        <h2 id="forums"><?= Lang::get('rules', 'chat_forums') ?></h2>
    </div>
    <div class="box pad rule_summary" style="padding: 10px 10px 10px 20px;">
        <? Rules::display_forum_rules() ?>
    </div>
    <!-- END Forum Rules -->

    <!-- IRC Rules -->
    <div class="header">
        <h2 id="irc"><?= Lang::get('rules', 'chat_groups') ?></h2>
    </div>
    <div class="box pad rule_summary" style="padding: 10px 10px 10px 20px;">
        <? Rules::display_irc_chat_rules() ?>
    </div>
</div>
<?
View::show_footer();
?>