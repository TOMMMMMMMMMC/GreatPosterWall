<?
if (!isset($_GET['id']) || !is_number($_GET['id'])) {
    error(404);
}
$ArticleID = (int)$_GET['id'];

$Latest = Wiki::get_article($ArticleID);
list($Revision, $Title, $Body, $Read, $Edit, $Date, $AuthorID, $AuthorName) = array_shift($Latest);
if ($Read > $LoggedUser['EffectiveClass']) {
    error(404);
}
if ($Edit > $LoggedUser['EffectiveClass']) {
    error(403);
}

View::show_header("Revisions of " . $Title);
?>
<div class="thin">
    <div class="header">
        <h2><?= Lang::get('wiki', 'revision_history_before') ?><a href="wiki.php?action=article&amp;id=<?= $ArticleID ?>"><?= $Title ?></a><?= Lang::get('wiki', 'revision_history_after') ?></h2>
    </div>
    <form action="wiki.php" method="get">
        <input type="hidden" name="action" id="action" value="compare" />
        <input type="hidden" name="id" id="id" value="<?= $ArticleID ?>" />
        <div class="table_container border">
            <table id="wiki_revisions_table">
                <tr class="colhead">
                    <td><?= Lang::get('wiki', 'history_revision') ?></td>
                    <td><?= Lang::get('wiki', 'history_title') ?></td>
                    <td><?= Lang::get('wiki', 'history_author') ?></td>
                    <td><?= Lang::get('wiki', 'history_age') ?></td>
                    <td><?= Lang::get('wiki', 'history_old') ?></td>
                    <td><?= Lang::get('wiki', 'history_new') ?></td>
                </tr>
                <tr>
                    <td><?= $Revision ?></td>
                    <td><?= $Title ?></td>
                    <td><?= Users::format_username($AuthorID, false, false, false) ?></td>
                    <td><?= time_diff($Date) ?></td>
                    <td><input type="radio" name="old" value="<?= $Revision ?>" disabled="disabled" /></td>
                    <td><input type="radio" name="new" value="<?= $Revision ?>" checked="checked" /></td>
                </tr>
                <?
                $DB->query("
	SELECT
		Revision,
		Title,
		Author,
		Date
	FROM wiki_revisions
	WHERE ID = '$ArticleID'
	ORDER BY Revision DESC");
                while (list($Revision, $Title, $AuthorID, $Date) = $DB->next_record()) { ?>
                    <tr>
                        <td><?= $Revision ?></td>
                        <td><?= $Title ?></td>
                        <td><?= Users::format_username($AuthorID, false, false, false) ?></td>
                        <td><?= time_diff($Date) ?></td>
                        <td><input type="radio" name="old" value="<?= $Revision ?>" /></td>
                        <td><input type="radio" name="new" value="<?= $Revision ?>" /></td>
                    </tr>
                <? } ?>
            </table>
        </div>
        <div class="center">
            <input type="submit" value="Compare" />
        </div>
    </form>
</div>
<? View::show_footer(); ?>