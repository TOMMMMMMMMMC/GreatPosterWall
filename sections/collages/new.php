<?
View::show_header(Lang::get('collages', 'new_create'));

if (!check_perms('site_collages_renamepersonal')) {
    $ChangeJS = " onchange=\"if ( this.options[this.selectedIndex].value == '0') { $('#namebox').ghide(); $('#personal').gshow(); } else { $('#namebox').gshow(); $('#personal').ghide(); }\"";
}

if (!check_perms('site_collages_renamepersonal') && $Category === '0') {
    $NoName = true;
}
?>
<div class="thin">
    <?
    if (isset($Err)) { ?>
        <div class="save_message error"><?= $Err ?></div>
        <br />
    <?
    } ?>
    <form class="create_form" name="collage" action="collages.php" method="post">
        <input type="hidden" name="action" value="new_handle" />
        <input type="hidden" name="auth" value="<?= $LoggedUser['AuthKey'] ?>" />
        <table class="layout">
            <tr id="collagename">
                <td class="label"><strong><?= Lang::get('collages', 'new_name') ?>:</strong></td>
                <td>
                    <input type="text" <?= $NoName ? ' class="hidden"' : ''; ?> name="name" size="60" id="namebox" value="<?= display_str($Name) ?>" />
                    <span id="personal" <?= $NoName ? '' : ' class="hidden"'; ?> style="font-style: oblique;"><strong><?= $LoggedUser['Username'] ?><?= Lang::get('collages', 'user_s_personal_collage') ?></strong></span>
                </td>
            </tr>
            <tr>
                <td class="label"><strong><?= Lang::get('collages', 'new_category') ?>:</strong></td>
                <td>
                    <select name="category" <?= $ChangeJS ?>>
                        <?
                        array_shift($CollageCats);

                        foreach ($CollageCats as $CatID => $CatName) { ?>
                            <option value="<?= $CatID + 1 ?>" <?= (($CatID + 1 == $Category) ? ' selected="selected"' : '') ?>><?= Lang::get('collages', 'collagecats')[$CatID + 1] ?></option>
                        <?
                        }

                        $DB->query("
	SELECT COUNT(ID)
	FROM collages
	WHERE UserID = '$LoggedUser[ID]'
		AND CategoryID = '0'
		AND Deleted = '0'");
                        list($CollageCount) = $DB->next_record();
                        if (($CollageCount < $LoggedUser['Permissions']['MaxCollages']) && check_perms('site_collages_personal')) { ?>
                            <option value="0" <?= (($Category === '0') ? ' selected="selected"' : '') ?>><?= Lang::get('collages', 'collagecats')[0] ?></option>
                        <?
                        } ?>
                    </select>
                    <br />
                    <ul>
                        <?= Lang::get('collages', 'new_category_note') ?>
                        <?
                        if (($CollageCount < $LoggedUser['Permissions']['MaxCollages']) && check_perms('site_collages_personal')) { ?>
                            <?= Lang::get('collages', 'new_category_note2') ?>
                        <?  } ?>
                    </ul>
                </td>
            </tr>
            <tr>
                <td class="label"><?= Lang::get('collages', 'new_description') ?>:</td>
                <td>
                    <textarea name="description" id="description" cols="60" rows="10"><?= display_str($Description) ?></textarea>
                </td>
            </tr>
            <tr>
                <td class="label"><strong><?= Lang::get('collages', 'tags') ?></strong></td>
                <td>
                    <input type="text" id="tags" name="tags" size="60" value="<?= display_str($Tags) ?>" />
                </td>
            </tr>
            <tr>
                <td colspan="2" class="center">
                    <?= Lang::get('collages', 'new_note') ?>
                </td>
            </tr>
            <tr>
                <td colspan="2" class="center"><input type="submit" value="<?= Lang::get('collages', 'new_create') ?>" /></td>
            </tr>
        </table>
    </form>
</div>
<? View::show_footer(); ?>