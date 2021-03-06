<?
if (!check_perms('admin_dnu')) {
    error(403);
}
$Title = Lang::get('tools', 'manage_the_dnu_list');

View::show_header($Title, 'jquery-ui,dnu_list');
$DB->query("
	SELECT
		d.ID,
		d.Name,
		d.Comment,
		d.UserID,
		d.Time
	FROM do_not_upload AS d
		LEFT JOIN users_main AS um ON um.ID = d.UserID
	ORDER BY d.Sequence");
?>
<div class="header">
    <h2><?= ($Title) ?></h2>
    <div class="linkbox">
        <p><?= Lang::get('tools', 'drag_and_drop_table_rows_to_reorder') ?></p>
    </div>
</div>
<div class="table_container border">
    <table id="dnu">
        <tr class="colhead">
            <td colspan="4"><?= Lang::get('tools', 'add_an_entry_to_the_dnu_list') ?></td>
        </tr>
        <tr class="rowa">
            <form class="add_form" name="dnu" action="tools.php" method="post">
                <input type="hidden" name="action" value="dnu_alter" />
                <input type="hidden" name="auth" value="<?= $LoggedUser['AuthKey'] ?>" />
                <td>
                    <input type="text" name="name" size="40" />
                </td>
                <td colspan="2">
                    <input type="text" name="comment" size="60" />
                </td>
                <td>
                    <input type="submit" value="Create" />
                </td>
            </form>
        </tr>
        <tr class="colhead">
            <td><?= Lang::get('tools', 'name') ?></td>
            <td><?= Lang::get('tools', 'dnu_comment') ?></td>
            <td><?= Lang::get('tools', 'dnu_added') ?></td>
            <td><?= Lang::get('tools', 'operations') ?></td>
        </tr>
        <tbody>
            <? while (list($ID, $Name, $Comment, $UserID, $DNUTime) = $DB->next_record()) { ?>
                <tr id="item_<?= $ID ?>">
                    <form class="manage_form dnu" action="tools.php" method="post">
                        <td>
                            <input type="hidden" name="action" value="dnu_alter" />
                            <input type="hidden" name="auth" value="<?= $LoggedUser['AuthKey'] ?>" />
                            <input type="hidden" name="id" value="<?= $ID ?>" />
                            <input type="text" name="name" value="<?= display_str($Name) ?>" size="40" />
                        </td>
                        <td>
                            <input type="text" name="comment" value="<?= display_str($Comment) ?>" size="60" />
                        </td>
                        <td>
                            <?= Users::format_username($UserID, false, false, false) ?><br />
                            <? echo time_diff($DNUTime, 1) . "\n"; ?>
                        </td>
                        <td>
                            <input type="submit" name="submit" value="Edit" />
                            <input type="submit" name="submit" value="Delete" />
                        </td>
                    </form>
                </tr>
            <?  } ?>
        </tbody>
    </table>
</div>
<? View::show_footer(); ?>