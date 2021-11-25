<?
View::show_header(Lang::get('top10', 'top_10_donors'));
?>
<div class="thin">
    <div class="header">
        <h2><?= Lang::get('top10', 'top_donors') ?></h2>
        <? Top10View::render_linkbox("donors"); ?>
    </div>
    <?

    $Limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
    $Limit = in_array($Limit, array(10, 100, 250)) ? $Limit : 10;

    $IsMod = check_perms("users_mod");
    $DB->query("
	SELECT
		UserID, TotalRank, Rank, SpecialRank, DonationTime, Hidden
	FROM users_donor_ranks
	WHERE TotalRank > 0
	ORDER BY TotalRank DESC
	LIMIT $Limit");

    $Results = $DB->to_array();

    generate_user_table(Lang::get('top10', 'top_n_donors'), $Results, $Limit);


    echo '</div>';
    View::show_footer();

    // generate a table based on data from most recent query to $DB
    function generate_user_table($Caption, $Results, $Limit) {
        global $Time, $IsMod;
    ?>
        <h3><?= Lang::get('top10', 'top') ?> <?= "$Limit $Caption"; ?>
            <small class="top10_quantity_links">
                <?
                switch ($Limit) {
                    case 100: ?>
                        - <a href="top10.php?type=donors" class="brackets"><?= Lang::get('top10', 'top') ?> 10</a>
                        - <span class="brackets"><?= Lang::get('top10', 'top') ?> 100</span>
                        - <a href="top10.php?type=donors&amp;limit=250" class="brackets"><?= Lang::get('top10', 'top') ?> 250</a>
                    <? break;
                    case 250: ?>
                        - <a href="top10.php?type=donors" class="brackets"><?= Lang::get('top10', 'top') ?> 10</a>
                        - <a href="top10.php?type=donors&amp;limit=100" class="brackets"><?= Lang::get('top10', 'top') ?> 100</a>
                        - <span class="brackets"><?= Lang::get('top10', 'top') ?> 250</span>
                    <? break;
                    default: ?>
                        - <span class="brackets"><?= Lang::get('top10', 'top') ?> 10</span>
                        - <a href="top10.php?type=donors&amp;limit=100" class="brackets"><?= Lang::get('top10', 'top') ?> 100</a>
                        - <a href="top10.php?type=donors&amp;limit=250" class="brackets"><?= Lang::get('top10', 'top') ?> 250</a>
                <?  } ?>
            </small>
        </h3>
        <div class="table_container border">
            <table class="border top10_donors_tables">
                <tr class="colhead">
                    <td class="center"><?= Lang::get('top10', 'position') ?></td>
                    <td><?= Lang::get('top10', 'user') ?></td>
                    <td style="text-align: left;"><?= Lang::get('top10', 'total_donor_points') ?></td>
                    <td style="text-align: left;"><?= Lang::get('top10', 'current_donor_rank') ?></td>
                    <td style="text-align: left;"><?= Lang::get('top10', 'last_donated') ?></td>

                </tr>
                <?
                // in the unlikely event that query finds 0 rows...
                if (empty($Results)) {
                    echo '
		<tr class="rowb">
			<td colspan="9" class="center">' . Lang::get('top10', 'found_no_users_matching_the_criteria') . '</td>
		</tr>
		</table><br />';
                }
                $Position = 0;
                foreach ($Results as $Result) {
                    $Position++;
                    $Highlight = ($Position % 2 ? 'a' : 'b');
                ?>
                    <tr class="row<?= $Highlight ?>">
                        <td class="center"><?= $Position ?></td>
                        <td><?= $Result['Hidden'] && !$IsMod ? 'Hidden' : Users::format_username($Result['UserID'], false, false, false) ?></td>
                        <td style="text-align: left;"><?= check_perms('users_mod') || $Position < 51 ? $Result['TotalRank'] : 'Hidden'; ?></td>
                        <td style="text-align: left;"><?= $Result['Hidden'] && !$IsMod ? 'Hidden' : DonationsView::render_rank($Result['Rank'], $Result['SpecialRank']) ?></td>
                        <td style="text-align: left;"><?= $Result['Hidden'] && !$IsMod ? 'Hidden' : time_diff($Result['DonationTime']) ?></td>
                    </tr>
                <?  } ?>
            </table>
        </div>
    <?
    }
    ?>