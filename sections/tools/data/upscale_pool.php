<?php
if (!check_perms('site_view_flow')) {
    error(403);
}
View::show_header(Lang::get('tools', 'h2_upscale_pool'));
define('USERS_PER_PAGE', 50);
list($Page, $Limit) = Format::page_limit(USERS_PER_PAGE);

$RS = $DB->query("
	SELECT
		SQL_CALC_FOUND_ROWS
		m.ID,
		m.Username,
		m.Uploaded,
		m.Downloaded,
		m.PermissionID,
		m.Enabled,
		i.Donor,
		i.Warned,
		i.JoinDate,
		i.RatioWatchEnds,
		i.RatioWatchDownload,
		m.RequiredRatio
	FROM users_main AS m
		LEFT JOIN users_info AS i ON i.UserID = m.ID
	WHERE i.RatioWatchEnds != '0000-00-00 00:00:00'
		AND m.Enabled = '1'
	ORDER BY i.RatioWatchEnds ASC
	LIMIT $Limit");
$DB->query('SELECT FOUND_ROWS()');
list($Results) = $DB->next_record();
$DB->query("
	SELECT COUNT(UserID)
	FROM users_info
	WHERE BanDate != '0000-00-00 00:00:00'
		AND BanReason = '2'");
list($TotalDisabled) = $DB->next_record();
$DB->set_query_id($RS);
?>
<div class="header">
    <h2><?= Lang::get('tools', 'h2_upscale_pool') ?></h2>
</div>
<?
if ($DB->has_results()) {
?>
    <div class="box pad thin" id="users_on_ratio_watch_number">
        <p><?= Lang::get('tools', 'there_are_currently_enabled_users_on_ratio_watch_1') ?> <?= number_format($Results) ?> <?= Lang::get('tools', 'there_are_currently_enabled_users_on_ratio_watch_2') ?> <?= number_format($TotalDisabled) ?> <?= Lang::get('tools', 'there_are_currently_enabled_users_on_ratio_watch_3') ?></p>
    </div>
    <div class="linkbox">
        <?
        $Pages = Format::get_pages($Page, $Results, USERS_PER_PAGE, 11);
        echo $Pages;
        ?>
    </div>
    <table width="100%">
        <tr class="colhead">
            <td><?= Lang::get('tools', 'user') ?></td>
            <td class="number_column"><?= Lang::get('tools', 'uploaded') ?></td>
            <td class="number_column"><?= Lang::get('tools', 'downloaded') ?></td>
            <td class="number_column"><?= Lang::get('tools', 'ratio') ?></td>
            <td class="number_column"><?= Lang::get('tools', 'required_ratio') ?></td>
            <td class="number_column tooltip" title="<?= Lang::get('tools', 'deficit_title') ?>"><?= Lang::get('tools', 'deficit') ?></td>
            <td class="number_column tooltip" title="<?= Lang::get('tools', 'gamble_title') ?>"><?= Lang::get('tools', 'gamble') ?></td>
            <td><?= Lang::get('tools', 'registration_date') ?></td>
            <td class="tooltip" title="<?= Lang::get('tools', 'ratio_watch_ended_ends_title') ?>"><?= Lang::get('tools', 'ratio_watch_ended_ends') ?></td>
            <td><?= Lang::get('tools', 'life_span') ?></td>
        </tr>
        <?
        while (list($UserID, $Username, $Uploaded, $Downloaded, $PermissionID, $Enabled, $Donor, $Warned, $Joined, $RatioWatchEnds, $RatioWatchDownload, $RequiredRatio) = $DB->next_record()) {
            $Row = $Row === 'b' ? 'a' : 'b';

        ?>
            <tr class="row<?= $Row ?>">
                <td><?= Users::format_username($UserID, true, true, true, true) ?></td>
                <td class="number_column"><?= Format::get_size($Uploaded) ?></td>
                <td class="number_column"><?= Format::get_size($Downloaded) ?></td>
                <td class="number_column"><?= Format::get_ratio_html($Uploaded, $Downloaded) ?></td>
                <td class="number_column"><?= number_format($RequiredRatio, 2) ?></td>
                <td class="number_column"><? if (($Downloaded * $RequiredRatio) > $Uploaded) {
                                                echo Format::get_size(($Downloaded * $RequiredRatio) - $Uploaded);
                                            } ?></td>
                <td class="number_column"><?= Format::get_size($Downloaded - $RatioWatchDownload) ?></td>
                <td><?= time_diff($Joined, 2) ?></td>
                <td><?= time_diff($RatioWatchEnds) ?></td>
                <td><?/*time_diff(strtotime($Joined), strtotime($RatioWatchEnds))*/ ?></td>
            </tr>
        <?  } ?>
    </table>
    <div class="linkbox">
        <? echo $Pages; ?>
    </div>
<?
} else { ?>
    <h2 align="center"><?= Lang::get('tools', 'there_are_currently_no_users_on_ratio_watch') ?></h2>
<?
}

View::show_footer();
?>