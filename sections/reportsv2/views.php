<?
/*
 * This page is to outline all of the views built into reports v2.
 * It's used as the main page as it also lists the current reports by type
 * and the current in-progress reports by staff member.
 * All the different views are self explanatory by their names.
 */
if (!check_perms('admin_reports')) {
    error(403);
}

View::show_header(Lang::get('reportsv2', 'reports_v2'), 'reportsv2');


//Grab owner's ID, just for examples
$DB->query("
	SELECT ID, Username
	FROM users_main
	ORDER BY ID ASC
	LIMIT 1");
list($OwnerID, $Owner) = $DB->next_record();
$Owner = display_str($Owner);

?>
<div class="header">
    <h2><?= Lang::get('reportsv2', 'reports_v2_information') ?></h2>
    <? include('header.php'); ?>
</div>
<div class="thin float_clear">
    <div class="two_gigcolumns pad">
        <?
        $DB->query("
	SELECT
		um.ID,
		um.Username,
		COUNT(r.ID) AS Reports
	FROM reportsv2 AS r
		JOIN users_main AS um ON um.ID = r.ResolverID
	WHERE r.LastChangeTime > NOW() - INTERVAL 24 HOUR
	GROUP BY r.ResolverID
	ORDER BY Reports DESC");
        $Results = $DB->to_array();
        ?>
        <h3><?= Lang::get('reportsv2', 'time_d') ?></h3>
        <table class="box border">
            <tr class="colhead">
                <td class="colhead_dark"><?= Lang::get('reportsv2', 'username') ?></td>
                <td class="colhead_dark number_column"><?= Lang::get('reportsv2', 'reports') ?></td>
            </tr>
            <?
            foreach ($Results as $Result) {
                list($UserID, $Username, $Reports) = $Result;
                if ($Username == $LoggedUser['Username']) {
                    $RowClass = ' class="rowa"';
                } else {
                    $RowClass = '';
                }
            ?>
                <tr<?= $RowClass ?>>
                    <td><a href="reportsv2.php?view=resolver&amp;id=<?= $UserID ?>"><?= $Username ?></a></td>
                    <td class="number_column"><?= number_format($Reports) ?></td>
                    </tr>
                <?
            }
                ?>
        </table>
        <?
        $DB->query("
	SELECT
		um.ID,
		um.Username,
		COUNT(r.ID) AS Reports
	FROM reportsv2 AS r
		JOIN users_main AS um ON um.ID = r.ResolverID
	WHERE r.LastChangeTime > NOW() - INTERVAL 1 WEEK
	GROUP BY r.ResolverID
	ORDER BY Reports DESC");
        $Results = $DB->to_array();
        ?>
        <h3><?= Lang::get('reportsv2', 'time_w') ?></h3>
        <table class="box border">
            <tr class="colhead">
                <td class="colhead_dark"><?= Lang::get('reportsv2', 'username') ?></td>
                <td class="colhead_dark number_column"><?= Lang::get('reportsv2', 'reports') ?></td>
            </tr>
            <?
            foreach ($Results as $Result) {
                list($UserID, $Username, $Reports) = $Result;
                if ($Username == $LoggedUser['Username']) {
                    $RowClass = ' class="rowa"';
                } else {
                    $RowClass = '';
                }
            ?>
                <tr<?= $RowClass ?>>
                    <td><a href="reportsv2.php?view=resolver&amp;id=<?= $UserID ?>"><?= $Username ?></a></td>
                    <td class="number_column"><?= number_format($Reports) ?></td>
                    </tr>
                <?
            }
                ?>
        </table>
        <?
        $DB->query("
	SELECT
		um.ID,
		um.Username,
		COUNT(r.ID) AS Reports
	FROM reportsv2 AS r
		JOIN users_main AS um ON um.ID = r.ResolverID
	WHERE r.LastChangeTime > NOW() - INTERVAL 1 MONTH
	GROUP BY r.ResolverID
	ORDER BY Reports DESC");
        $Results = $DB->to_array();
        ?>
        <h3><?= Lang::get('reportsv2', 'time_m') ?></h3>
        <table class="box border">
            <tr class="colhead">
                <td class="colhead_dark"><?= Lang::get('reportsv2', 'username') ?></td>
                <td class="colhead_dark number_column"><?= Lang::get('reportsv2', 'reports') ?></td>
            </tr>
            <?
            foreach ($Results as $Result) {
                list($UserID, $Username, $Reports) = $Result;
                if ($Username == $LoggedUser['Username']) {
                    $RowClass = ' class="rowa"';
                } else {
                    $RowClass = '';
                }
            ?>
                <tr<?= $RowClass ?>>
                    <td><a href="reportsv2.php?view=resolver&amp;id=<?= $UserID ?>"><?= $Username ?></a></td>
                    <td class="number_column"><?= number_format($Reports) ?></td>
                    </tr>
                <?
            }
                ?>
        </table>
        <?
        $DB->query("
	SELECT
		um.ID,
		um.Username,
		COUNT(r.ID) AS Reports
	FROM reportsv2 AS r
		JOIN users_main AS um ON um.ID = r.ResolverID
	GROUP BY r.ResolverID
	ORDER BY Reports DESC");
        $Results = $DB->to_array();
        ?>
        <h3><?= Lang::get('reportsv2', 'time_a') ?></h3>
        <table class="box border">
            <tr class="colhead">
                <td class="colhead_dark"><?= Lang::get('reportsv2', 'username') ?></td>
                <td class="colhead_dark number_column"><?= Lang::get('reportsv2', 'reports') ?></td>
            </tr>
            <?
            foreach ($Results as $Result) {
                list($UserID, $Username, $Reports) = $Result;
                if ($Username == $LoggedUser['Username']) {
                    $RowClass = ' class="rowa"';
                } else {
                    $RowClass = '';
                }
            ?>
                <tr<?= $RowClass ?>>
                    <td><a href="reportsv2.php?view=resolver&amp;id=<?= $UserID ?>"><?= $Username ?></a></td>
                    <td class="number_column"><?= number_format($Reports) ?></td>
                    </tr>
                <?
            }
                ?>
        </table>
        <h3><?= Lang::get('reportsv2', 'different_view_modes_by_person') ?></h3>
        <div class="box pad">
            <strong><?= Lang::get('reportsv2', 'by_id_of_torrent_reported') ?>:</strong>
            <ul>
                <li>
                    <?= Lang::get('reportsv2', 'reports_of_torrents_with_id_1') ?>
                </li>
                <li>
                    <a href="reportsv2.php?view=torrent&amp;id=1"><?= site_url() ?>reportsv2.php?view=torrent&amp;id=1</a>
                </li>
            </ul>
            <strong><?= Lang::get('reportsv2', 'by_group_id_of_torrent_reported') ?>:</strong>
            <ul>
                <li>
                    <?= Lang::get('reportsv2', 'reports_of_torrents_within_the_group_with_id_1') ?>
                </li>
                <li>
                    <a href="reportsv2.php?view=group&amp;id=1"><?= site_url() ?>reportsv2.php?view=group&amp;id=1</a>
                </li>
            </ul>
            <strong><?= Lang::get('reportsv2', 'by_report_id') ?>:</strong>
            <ul>
                <li>
                    <?= Lang::get('reportsv2', 'the_report_with_id_1') ?>
                </li>
                <li>
                    <a href="reportsv2.php?view=report&amp;id=1"><?= site_url() ?>reportsv2.php?view=report&amp;id=1</a>
                </li>
            </ul>
            <strong><?= Lang::get('reportsv2', 'by_reporter_id') ?>:</strong>
            <ul>
                <li>
                    <?= Lang::get('reportsv2', 'reports_created_by_before') ?> <?= $Owner ?> <?= Lang::get('reportsv2', 'reports_created_by_after') ?>
                </li>
                <li>
                    <a href="reportsv2.php?view=reporter&amp;id=<?= $OwnerID ?>"><?= site_url() ?>reportsv2.php?view=reporter&amp;id=<?= $OwnerID ?></a>
                </li>
            </ul>
            <strong><?= Lang::get('reportsv2', 'by_uploader_id') ?>:</strong>
            <ul>
                <li>
                    <?= Lang::get('reportsv2', 'reports_for_torrents_uploaded_by_before') ?> <?= $Owner ?> <?= Lang::get('reportsv2', 'reports_for_torrents_uploaded_by_after') ?>
                </li>
                <li>
                    <a href="reportsv2.php?view=uploader&amp;id=<?= $OwnerID ?>"><?= site_url() ?>reportsv2.php?view=uploader&amp;id=<?= $OwnerID ?></a>
                </li>
            </ul>
            <strong><?= Lang::get('reportsv2', 'by_resolver_id') ?>:</strong>
            <ul>
                <li>
                    <?= Lang::get('reportsv2', 'reports_for_torrents_resolved_by_before') ?> <?= $Owner ?> <?= Lang::get('reportsv2', 'reports_for_torrents_resolved_by_after') ?>
                </li>
                <li>
                    <a href="reportsv2.php?view=resolver&amp;id=<?= $OwnerID ?>"><?= site_url() ?>reportsv2.php?view=resolver&amp;id=<?= $OwnerID ?></a>
                </li>
            </ul>
            <strong><?= Lang::get('reportsv2', 'for_browsing_anything_more_complicated_than_these') ?></strong>
        </div>
    </div>
    <div class="two_columns pad">
        <?
        $DB->query("
		SELECT
			r.ResolverID,
			um.Username,
			COUNT(r.ID) AS Count
		FROM reportsv2 AS r
			LEFT JOIN users_main AS um ON r.ResolverID = um.ID
		WHERE r.Status = 'InProgress'
		GROUP BY r.ResolverID");

        $Staff = $DB->to_array();
        ?>
        <h3><?= Lang::get('reportsv2', 'currently_assigned_reports_by_staff_member') ?></h3>
        <table class="box border">
            <tr class="colhead">
                <td class="colhead_dark"><?= Lang::get('reportsv2', 'staff_member') ?></td>
                <td class="colhead_dark number_column"><?= Lang::get('reportsv2', 'current_count') ?></td>
            </tr>
            <?
            foreach ($Staff as $Array) {
                if ($Array['Username'] == $LoggedUser['Username']) {
                    $RowClass = ' class="rowa"';
                } else {
                    $RowClass = '';
                }
            ?>
                <tr<?= $RowClass ?>>
                    <td>
                        <a href="reportsv2.php?view=staff&amp;id=<?= $Array['ResolverID'] ?>"><?= display_str($Array['Username']) ?><?= Lang::get('reportsv2', 's_reports') ?></a>
                    </td>
                    <td class="number_column"><?= number_format($Array['Count']) ?></td>
                    </tr>
                <?  } ?>
        </table>
        <h3><?= Lang::get('reportsv2', 'different_view_modes_by_report_type') ?></h3>
        <?
        $DB->query("
		SELECT
			Type,
			COUNT(ID) AS Count
		FROM reportsv2
		WHERE Status = 'New'
		GROUP BY Type");
        $Current = $DB->to_array();
        if (!empty($Current)) {
        ?>
            <table class="box border">
                <tr class="colhead">
                    <td class="colhead_dark"><?= Lang::get('reportsv2', 'type') ?></td>
                    <td class="colhead_dark number_column"><?= Lang::get('reportsv2', 'current_count') ?></td>
                </tr>
                <?
                foreach ($Current as $Array) {
                    //Ugliness
                    foreach ($Types as $Category) {
                        if (!empty($Category[$Array['Type']])) {
                            $Title = $Category[$Array['Type']]['title'];
                            break;
                        }
                    }
                ?>
                    <tr<?= $Title === 'Urgent' ? ' class="rowa" style="font-weight: bold;"' : '' ?>>
                        <td>
                            <a href="reportsv2.php?view=type&amp;id=<?= display_str($Array['Type']) ?>"><?= display_str($Title) ?></a>
                        </td>
                        <td class="number_column">
                            <?= number_format($Array['Count']) ?>
                        </td>
                        </tr>
                <?
                }
            }
                ?>
            </table>
    </div>
</div>
<?
View::show_footer();
?>