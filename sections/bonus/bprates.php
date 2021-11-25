<?php

$Page = !empty($_GET['page']) ? intval($_GET['page']) : 1;
$Page = max(1, $Page);
$Limit = TORRENTS_PER_PAGE;
$Offset = TORRENTS_PER_PAGE * ($Page - 1);

if (!empty($_GET['order_way']) && $_GET['order_way'] == 'desc') {
    $OrderWay = 'desc';
} else {
    $OrderWay = 'asc';
}

$OrderBys = array("size", "seeders", "seedtime", "hourlypoints");
$OrderBy = '';
if (!empty($_GET['order_by']) && in_array($_GET['order_by'], $OrderBys)) {
    $OrderBy = " order by " . $_GET['order_by'] . " $OrderWay ";
}

if (!empty($_GET['userid']) && check_perms('users_mod')) {
    $UserID = intval($_GET['userid']);
    $User = array_merge(Users::user_stats($_GET['userid']), Users::user_info($_GET['userid']), Users::user_heavy_info($_GET['userid']));
    if (empty($User)) {
        error(404);
    }
} else {
    $UserID = $LoggedUser['ID'];
    $User = $LoggedUser;
}

$Title = ($UserID === $LoggedUser['ID']) ? Lang::get('bonus', 'your_bonus_point_rate') : "{$User['Username']}" . Lang::get('bonus', 's_bonus_point_rate');
View::show_header($Title);

$DB->prepared_query("
SELECT
	COUNT(xfu.uid) as TotalTorrents,
	SUM(t.Size) as TotalSize,
	SUM(IFNULL(t.Size / (1024 * 1024 * 1024) * 1 * (
		0.025 + (
			(0.06 * LN(1 + (xfh.seedtime / (24)))) / (POW(GREATEST(t.Seeders, 1), 0.6))
		)
	), 0)) AS TotalHourlyPoints
FROM
	(SELECT DISTINCT uid,fid FROM xbt_files_users WHERE active=1 AND remaining=0 AND mtime > unix_timestamp(NOW() - INTERVAL 1 HOUR) AND uid = ?) AS xfu
	JOIN xbt_files_history AS xfh ON xfh.uid = xfu.uid AND xfh.fid = xfu.fid
	JOIN torrents AS t ON t.ID = xfu.fid
WHERE
	xfu.uid = ?", $UserID, $UserID);


list($TotalTorrents, $TotalSize, $TotalHourlyPoints) = $DB->next_record();
$TotalTorrents = intval($TotalTorrents);
$TotalSize = floatval($TotalSize);
$TotalHourlyPoints = floatval($TotalHourlyPoints);
$TotalDailyPoints = $TotalHourlyPoints * 24;
$TotalWeeklyPoints = $TotalDailyPoints * 7;
// The mean number of days in a month in the Gregorian calendar,
// and then multiple that by 12
$TotalMonthlyPoints = $TotalDailyPoints * 30.436875;
$TotalYearlyPoints = $TotalDailyPoints * 365.2425;

$Pages = Format::get_pages($Page, $TotalTorrents, TORRENTS_PER_PAGE);

?>
<div class="header">
    <h2><?= $Title ?></h2>
    <h3><?= Lang::get('bonus', 'total_points') ?>: <?= number_format($User['BonusPoints']) ?></h3>
</div>
<div class="linkbox">
    <a href="wiki.php?action=article&id=47" class="brackets"><?= Lang::get('bonus', 'about_bonus_points') ?></a>
    <a href="bonus.php" class="brackets"><?= Lang::get('bonus', 'bonus_points_shop') ?></a>
    <a href="bonus.php?action=history" class="brackets"><?= Lang::get('bonus', 'history') ?></a>
</div>
<div class="linkbox">
    <?= $Pages ?>
</div>
<div class="table_container">
    <table id="bprates_overview">
        <thead>
            <tr class="colhead">
                <td><?= Lang::get('bonus', 'total_torrents') ?></td>
                <td><?= Lang::get('global', 'size') ?></td>
                <td><?= Lang::get('bonus', 'bp_hour') ?></td>
                <td><?= Lang::get('bonus', 'bp_day') ?></td>
                <td><?= Lang::get('bonus', 'bp_week') ?></td>
                <td><?= Lang::get('bonus', 'bp_month') ?></td>
                <td><?= Lang::get('bonus', 'bp_year') ?></td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?= $TotalTorrents ?></td>
                <td><?= Format::get_size($TotalSize) ?></td>
                <td><?= number_format($TotalHourlyPoints, 2) ?></td>
                <td><?= number_format($TotalDailyPoints, 2) ?></td>
                <td><?= number_format($TotalWeeklyPoints, 2) ?></td>
                <td><?= number_format($TotalMonthlyPoints, 2) ?></td>
                <td><?= number_format($TotalYearlyPoints, 2) ?></td>
            </tr>
        </tbody>
    </table>
</div>
<br />
<?
$LinkTail = "&order_way=" . ($OrderWay == "asc" ? "desc" : "asc") . ($Page != 1 ? "&page=$Page" : "");
?>
<div class="table_container border">
    <table id="bprates_details">
        <thead>
            <tr class="colhead">
                <td><?= Lang::get('global', 'torrent') ?></td>
                <td><a href="bonus.php?action=bprates&order_by=size<?= $LinkTail ?><?= $UserID == $LoggedUser['ID'] ? "" : "&userid=$UserID" ?>"><?= Lang::get('global', 'size') ?></a></td>
                <td><a href="bonus.php?action=bprates&order_by=seeders<?= $LinkTail ?><?= $UserID == $LoggedUser['ID'] ? "" : "&userid=$UserID" ?>"><?= Lang::get('global', 'seeders') ?></a></td>
                <td><a href="bonus.php?action=bprates&order_by=seedtime<?= $LinkTail ?><?= $UserID == $LoggedUser['ID'] ? "" : "&userid=$UserID" ?>"><?= Lang::get('bonus', 'seedtime') ?></a></td>
                <td><a href="bonus.php?action=bprates&order_by=hourlypoints<?= $LinkTail ?><?= $UserID == $LoggedUser['ID'] ? "" : "&userid=$UserID" ?>"><?= Lang::get('bonus', 'bp_hour') ?></a></td>
                <td><?= Lang::get('bonus', 'bp_day') ?></td>
                <td><?= Lang::get('bonus', 'bp_week') ?></td>
                <td><?= Lang::get('bonus', 'bp_month') ?></td>
                <td><?= Lang::get('bonus', 'bp_year') ?></td>
            </tr>
        </thead>
        <tbody>
            <?php

            if ($TotalTorrents > 0) {
                $DB->prepared_query("
	SELECT
		t.ID,
		t.GroupID,
		t.Size,
		t.Size / (1024 * 1024 * 1024) as CorrectSize,
		t.Codec,
		t.Source,
		t.Processing,
        t.Container,
        t.Resolution,
		t.HasLogDB,
		t.HasCue,
		t.LogScore,
		t.LogChecksum,
		t.Media,
		t.Scene,
		t.RemasterYear,
		t.RemasterTitle,
		GREATEST(t.Seeders, 1) AS Seeders,
		xfh.seedtime AS Seedtime,
		(t.Size / (1024 * 1024 * 1024) * 1 *(
			0.025 + (
				(0.06 * LN(1 + (xfh.seedtime / (24)))) / (POW(GREATEST(t.Seeders, 1), 0.6))
			)
		)) AS HourlyPoints
	FROM
		(SELECT DISTINCT uid,fid FROM xbt_files_users WHERE active=1 AND remaining=0 AND mtime > unix_timestamp(NOW() - INTERVAL 1 HOUR) AND uid = ?) AS xfu
		JOIN xbt_files_history AS xfh ON xfh.uid = xfu.uid AND xfh.fid = xfu.fid
		JOIN torrents AS t ON t.ID = xfu.fid
	WHERE
		xfu.uid = ?
	$OrderBy
	LIMIT ?
	OFFSET ?", $UserID, $UserID, $Limit, $Offset);

                $GroupIDs = $DB->collect('GroupID');
                $Groups = Torrents::get_groups($GroupIDs, true, true, false);
                while ($Torrent = $DB->next_record(MYSQLI_ASSOC)) {
                    $Size = intval($Torrent['Size']);
                    $CorrectSize = $Torrent['CorrectSize'];
                    $Seeders = intval($Torrent['Seeders']);
                    $HourlyPoints = floatval($Torrent['HourlyPoints']);
                    $DailyPoints = $HourlyPoints * 24;
                    $WeeklyPoints = $DailyPoints * 7;
                    $MonthlyPoints = $DailyPoints * 30.436875;
                    $YearlyPoints = $DailyPoints * 365.2425;

                    extract(Torrents::array_group($Groups[$Torrent['GroupID']]));

                    $Torrent['Name'] = $GroupName;
                    $Torrent['SubName'] = $GroupSubName;
                    $Torrent['Year'] = $GroupYear;


                    $TorrentTags = new Tags($TagList);
                    $Name = Torrents::torrent_group_name($Torrent);

                    $DisplayName = '<a href="torrents.php?id=' . $GroupID . '&amp;torrentid=' . $Torrent['ID'] . '" class="tooltip" title="' . Lang::get('global', 'view_torrent') . '" dir="ltr">' . $Name . '</a>';
            ?>
                    <tr>
                        <td><?= $DisplayName ?></td>
                        <td><?= Format::get_size($Torrent['Size']) ?></td>
                        <td><?= number_format($Seeders) ?></td>
                        <td><?= convert_hours($Torrent['Seedtime'], 2) ?></td>
                        <td><?= number_format($HourlyPoints, 2) ?></td>
                        <td><?= number_format($DailyPoints, 2) ?></td>
                        <td><?= number_format($WeeklyPoints, 2) ?></td>
                        <td><?= number_format($MonthlyPoints, 2) ?></td>
                        <td><?= number_format($YearlyPoints, 2) ?></td>
                    </tr>
                <?php
                }
            } else {
                ?>
                <tr>
                    <td colspan="10" style="text-align:center;"><?= Lang::get('bonus', 'no_torrent_seeded_currently') ?></td>
                </tr>
            <?php
            }
            ?>

        </tbody>
    </table>
</div>
<?php

View::show_footer();
?>