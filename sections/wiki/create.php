<?
View::show_header(Lang::get('wiki', 'create_an_article'));
?>
<div class="thin">
    <div class="box pad">
        <form class="create_form" name="wiki_article" action="wiki.php" method="post">
            <input type="hidden" name="action" value="create" />
            <input type="hidden" name="auth" value="<?= $LoggedUser['AuthKey'] ?>" />
            <div>
                <h3><?= Lang::get('wiki', 'title') ?></h3>
                <input type="text" name="title" size="92" maxlength="100" />
                <? /* if ($_GET['alias']) { ?>
                <input type="hidden" name="alias" value="<?=display_str(alias($_GET['alias']))?>" />
<? } else { ?>
                <h3>Alias</h3>
                <p>An exact search string or name that should lead to this article. (More can be added later)</p>
                <input type="text" name="alias" size="50" maxlength="50" />
<? } */ ?>
                <h3><?= Lang::get('wiki', 'body') ?></h3>
                <?
                $ReplyText = new TEXTAREA_PREVIEW('body', 'body', '', 91, 22, true, false);

                if (check_perms('admin_manage_wiki')) { ?>
                    <h3><?= Lang::get('wiki', 'article_access') ?></h3>
                    <p><?= Lang::get('wiki', 'article_access_detail') ?></p>
                    <strong><?= Lang::get('wiki', 'article_restrict_read') ?></strong> <select name="minclassread"><?= class_list() ?></select>
                    <strong><?= Lang::get('wiki', 'article_restrict_edit') ?></strong> <select name="minclassedit"><?= class_list() ?></select>
                <?  } ?>
                <h3><?= Lang::get('wiki', 'article_language') ?></h3>
                <div id="wiki_create_language_box">
                    <p><?= Lang::get('wiki', 'article_language_detail') ?></p>
                    <div>
                        <span><?= Lang::get('wiki', 'article_language_select') ?></span>
                        <select name="language" id="language">
                            <option value="chs" selected="selected"><?= Lang::get('wiki', 'chinese') ?></option>
                            <option value="en"><?= Lang::get('wiki', 'english') ?></option>
                        </select>
                    </div>
                    <div>
                        <span><?= Lang::get('wiki', 'article_chinese') ?></span>
                        <input type="text" size="92" name="fatherLink" id="fatherLink">
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