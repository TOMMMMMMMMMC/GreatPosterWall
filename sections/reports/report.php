<?
include(SERVER_ROOT . '/sections/reports/array.php');

if (empty($_GET['type']) || empty($_GET['id']) || !is_number($_GET['id'])) {
    error(404);
}

if (!array_key_exists($_GET['type'], $Types)) {
    error(403);
}
$Short = $_GET['type'];
$Type = $Types[$Short];

$ID = $_GET['id'];

switch ($Short) {
    case 'user':
        $DB->query("
			SELECT Username
			FROM users_main
			WHERE ID = $ID");
        if (!$DB->has_results()) {
            error(404);
        }
        list($Username) = $DB->next_record();
        break;

    case 'request_update':
        $NoReason = true;
        $DB->query("
			SELECT Title, Description, TorrentID, CategoryID, Year
			FROM requests
			WHERE ID = $ID");
        if (!$DB->has_results()) {
            error(404);
        }
        list($Name, $Desc, $Filled, $CategoryID, $Year) = $DB->next_record();
        if ($Filled || ($CategoryID != 0 && ($Categories[$CategoryID - 1] != 'Music' || $Year != 0))) {
            error(403);
        }
        break;

    case 'request':
        $DB->query("
			SELECT Title, Description, TorrentID
			FROM requests
			WHERE ID = $ID");
        if (!$DB->has_results()) {
            error(404);
        }
        list($Name, $Desc, $Filled) = $DB->next_record();
        break;

    case 'collage':
        $DB->query("
			SELECT Name, Description
			FROM collages
			WHERE ID = $ID");
        if (!$DB->has_results()) {
            error(404);
        }
        list($Name, $Desc) = $DB->next_record();
        break;

    case 'thread':
        $DB->query("
			SELECT ft.Title, ft.ForumID, um.Username
			FROM forums_topics AS ft
				JOIN users_main AS um ON um.ID = ft.AuthorID
			WHERE ft.ID = $ID");
        if (!$DB->has_results()) {
            error(404);
        }
        list($Title, $ForumID, $Username) = $DB->next_record();
        $DB->query("
			SELECT MinClassRead
			FROM forums
			WHERE ID = $ForumID");
        list($MinClassRead) = $DB->next_record();
        if (
            !empty($LoggedUser['DisableForums'])
            || ($MinClassRead > $LoggedUser['EffectiveClass'] && (!isset($LoggedUser['CustomForums'][$ForumID]) || $LoggedUser['CustomForums'][$ForumID] == 0))
            || (isset($LoggedUser['CustomForums'][$ForumID]) && $LoggedUser['CustomForums'][$ForumID] == 0)
        ) {
            error(403);
        }
        break;

    case 'post':
        $DB->query("
			SELECT fp.Body, fp.TopicID, um.Username
			FROM forums_posts AS fp
				JOIN users_main AS um ON um.ID = fp.AuthorID
			WHERE fp.ID = $ID");
        if (!$DB->has_results()) {
            error(404);
        }
        list($Body, $TopicID, $Username) = $DB->next_record();
        $DB->query("
			SELECT ForumID
			FROM forums_topics
			WHERE ID = $TopicID");
        list($ForumID) = $DB->next_record();
        $DB->query("
			SELECT MinClassRead
			FROM forums
			WHERE ID = $ForumID");
        list($MinClassRead) = $DB->next_record();
        if (
            !empty($LoggedUser['DisableForums'])
            || ($MinClassRead > $LoggedUser['EffectiveClass'] && (!isset($LoggedUser['CustomForums'][$ForumID]) || $LoggedUser['CustomForums'][$ForumID] == 0))
            || (isset($LoggedUser['CustomForums'][$ForumID]) && $LoggedUser['CustomForums'][$ForumID] == 0)
        ) {
            error(403);
        }
        break;

    case 'comment':
        $DB->query("
			SELECT c.Body, um.Username
			FROM comments AS c
				JOIN users_main AS um ON um.ID = c.AuthorID
			WHERE c.ID = $ID");
        if (!$DB->has_results()) {
            error(404);
        }
        list($Body, $Username) = $DB->next_record();
        break;
}

View::show_header(Lang::get('reports', 'report_a') . $Type['title'], 'bbcode,jquery.validate,form_validate');
?>
<div class="thin">
    <div class="header">
        <h2><?= Lang::get('reports', 'h2_report') ?><?= $Type['title'] ?></h2>
    </div>
    <h3><?= Lang::get('reports', 'reporting_guidelines') ?></h3>
    <div class="box pad">
        <p><?= Lang::get('reports', 'reporting_guidelines_intro') ?></p>
        <ul>
            <? foreach ($Type['guidelines'] as $Guideline) { ?>
                <li><?= $Guideline ?></li>
            <?  } ?>
        </ul>
        <p><?= Lang::get('reports', 'reporting_guidelines_summary') ?></p>
    </div>
    <?

    switch ($Short) {
        case 'user':
    ?>
            <p><?= Lang::get('reports', 'reporting_the_user') ?> <strong><?= display_str($Username) ?></strong></p>
        <?
            break;
        case 'request_update':
        ?>
            <p><?= Lang::get('reports', 'reporting_the_request') ?>:</p>
            <table id="report_request_update_table" class="report_details_table">
                <tr class="colhead">
                    <td><?= Lang::get('reports', 'title') ?></td>
                    <td><?= Lang::get('reports', 'description') ?></td>
                    <td><?= Lang::get('reports', 'filled') ?>?</td>
                </tr>
                <tr>
                    <td><?= display_str($Name) ?></td>
                    <td><?= Text::full_format($Desc) ?></td>
                    <td><strong><?= ($Filled == 0 ? Lang::get('reports', 'no') : Lang::get('reports', 'yes')) ?></strong></td>
                </tr>
            </table>
            <br />

            <div class="box pad center">
                <p><strong><?= Lang::get('reports', 'way_to_increase_turnover_rate') ?></strong></p>
                <form class="create_form" id="report_form" name="report" action="" method="post">
                    <input type="hidden" name="action" value="takereport" />
                    <input type="hidden" name="auth" value="<?= $LoggedUser['AuthKey'] ?>" />
                    <input type="hidden" name="id" value="<?= $ID ?>" />
                    <input type="hidden" name="type" value="<?= $Short ?>" />
                    <table class="layout">
                        <tr>
                            <td class="label"><?= Lang::get('reports', 'year_required') ?></td>
                            <td>
                                <input type="text" size="4" name="year" class="required" />
                            </td>
                        </tr>
                        <tr>
                            <td class="label"><?= Lang::get('reports', 'release_type') ?></td>
                            <td>
                                <select id="releasetype" name="releasetype">
                                    <option value="0">---</option>
                                    <? foreach ($ReleaseTypes as $Key => $Val) { ?>
                                        <option value="<?= $Key ?>" <?= (!empty($ReleaseType) ? ($Key == $ReleaseType ? ' selected="selected"' : '') : '') ?>><?= $Val ?></option>
                                    <?      } ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td class="label"><?= Lang::get('reports', 'comment') ?></td>
                            <td>
                                <textarea rows="8" cols="80" name="comment" class="required"></textarea>
                            </td>
                        </tr>
                    </table>
                    <br />
                    <br />
                    <input type="submit" value="Submit report" />
                </form>
            </div>
        <?
            break;
        case 'request':
        ?>
            <p><?= Lang::get('reports', 'reporting_the_request') ?>:</p>
            <table id="report_request_table" class="report_details_table">
                <tr class="colhead">
                    <td><?= Lang::get('reports', 'title') ?></td>
                    <td><?= Lang::get('reports', 'description') ?></td>
                    <td><?= Lang::get('reports', 'filled') ?>?</td>
                </tr>
                <tr>
                    <td><?= display_str($Name) ?></td>
                    <td><?= Text::full_format($Desc) ?></td>
                    <td><strong><?= ($Filled == 0 ? Lang::get('reports', 'no') : Lang::get('reports', 'yes')) ?></strong></td>
                </tr>
            </table>
        <?
            break;
        case 'collage':
        ?>
            <p><?= Lang::get('reports', 'reporting_the_collage') ?>:</p>
            <table id="report_collage_table" class="report_details_table">
                <tr class="colhead">
                    <td><?= Lang::get('reports', 'title') ?></td>
                    <td><?= Lang::get('reports', 'description') ?></td>
                </tr>
                <tr>
                    <td><?= display_str($Name) ?></td>
                    <td><?= Text::full_format($Desc) ?></td>
                </tr>
            </table>
        <?
            break;
        case 'thread':
        ?>
            <p><?= Lang::get('reports', 'reporting_the_thread') ?>:</p>
            <table id="report_thread_table" class="report_details_table">
                <tr class="colhead">
                    <td><?= Lang::get('reports', 'username') ?></td>
                    <td><?= Lang::get('reports', 'title') ?></td>
                </tr>
                <tr>
                    <td><?= display_str($Username) ?></td>
                    <td><?= display_str($Title) ?></td>
                </tr>
            </table>
        <?
            break;
        case 'post':
        ?>
            <p><?= Lang::get('reports', 'reporting_the_post') ?>:</p>
            <div class="table_container border mgb" id="report_post_table_container">
                <table id="report_post_table" class="report_details_table">
                    <tr class="colhead">
                        <td><?= Lang::get('reports', 'username') ?></td>
                        <td><?= Lang::get('reports', 'body') ?></td>
                    </tr>
                    <tr>
                        <td><?= display_str($Username) ?></td>
                        <td><?= Text::full_format($Body) ?></td>
                    </tr>
                </table>
            </div>
        <?
            break;
        case 'comment':
        ?>
            <p><?= Lang::get('reports', 'reporting_the') ?><?= $Types[$Short]['title'] ?>:</p>
            <div class="table_container border mgb" id="report_comment_table_container">
                <table id="report_comment_table" class="report_details_table">
                    <tr class="colhead">
                        <td><?= Lang::get('reports', 'username') ?></td>
                        <td><?= Lang::get('reports', 'body') ?></td>
                    </tr>
                    <tr>
                        <td><?= display_str($Username) ?></td>
                        <td><?= Text::full_format($Body) ?></td>
                    </tr>
                </table>
            </div>
        <?
            break;
    }
    if (empty($NoReason)) {
        ?>
        <h3><?= Lang::get('reports', 'reason') ?></h3>
        <div class="box pad center">
            <form class="create_form" name="report" id="report_form" action="" method="post">
                <input type="hidden" name="action" value="takereport" />
                <input type="hidden" name="auth" value="<?= $LoggedUser['AuthKey'] ?>" />
                <input type="hidden" name="id" value="<?= $ID ?>" />
                <input type="hidden" name="type" value="<?= $Short ?>" />
                <textarea class="required" rows="10" cols="95" name="reason"></textarea><br /><br />
                <input type="submit" value="Submit report" />
            </form>
        </div>
    <?
    } /* close <div class="thin"> */ ?>
</div>
<?
View::show_footer();
?>