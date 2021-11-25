<?php
if (!check_perms('users_view_ips')) {
    error(403);
}
View::show_header(Lang::get('tools', 'dupe_ips'));
define('USERS_PER_PAGE', 50);
define('IP_OVERLAPS', 5);
list($Page, $Limit) = Format::page_limit(USERS_PER_PAGE);


$RS = $DB->query("
		SELECT
			SQL_CALC_FOUND_ROWS
			m.ID,
			m.IP,
			m.Username,
			m.PermissionID,
			m.Enabled,
			i.Donor,
			i.Warned,
			i.JoinDate,
			(
				SELECT COUNT(DISTINCT h.UserID)
				FROM users_history_ips AS h
				WHERE h.IP = m.IP
			) AS Uses
		FROM users_main AS m
			LEFT JOIN users_info AS i ON i.UserID = m.ID
		WHERE
			(
				SELECT COUNT(DISTINCT h.UserID)
				FROM users_history_ips AS h
				WHERE h.IP = m.IP
			) >= " . IP_OVERLAPS . "
			AND m.Enabled = '1'
			AND m.IP != '127.0.0.1'
		ORDER BY Uses DESC
		LIMIT $Limit");
$DB->query('SELECT FOUND_ROWS()');
list($Results) = $DB->next_record();
$DB->set_query_id($RS);

if ($DB->has_results()) {
?>
    <div class="header">
        <h2><?= Lang::get('tools', 'dupe_ips') ?></h2>
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
            <td><?= Lang::get('tools', 'td_ip_address') ?></td>
            <td><?= Lang::get('tools', 'dupes') ?></td>
            <td><?= Lang::get('tools', 'registered') ?></td>
        </tr>
        <?
        $Row = 'b';
        while (list($UserID, $IP, $Username, $PermissionID, $Enabled, $Donor, $Warned, $Joined, $Uses) = $DB->next_record()) {
            $Row = $Row === 'b' ? 'a' : 'b';
        ?>
            <tr class="row<?= $Row ?>">
                <td><?= Users::format_username($UserID, true, true, true, true) ?></td>
                <td>
                    <span style="float: left;"><?= Tools::get_host_by_ajax($IP) . " ($IP)" ?></span><span style="float: right;"><a href="userhistory.php?action=ips&amp;userid=<?= $UserID ?>" title="<?= Lang::get('tools', 'history') ?>" class="brackets tooltip">H</a> <a href="user.php?action=search&amp;ip_history=on&amp;ip=<?= display_str($IP) ?>" title="<?= Lang::get('tools', 'search') ?>" class="brackets tooltip">S</a></span>
                </td>
                <td><?= display_str($Uses) ?></td>
                <td><?= time_diff($Joined) ?></td>
            </tr>
        <?  } ?>
    </table>
    <div class="linkbox">
        <? echo $Pages; ?>
    </div>
<?  } else { ?>
    <h2 align="center"><?= Lang::get('tools', 'there_are_no_users_with_more_than_n_ip_overlaps_before') ?> <?= IP_OVERLAPS ?> <?= Lang::get('tools', 'there_are_no_users_with_more_than_n_ip_overlaps_after') ?></h2>
<?
}
View::show_footer();
?>