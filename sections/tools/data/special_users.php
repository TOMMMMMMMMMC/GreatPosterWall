<?
if (!check_perms('admin_manage_permissions')) {
    error(403);
}
View::show_header('Special Users List');
?>
<div class="thin">
    <?
    $DB->query("
	SELECT ID
	FROM users_main
	WHERE CustomPermissions != ''
		AND CustomPermissions != 'a:0:{}'");
    if ($DB->has_results()) {
    ?>
        <table width="100%">
            <tr class="colhead">
                <td><?= Lang::get('tools', 'user') ?></td>
                <td><?= Lang::get('tools', 'access') ?></td>
            </tr>
            <?
            while (list($UserID) = $DB->next_record()) {
            ?>
                <tr>
                    <td><?= Users::format_username($UserID, true, true, true, true) ?></td>
                    <td><a href="user.php?action=permissions&amp;userid=<?= $UserID ?>"><?= Lang::get('tools', 'manage') ?></a></td>
                </tr>
            <?  } ?>
        </table>
    <?
    } else { ?>
        <h2 align="center"><?= Lang::get('tools', 'no_special_users') ?></h2>
    <?
    } ?>
</div>
<? View::show_footer(); ?>