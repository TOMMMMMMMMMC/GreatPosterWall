<?
Text::$TOC = true;

$ArticleID = false;
if (!empty($_GET['id']) && is_number($_GET['id'])) { //Visiting article via ID
    $ArticleID = (int)$_GET['id'];
} elseif ($_GET['name'] != '') { //Retrieve article ID via alias.
    $ArticleID = Wiki::alias_to_id($_GET['name']);
} else { //No ID, No Name
    //error(404);
    error('Unknown article [' . display_str($_GET['id']) . ']');
}

if (!$ArticleID) { //No article found
    View::show_header(Lang::get('wiki', 'no_article_found'));
?>
    <div class="thin">
        <div class="header">
            <h2><?= Lang::get('wiki', 'no_article_found') ?></h2>
        </div>
        <div class="box pad">
            <?= Lang::get('wiki', 'no_article_matching_the_name') ?>
            <ul>
                <li><a href="wiki.php?action=search&amp;search=<?= display_str($_GET['name']) ?>"><?= Lang::get('wiki', 'search') ?></a><?= Lang::get('wiki', 'for_an_similar_article') ?></li>
                <li><a href="wiki.php?action=create&amp;alias=<?= display_str(Wiki::normalize_alias($_GET['name'])) ?>"><?= Lang::get('wiki', 'create') ?></a><?= Lang::get('wiki', 'replace_article') ?></li>
            </ul>
        </div>
    </div>
<?
    View::show_footer();
    die();
}

$Article = Wiki::get_article($ArticleID);
list($Revision, $Title, $Body, $Read, $Edit, $Date, $AuthorID, $AuthorName, $Aliases, $UserIDs,, $FatherID) = array_shift($Article);
if ($Read > $LoggedUser['EffectiveClass']) {
    error(Lang::get('wiki', 'you_must_be_a_higher_user_class_to_view'));
}

$TextBody = Text::full_format($Body, false);
$TOC = Text::parse_toc(0);

View::show_header($Title, 'wiki,bbcode');
?>
<div class="thin">
    <div class="header">
        <h2><?= $Title ?></h2>
        <div class="linkbox">
            <a href="wiki.php?action=create" class="brackets"><?= Lang::get('wiki', 'article_create') ?></a>
            <? if ($Edit <= $LoggedUser['EffectiveClass']) { ?>
                <a href="wiki.php?action=edit&amp;id=<?= $ArticleID ?>" class="brackets"><?= Lang::get('wiki', 'article_edit') ?></a>
                <a href="wiki.php?action=revisions&amp;id=<?= $ArticleID ?>" class="brackets"><?= Lang::get('wiki', 'article_history') ?></a>
                <? if (check_perms('admin_manage_wiki') && $_GET['id'] != INDEX_ARTICLE) { ?>
                    <a href="wiki.php?action=delete&amp;id=<?= $ArticleID ?>&amp;auth=<?= $LoggedUser['AuthKey'] ?>" class="brackets" onclick="return confirm('<?= Lang::get('wiki', 'article_delete_confirm1') ?>\n<?= Lang::get('wiki', 'article_delete_confirm2') ?>\n<?= Lang::get('wiki', 'article_delete_confirm3') ?>')"><?= Lang::get('wiki', 'article_delete') ?></a>
                <? } ?>
            <? } ?>
            <!--<a href="reports.php?action=submit&amp;type=wiki&amp;article=<?= $ArticleID ?>" class="brackets">Report</a>-->
        </div>
    </div>
    <div class="grid_container">
        <div class="sidebar">
            <div class="box">
                <div class="head"><?= Lang::get('wiki', 'search') ?></div>
                <div class="pad">
                    <form class="search_form" name="articles" action="wiki.php" method="get">
                        <input type="hidden" name="action" value="search" />
                        <input type="search" placeholder="<?= Lang::get('wiki', 'search_articles') ?>" name="search" size="20" />
                        <input value="Search" type="submit" class="hidden" />
                    </form>
                    <br style="line-height: 10px;" />
                    <a href="wiki.php?action=browse" class="brackets"><?= Lang::get('wiki', 'all_articles') ?></a>
                </div>
            </div>
            <div class="box" id="other_language_box">
                <div class="head"><?= Lang::get('wiki', 'other_languages') ?></div>
                <div class="body">
                    <?
                    if ($FatherID) {
                        $DB->query("select ID,Title,Lang from wiki_articles where id=$FatherID or father=$FatherID and id != $ArticleID");
                    } else {
                        $DB->query("select ID,Title,Lang from wiki_articles where Father=$ArticleID");
                    }
                    $Atcs = $DB->to_array();
                    echo "<ul>";
                    foreach ($Atcs as $Atc) {
                        echo "<li><a href=\"https://greatposterwall.com/wiki.php?action=article&id=$Atc[0]\">" . ($Atc[2] == 'chs' ? '简体中文' : 'English') . "</a></li>";
                    }
                    echo "</ul>";
                    ?>
                </div>
            </div>
            <div class="box">
                <div class="head"><?= Lang::get('wiki', 'toc') ?></div>
                <div class="body">
                    <?= $TOC ?>
                </div>
            </div>


            <div class="box box_info pad">
                <ul>
                    <li>
                        <strong><?= Lang::get('wiki', 'permissions') ?></strong>
                        <ul>
                            <li><?= Lang::get('wiki', 'read') ?>: <?= $ClassLevels[$Read]['Name'] ?></li>
                            <li><?= Lang::get('global', 'edit') ?>: <?= $ClassLevels[$Edit]['Name'] ?></li>
                        </ul>
                    </li>
                    <li>
                        <strong><?= Lang::get('wiki', 'details') ?></strong>
                        <ul>
                            <li><?= Lang::get('wiki', 'version') ?>: <?= $Revision ?></li>
                            <li><?= Lang::get('wiki', 'last_edited') ?>: <?= Users::format_username($AuthorID, false, false, false) ?></li>
                            <li><?= Lang::get('wiki', 'last_upload') ?>: <?= time_diff($Date) ?></li>
                        </ul>
                    </li>
                    <li>
                        <strong><?= Lang::get('wiki', 'aliases') ?></strong>
                        <ul>
                            <?
                            if ($Aliases != $Title) {
                                $AliasArray = explode(',', $Aliases);
                                $UserArray = explode(',', $UserIDs);
                                $i = 0;
                                foreach ($AliasArray as $AliasItem) {
                            ?>
                                    <li id="alias_<?= $AliasItem ?>"><a href="wiki.php?action=article&amp;name=<?= $AliasItem ?>"><?= Format::cut_string($AliasItem, 80, 1) ?></a><? if (check_perms('admin_manage_wiki')) { ?> <a href="#" onclick="Remove_Alias('<?= $AliasItem ?>'); return false;" class="brackets tooltip" title="<?= Lang::get('wiki', 'delete_aliases') ?>">X</a> <a href="user.php?id=<?= $UserArray[$i] ?>" class="brackets tooltip" title="<?= Lang::get('wiki', 'view_user') ?>">U</a><? } ?></li>
                            <? $i++;
                                }
                            }
                            ?>
                        </ul>
                    </li>
                </ul>
            </div>
            <? if ($Edit <= $LoggedUser['EffectiveClass']) { ?>
                <div class="box box_addalias">
                    <div style="padding: 5px;">
                        <form class="add_form" name="aliases" action="wiki.php" method="post">
                            <input type="hidden" name="action" value="add_alias" />
                            <input type="hidden" name="auth" value="<?= $LoggedUser['AuthKey'] ?>" />
                            <input type="hidden" name="article" value="<?= $ArticleID ?>" />
                            <input onfocus="if (this.value == 'Add alias') this.value='';" onblur="if (this.value == '') this.value='Add alias';" value="Add alias" type="text" name="alias" size="20" />
                            <input type="submit" value="+" />
                        </form>
                    </div>
                </div>
            <? } ?>
        </div>
        <div class="main_column">
            <div class="box wiki_article">
                <div class="pad"><?= $TextBody ?></div>
            </div>
        </div>
    </div>
</div>
<? View::show_footer([], 'wiki/index'); ?>