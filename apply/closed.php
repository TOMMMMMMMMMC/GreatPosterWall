<?
View::show_header(Lang::get('pub', 'register_closed'));
?>
<div style="margin-top: 2.5rem;">
    <!-- <strong>Sorry, the site is currently invite only.</strong>
    <br> -->
    <strong><?= Lang::get('pub', 'register_invite_only') ?></strong>
</div>
<?
View::show_footer();
?>