<?
if (!isset($_GET['id']) || !is_number($_GET['id'])) {
    error(404);
}
$ArticleID = (int)$_GET['id'];

$Article = Wiki::get_article($ArticleID);
list($Revision, $Title, $Body, $Read, $Edit, $Date, $Author,,,, $Lang, $FatherID) = array_shift($Article);
if ($Edit > $LoggedUser['EffectiveClass']) {
    error('You do not have access to edit this article.');
}

View::show_header('Edit ' . $Title);
?>
<div class="thin">
    <div class="box pad">
        <form class="edit_form" name="wiki_article" action="wiki.php" method="post">
            <input type="hidden" name="action" value="edit" />
            <input type="hidden" name="auth" value="<?= $LoggedUser['AuthKey'] ?>" />
            <input type="hidden" name="id" value="<?= $ArticleID ?>" />
            <input type="hidden" name="revision" value="<?= $Revision ?>" />
            <div>
                <h3><?= Lang::get('wiki', 'title') ?></h3>
                <input type="text" name="title" size="92" maxlength="100" value="<?= $Title ?>" />
                <h3><?= Lang::get('wiki', 'body') ?></h3>
                <?
                $ReplyText = new TEXTAREA_PREVIEW('body', 'body', $Body, 91, 22, true, false);

                if (check_perms('admin_manage_wiki')) {
                ?>
                    <h3><?= Lang::get('wiki', 'article_access') ?></h3>
                    <p><?= Lang::get('wiki', 'article_access_detail') ?></p>
                    <strong><?= Lang::get('wiki', 'article_restrict_read') ?>:</strong> <select name="minclassread"><?= class_list($Read) ?></select>
                    <strong><?= Lang::get('wiki', 'article_restrict_edit') ?>:</strong> <select name="minclassedit"><?= class_list($Edit) ?></select>
                <?  } ?>
                <h3><?= Lang::get('wiki', 'article_language') ?></h3>
                <div id="wiki_create_language_box">
                    <p><?= Lang::get('wiki', 'article_language_detail') ?></p>
                    <div>
                        <span><?= Lang::get('wiki', 'article_language_select') ?>:</span>
                        <select name="language" id="language">
                            <option value="chs" <?= $Lang == "chs" ? " selected" : "" ?>><?= Lang::get('wiki', 'chinese') ?></option>
                            <option value="en" <?= $Lang == "en" ? " selected" : "" ?>><?= Lang::get('wiki', 'english') ?></option>
                        </select>
                    </div>
                    <div>
                        <span><?= Lang::get('wiki', 'article_chinese') ?>:</span>
                        <input type="text" size="92" name="fatherLink" id="fatherLink" <?= $FatherID ? "value=\"https://greatposterwall.com/wiki.php?action=article&id=$FatherID\"" : "" ?>>
                    </div>
                </div>
                <div style="text-align: center;">
                    <input type="button" value="Preview" class="hidden button_preview_<?= $ReplyText->getID() ?>" tabindex="1" />
                    <input type="submit" value="Submit" />
                </div>
            </div>
        </form>
    </div>
</div>
<? View::show_footer([], 'wiki/index'); ?>