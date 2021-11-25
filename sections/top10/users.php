<?
// error out on invalid requests (before caching)
if (isset($_GET['details'])) {
    if (in_array($_GET['details'], array('ul', 'dl', 'numul', 'uls', 'dls'))) {
        $Details = $_GET['details'];
    } else {
        error(404);
    }
} else {
    $Details = 'all';
}

View::show_header(Lang::get('top10', 'top_10_users'));
?>
<div class="thin">
    <div class="header">
        <h2><?= Lang::get('top10', 'top_10_users') ?></h2>
        <? Top10View::render_linkbox("users"); ?>

    </div>
    <?

    // defaults to 10 (duh)
    $Limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
    $Limit = in_array($Limit, array(10, 100, 250)) ? $Limit : 10;

    $BaseQuery = "
	SELECT
		u.ID,
		ui.JoinDate,
		u.Uploaded,
		u.Downloaded,
		ABS(u.Uploaded-" . STARTING_UPLOAD . ") / (" . time() . " - UNIX_TIMESTAMP(ui.JoinDate)) AS UpSpeed,
		u.Downloaded / (" . time() . " - UNIX_TIMESTAMP(ui.JoinDate)) AS DownSpeed,
		COUNT(t.ID) AS NumUploads
	FROM users_main AS u
		JOIN users_info AS ui ON ui.UserID = u.ID
		LEFT JOIN torrents AS t ON t.UserID=u.ID
	WHERE u.Enabled='1'
		And Uploaded>" . STARTING_UPLOAD . "
		AND (Uploaded>'" . 10 * 1024 * 1024 * 1024 . "'
		or Downloaded>'" . 5 * 1024 * 1024 * 1024 . "')
		AND (Paranoia IS NULL OR (Paranoia NOT LIKE '%\"uploaded\"%' AND Paranoia NOT LIKE '%\"downloaded\"%'))
	GROUP BY u.ID";

    if ($Details == 'all' || $Details == 'ul') {
        if (!$TopUserUploads = $Cache->get_value('topuser_ul_' . $Limit)) {
            $DB->query("$BaseQuery ORDER BY u.Uploaded DESC LIMIT $Limit;");
            $TopUserUploads = $DB->to_array();
            $Cache->cache_value('topuser_ul_' . $Limit, $TopUserUploads, 3600 * 12);
        }
        generate_user_table(Lang::get('top10', 'uploaders'), 'ul', $TopUserUploads, $Limit);
    }

    if ($Details == 'all' || $Details == 'dl') {
        if (!$TopUserDownloads = $Cache->get_value('topuser_dl_' . $Limit)) {
            $DB->query("$BaseQuery ORDER BY u.Downloaded DESC LIMIT $Limit;");
            $TopUserDownloads = $DB->to_array();
            $Cache->cache_value('topuser_dl_' . $Limit, $TopUserDownloads, 3600 * 12);
        }
        generate_user_table(Lang::get('top10', 'downloaders'), 'dl', $TopUserDownloads, $Limit);
    }

    if ($Details == 'all' || $Details == 'numul') {
        if (!$TopUserNumUploads = $Cache->get_value('topuser_numul_' . $Limit)) {
            $DB->query("$BaseQuery ORDER BY NumUploads DESC LIMIT $Limit;");
            $TopUserNumUploads = $DB->to_array();
            $Cache->cache_value('topuser_numul_' . $Limit, $TopUserNumUploads, 3600 * 12);
        }
        generate_user_table(Lang::get('top10', 'torrents_uploaded'), 'numul', $TopUserNumUploads, $Limit);
    }

    if ($Details == 'all' || $Details == 'uls') {
        if (!$TopUserUploadSpeed = $Cache->get_value('topuser_ulspeed_' . $Limit)) {
            $DB->query("$BaseQuery ORDER BY UpSpeed DESC LIMIT $Limit;");
            $TopUserUploadSpeed = $DB->to_array();
            $Cache->cache_value('topuser_ulspeed_' . $Limit, $TopUserUploadSpeed, 3600 * 12);
        }
        generate_user_table(Lang::get('top10', 'fastest_uploaders'), 'uls', $TopUserUploadSpeed, $Limit);
    }

    if ($Details == 'all' || $Details == 'dls') {
        if (!$TopUserDownloadSpeed = $Cache->get_value('topuser_dlspeed_' . $Limit)) {
            $DB->query("$BaseQuery ORDER BY DownSpeed DESC LIMIT $Limit;");
            $TopUserDownloadSpeed = $DB->to_array();
            $Cache->cache_value('topuser_dlspeed_' . $Limit, $TopUserDownloadSpeed, 3600 * 12);
        }
        generate_user_table(Lang::get('top10', 'fastest_downloaders'), 'dls', $TopUserDownloadSpeed, $Limit);
    }



    echo '</div>';
    View::show_footer();
    exit;

    // generate a table based on data from most recent query to $DB
    function generate_user_table($Caption, $Tag, $Details, $Limit) {
        global $Time;
    ?>
        <h3><?= Lang::get('top10', 'top') ?> <?= $Limit . ' ' . $Caption; ?>
            <small class="top10_quantity_links">
                <?
                switch ($Limit) {
                    case 100: ?>
                        - <a href="top10.php?type=users&amp;details=<?= $Tag ?>" class="brackets"><?= Lang::get('top10', 'top') ?> 10</a>
                        - <span class="brackets"><?= Lang::get('top10', 'top') ?> 100</span>
                        - <a href="top10.php?type=users&amp;limit=250&amp;details=<?= $Tag ?>" class="brackets"><?= Lang::get('top10', 'top') ?> 250</a>
                    <? break;
                    case 250: ?>
                        - <a href="top10.php?type=users&amp;details=<?= $Tag ?>" class="brackets"><?= Lang::get('top10', 'top') ?> 10</a>
                        - <a href="top10.php?type=users&amp;limit=100&amp;details=<?= $Tag ?>" class="brackets"><?= Lang::get('top10', 'top') ?> 100</a>
                        - <span class="brackets"><?= Lang::get('top10', 'top') ?> 250</span>
                    <? break;
                    default: ?>
                        - <span class="brackets"><?= Lang::get('top10', 'top') ?> 10</span>
                        - <a href="top10.php?type=users&amp;limit=100&amp;details=<?= $Tag ?>" class="brackets"><?= Lang::get('top10', 'top') ?> 100</a>
                        - <a href="top10.php?type=users&amp;limit=250&amp;details=<?= $Tag ?>" class="brackets"><?= Lang::get('top10', 'top') ?> 250</a>
                <?  } ?>
            </small>
        </h3>
        <div class="table_container border">
            <table class="border top10_users_tables">
                <tr class="colhead">
                    <td class="center"><?= Lang::get('top10', 'rank') ?></td>
                    <td><?= Lang::get('top10', 'user') ?></td>
                    <td style="text-align: right;"><?= Lang::get('top10', 'uploaded') ?></td>
                    <td style="text-align: right;"><?= Lang::get('top10', 'ul_speed') ?></td>
                    <td style="text-align: right;"><?= Lang::get('top10', 'downloaded') ?></td>
                    <td style="text-align: right;"><?= Lang::get('top10', 'dl_speed') ?></td>
                    <td style="text-align: right;"><?= Lang::get('top10', 'uploads') ?></td>
                    <td style="text-align: right;"><?= Lang::get('top10', 'ratio') ?></td>
                    <td style="text-align: right;"><?= Lang::get('top10', 'joined') ?></td>
                </tr>
                <?
                // in the unlikely event that query finds 0 rows...
                if (empty($Details)) {
                    echo '
		<tr class="rowb">
			<td colspan="9" class="center">' . Lang::get('top10', 'found_no_users_matching_the_criteria') . '</td>
		</tr>
		</table><br />';
                    return;
                }
                $Rank = 0;
                foreach ($Details as $Detail) {
                    $Rank++;
                    $Highlight = ($Rank % 2 ? 'a' : 'b');
                ?>
                    <tr class="row<?= $Highlight ?>">
                        <td class="center"><?= $Rank ?></td>
                        <td><?= Users::format_username($Detail['ID'], false, false, false) ?></td>
                        <td class="number_column"><?= Format::get_size($Detail['Uploaded']) ?></td>
                        <td class="number_column tooltip" title="<?= Lang::get('top10', 'up_speed_is_base_2') ?>"><?= Format::get_size($Detail['UpSpeed']) ?>/s</td>
                        <td class="number_column"><?= Format::get_size($Detail['Downloaded']) ?></td>
                        <td class="number_column tooltip" title="<?= Lang::get('top10', 'down_speed_is_base_2') ?>"><?= Format::get_size($Detail['DownSpeed']) ?>/s</td>
                        <td class="number_column"><?= number_format($Detail['NumUploads']) ?></td>
                        <td class="number_column"><?= Format::get_ratio_html($Detail['Uploaded'], $Detail['Downloaded']) ?></td>
                        <td class="number_column"><?= time_diff($Detail['JoinDate']) ?></td>
                    </tr>
                <?  } ?>
            </table>
        </div>
    <?
    }
    ?>