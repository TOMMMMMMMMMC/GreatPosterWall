<?php
if (!(check_perms('users_mod') || check_perms('site_tag_aliases_read'))) {
    error(403);
}

View::show_header(Lang::get('tools', 'tag_aliases'));

$orderby = ($_GET['order'] === 'badtags' ? 'BadTag' : 'AliasTag');

if (check_perms('users_mod')) {
    if (isset($_POST['newalias'])) {
        $badtag = $_POST['badtag'];
        $aliastag = $_POST['aliastag'];

        $DB->prepared_query("
			INSERT INTO tag_aliases (BadTag, AliasTag)
			VALUES (?, ?)", $badtag, $aliastag);
    }

    if (isset($_POST['changealias']) && is_number($_POST['aliasid'])) {
        $aliasid = $_POST['aliasid'];
        $badtag = $_POST['badtag'];
        $aliastag = $_POST['aliastag'];

        if ($_POST['save']) {
            $DB->prepared_query("
				UPDATE tag_aliases
				SET BadTag = ?, AliasTag = ?
				WHERE ID = ?", $badtag, $aliastag, $aliasid);
        }
        if ($_POST['delete']) {
            $DB->prepared_query("
				DELETE FROM tag_aliases
				WHERE ID = ?", $aliasid);
        }
    }
}
?>
<div class="header">
    <h2><?= Lang::get('tools', 'tag_aliases') ?></h2>
    <div class="linkbox">
        <a href="tools.php?action=tag_aliases&amp;order=goodtags" class="brackets"><?= Lang::get('tools', 'sort_by_good_tags') ?></a>
        <a href="tools.php?action=tag_aliases&amp;order=badtags" class="brackets"><?= Lang::get('tools', 'sort_by_bad_tags') ?></a>
    </div>
</div>
<table class="thin" id="tag_aliases_table">
    <tr class="colhead">
        <td><?= Lang::get('tools', 'proper_tag') ?></td>
        <td><?= Lang::get('tools', 'renamed_from') ?></td>
        <? if (check_perms('users_mod')) { ?>
            <td><?= Lang::get('tools', 'operations') ?></td>
        <?  } ?>
    </tr>
    <!-- <tr /> -->
    <tr>
        <form class="add_form" name="aliases" method="post" action="">
            <input type="hidden" name="newalias" value="1" />
            <td>
                <input type="text" name="aliastag" />
            </td>
            <td>
                <input type="text" name="badtag" />
            </td>
            <? if (check_perms('users_mod')) { ?>
                <td>
                    <input type="submit" value="Add alias" />
                </td>
            <?  } ?>
        </form>
    </tr>
    <?
    $DB->prepared_query("
	SELECT ID, BadTag, AliasTag
	FROM tag_aliases
	ORDER BY $orderby");
    while (list($ID, $BadTag, $AliasTag) = $DB->next_record()) {
    ?>
        <tr>
            <form class="manage_form" name="aliases" method="post" action="">
                <input type="hidden" name="changealias" value="1" />
                <input type="hidden" name="aliasid" value="<?= $ID ?>" />
                <td>
                    <input type="text" name="aliastag" value="<?= $AliasTag ?>" />
                </td>
                <td>
                    <input type="text" name="badtag" value="<?= $BadTag ?>" />
                </td>
                <? if (check_perms('users_mod')) { ?>
                    <td>
                        <input type="submit" name="save" value="Save alias" />
                        <input type="submit" name="delete" value="Delete alias" />
                    </td>
                <?  } ?>
            </form>
        </tr>
    <?
    } ?>
</table>
<? View::show_footer(); ?>