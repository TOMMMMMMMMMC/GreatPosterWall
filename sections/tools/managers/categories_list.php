<?php

if (!check_perms('admin_manage_forums')) {
    error(403);
}

View::show_header(Lang::get('tools', 'forum_category_management'));
$DB->prepared_query('
	SELECT ID, Name, Sort, IFNULL(f.Count, 0) as Count
	FROM forums_categories as fc
	LEFT JOIN (SELECT CategoryID, COUNT(*) AS Count FROM forums GROUP BY CategoryID) AS f ON f.CategoryID = fc.ID
	ORDER BY Sort');

?>
<div class="header">
    <script type="text/javacript">document.getElementByID('content').style.overflow = 'visible';</script>
    <h2><?= Lang::get('tools', 'forum_category_control_panel') ?></h2>
</div>
<table width="100%">
    <tr class="colhead">
        <td><?= Lang::get('tools', 'sort') ?></td>
        <td><?= Lang::get('tools', 'name') ?></td>
        <td><?= Lang::get('tools', 'forum_category_control_panel_forums') ?></td>
        <td><?= Lang::get('tools', 'operation') ?></td>
    </tr>
    <?
    $Row = 'b';
    while (list($ID, $Name, $Sort, $Count) = $DB->fetch_record()) {
        $Row = $Row === 'a' ? 'b' : 'a';
    ?>
        <tr class="row<?= $Row ?>">
            <form class="manage_form" name="forums" action="" method="post">
                <input type="hidden" name="id" value="<?= $ID ?>" />
                <input type="hidden" name="action" value="categories_alter" />
                <input type="hidden" name="auth" value="<?= $LoggedUser['AuthKey'] ?>" />
                <td>
                    <input type="text" size="3" name="sort" value="<?= $Sort ?>" />
                </td>
                <td>
                    <input type="text" size="100" name="name" value="<?= $Name ?>" />
                </td>
                <td>
                    <?= $Count ?>
                </td>
                <td>
                    <input type="submit" name="submit" value="Edit" />
                    <?php if ($Count === 0) { ?>
                        <input type="submit" name="submit" value="Delete" onclick="return confirm('Are you sure you want to delete this category? This is an irreversible action!')" />
                    <?php } ?>
                </td>

            </form>
        </tr>
    <?
    }
    ?>
    <tr class="colhead">
        <td colspan="8"><?= Lang::get('tools', 'create_category') ?></td>
    </tr>
    <tr class="rowa">
        <form class="create_form" name="forum" action="" method="post">
            <input type="hidden" name="action" value="categories_alter" />
            <input type="hidden" name="auth" value="<?= $LoggedUser['AuthKey'] ?>" />
            <td>
                <input type="text" size="3" name="sort" />
            </td>
            <td colspan="2">
                <input type="text" size="100" name="name" />
            </td>
            <td>
                <input type="submit" value="Create" />
            </td>
        </form>
    </tr>
</table>
<? View::show_footer(); ?>