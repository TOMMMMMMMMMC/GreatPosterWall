<?
/*
 * This is the outline page for auto reports. It calls the AJAX functions
 * that actually populate the page and shows the proper header and footer.
 * The important function is AddMore().
 */
if (!check_perms('admin_reports')) {
    error(403);
}

View::show_header(Lang::get('reportsv2', 'reports_v2'), 'reportsv2');
?>
<div class="header">
    <h2><?= Lang::get('reportsv2', 'new_reports_auto_assigned') ?></h2>
    <? include('header.php'); ?>
</div>
<div class="buttonbox pad center">
    <input type="button" onclick="AddMore();" value="Add more" /> <input type="text" name="repop_amount" id="repop_amount" size="2" value="10" />
    | <span class="tooltip" title="Changes whether to automatically replace resolved ones with new ones"><input type="checkbox" checked="checked" id="dynamic" /> <label for="dynamic"><?= Lang::get('reportsv2', 'dynamic') ?></label></span>
    | <span class="tooltip" title="Resolves *all* checked reports with their respective resolutions"><input type="button" onclick="MultiResolve();" value="Multi-resolve" /></span>
    | <span class="tooltip" title="Unclaim all of the reports currently displayed"><input type="button" onclick="GiveBack();" value="Unclaim all" /></span>
</div>
<div id="all_reports" style="margin-left: auto; margin-right: auto;">
</div>
<?
View::show_footer();
?>