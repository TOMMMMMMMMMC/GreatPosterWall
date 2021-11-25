<?
include(SERVER_ROOT . '/classes/torrenttable.class.php');
$Where = array();
if (!empty($_GET['advanced']) && check_perms('site_advanced_top10')) {
    $Details = 'all';
    $Limit = 10;

    if ($_GET['tags']) {
        $TagWhere = array();
        $Tags = explode(',', str_replace('.', '_', trim($_GET['tags'])));
        foreach ($Tags as $Tag) {
            $Tag = preg_replace('/[^a-z0-9_]/', '', $Tag);
            if ($Tag != '') {
                $TagWhere[] = "g.TagList REGEXP '[[:<:]]" . db_string($Tag) . "[[:>:]]'";
            }
        }
        if (!empty($TagWhere)) {
            if ($_GET['anyall'] == 'any') {
                $Where[] = '(' . implode(' OR ', $TagWhere) . ')';
            } else {
                $Where[] = '(' . implode(' AND ', $TagWhere) . ')';
            }
        }
    }

    if ($_GET['format']) {
        if (in_array($_GET['format'], $Formats)) {
            $Where[] = "t.Format='" . db_string($_GET['format']) . "'";
        }
    }
} else {
    // error out on invalid requests (before caching)
    if (isset($_GET['details'])) {
        if (in_array($_GET['details'], array('day', 'week', 'overall', 'snatched', 'data', 'seeded', 'month', 'year'))) {
            $Details = $_GET['details'];
        } else {
            error(404);
        }
    } else {
        $Details = 'all';
    }

    // defaults to 10 (duh)
    $Limit = (isset($_GET['limit']) ? intval($_GET['limit']) : 10);
    $Limit = (in_array($Limit, array(10, 100, 250)) ? $Limit : 10);
}
$Filtered = !empty($Where);
View::show_header(Lang::get('top10', 'top') . " $Limit " . Lang::get('top10', 'top_torrents'));
?>
<div class="thin">
    <div class="header">
        <h2><?= Lang::get('top10', 'top') ?> <?= $Limit ?> <?= Lang::get('top10', 'top_torrents') ?></h2>
        <? Top10View::render_linkbox("torrents"); ?>
    </div>
    <?

    if (check_perms('site_advanced_top10')) {
    ?>
        <form class="search_form" name="torrents" action="" method="get">
            <input type="hidden" name="advanced" value="1" />
            <div class="searchbox_container">
                <table cellpadding="6" cellspacing="1" border="0" class="layout border" width="100%">
                    <tr id="tagfilter">
                        <td class="label"><?= Lang::get('top10', 'tags_comma') ?>:</td>
                        <td class="ft_taglist">
                            <input type="text" name="tags" id="tags" size="75" value="<? if (!empty($_GET['tags'])) {
                                                                                            echo display_str($_GET['tags']);
                                                                                        } ?>" <? Users::has_autocomplete_enabled('other'); ?> />&nbsp;
                            <input type="radio" id="rdoAll" name="anyall" value="all" <?= ($_GET['anyall'] != 'any' ? ' checked="checked"' : '') ?> /><label for="rdoAll"> <?= Lang::get('top10', 'all') ?></label>&nbsp;&nbsp;
                            <input type="radio" id="rdoAny" name="anyall" value="any" <?= ($_GET['anyall'] == 'any' ? ' checked="checked"' : '') ?> /><label for="rdoAny"> <?= Lang::get('top10', 'any') ?></label>
                        </td>
                    </tr>
                    <tr>
                        <td class="label"><?= Lang::get('top10', 'format') ?>:</td>
                        <td>
                            <select name="format" style="width: auto;" class="ft_format">
                                <option value=""><?= Lang::get('top10', 'any') ?></option>
                                <? foreach ($Formats as $FormatName) { ?>
                                    <option value="<?= display_str($FormatName) ?>" <? if (isset($_GET['format']) && $FormatName == $_GET['format']) { ?> selected="selected" <? } ?>><?= display_str($FormatName) ?></option>
                                <?  } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="center">
                            <input type="submit" value='<?= Lang::get('top10', 'ft_torrents') ?>' />
                        </td>
                    </tr>
                </table>
            </div>
        </form>
    <?
    }

    // default setting to have them shown
    $DisableFreeTorrentTop10 = (isset($LoggedUser['DisableFreeTorrentTop10']) ? $LoggedUser['DisableFreeTorrentTop10'] : 0);
    // did they just toggle it?
    if (isset($_GET['freeleech'])) {
        // what did they choose?
        $NewPref = (($_GET['freeleech'] == 'hide') ? 1 : 0);

        // Pref id different
        if ($NewPref != $DisableFreeTorrentTop10) {
            $DisableFreeTorrentTop10 = $NewPref;
            Users::update_site_options($LoggedUser['ID'], array('DisableFreeTorrentTop10' => $DisableFreeTorrentTop10));
        }
    }

    // Modify the Where query
    if ($DisableFreeTorrentTop10) {
        $Where[] = "t.FreeTorrent='0'";
    }

    // The link should say the opposite of the current setting
    $FreeleechToggleName = ($DisableFreeTorrentTop10 ? 'show' : 'hide');
    $FreeleechToggleQuery = Format::get_url(array('freeleech', 'groups'));

    if (!empty($FreeleechToggleQuery))
        $FreeleechToggleQuery .= '&amp;';

    $FreeleechToggleQuery .= 'freeleech=' . $FreeleechToggleName;

    $GroupByToggleName = ($_GET['groups'] == 'show' ? 'hide' : 'show');
    $GroupByToggleQuery = Format::get_url(array('freeleech', 'groups'));

    if (!empty($GroupByToggleQuery))
        $GroupByToggleQuery .= '&amp;';

    $GroupByToggleQuery .= 'groups=' . $GroupByToggleName;

    $GroupBySum = '';
    $GroupBy = '';
    if ($_GET['groups'] == 'show') {
        $GroupBy = ' GROUP BY g.ID ';
        $GroupBySum = md5($GroupBy);
    }

    ?>
    <div style="text-align: right;" class="linkbox">
        <a href="top10.php?<?= $FreeleechToggleQuery ?>" class="brackets"><?= ucfirst($FreeleechToggleName) ?> freeleech in Top 10</a>
        <? if (check_perms('users_mod')) { ?>
            <a href="top10.php?<?= $GroupByToggleQuery ?>" class="brackets"><?= ucfirst($GroupByToggleName) ?> top groups</a>
        <?      } ?>
    </div>
    <?

    if (!empty($Where)) {
        $Where = '(' . implode(' AND ', $Where) . ')';
        $WhereSum = md5($Where);
    } else {
        $WhereSum = '';
    }
    $BaseQuery = '
	SELECT
		t.ID as TorrentID,
		g.ID,
		g.Name,
		g.CategoryID,
		g.wikiImage,
		g.TagList,
        g.SubName,
        g.IMDBID,
        g.TrailerLink,
        g.IMDBRating,
        g.DoubanRating,
        g.RTRating,
		t.Codec,
		t.Source,
		t.Container,
		t.Resolution,
        t.Processing,
		t.RemasterYear,
        t.Scene,
		t.Jinzhuan,
		t.Diy,
		t.Buy,
		t.Allow,
		g.Year,
		t.RemasterTitle,
        t.RemasterCustomTitle,
        t.NotMainMovie,
		t.Snatched,
		t.Seeders,
		t.Leechers,
		((t.Size * t.Snatched) + (t.Size * 0.5 * t.Leechers)) AS Data,
		g.ReleaseType,
		t.Size,
        t.FileCount,
        t.FreeTorrent,
        t.Time

	FROM torrents AS t
		LEFT JOIN torrents_group AS g ON g.ID = t.GroupID';

    if ($Details == 'all' || $Details == 'day') {
        $TopTorrentsActiveLastDay = $Cache->get_value('top10tor_day_' . $Limit . $WhereSum . $GroupBySum);
        if ($TopTorrentsActiveLastDay === false) {
            if ($Cache->get_query_lock('top10')) {
                $DayAgo = time_minus(86400);
                $Query = $BaseQuery . ' WHERE t.Seeders>0 AND ';
                if (!empty($Where)) {
                    $Query .= $Where . ' AND ';
                }
                $Query .= "
				t.Time>'$DayAgo'
				$GroupBy
				ORDER BY (t.Seeders + t.Leechers) DESC
				LIMIT $Limit;";
                $DB->query($Query);
                $TopTorrentsActiveLastDay = $DB->to_array(false, MYSQLI_ASSOC);
                $Cache->cache_value('top10tor_day_' . $Limit . $WhereSum . $GroupBySum, $TopTorrentsActiveLastDay, 3600 * 2);
                $Cache->clear_query_lock('top10');
            } else {
                $TopTorrentsActiveLastDay = false;
            }
        }
        generate_torrent_table(Lang::get('top10', 'in_the_past_day'), 'day', $TopTorrentsActiveLastDay, $Limit);
    }
    if ($Details == 'all' || $Details == 'week') {
        $TopTorrentsActiveLastWeek = $Cache->get_value('top10tor_week_' . $Limit . $WhereSum . $GroupBySum);
        if ($TopTorrentsActiveLastWeek === false) {
            if ($Cache->get_query_lock('top10')) {
                $WeekAgo = time_minus(604800);
                $Query = $BaseQuery . ' WHERE ';
                if (!empty($Where)) {
                    $Query .= $Where . ' AND ';
                }
                $Query .= "
				t.Time>'$WeekAgo'
				$GroupBy
				ORDER BY (t.Seeders + t.Leechers) DESC
				LIMIT $Limit;";
                $DB->query($Query);
                $TopTorrentsActiveLastWeek = $DB->to_array(false, MYSQLI_ASSOC);
                $Cache->cache_value('top10tor_week_' . $Limit . $WhereSum . $GroupBySum, $TopTorrentsActiveLastWeek, 3600 * 6);
                $Cache->clear_query_lock('top10');
            } else {
                $TopTorrentsActiveLastWeek = false;
            }
        }
        generate_torrent_table(Lang::get('top10', 'in_the_past_week'), 'week', $TopTorrentsActiveLastWeek, $Limit);
    }

    if ($Details == 'all' || $Details == 'month') {
        $TopTorrentsActiveLastMonth = $Cache->get_value('top10tor_month_' . $Limit . $WhereSum . $GroupBySum);
        if ($TopTorrentsActiveLastMonth === false) {
            if ($Cache->get_query_lock('top10')) {
                $Query = $BaseQuery . ' WHERE ';
                if (!empty($Where)) {
                    $Query .= $Where . ' AND ';
                }
                $Query .= "
				t.Time>'" . sqltime() . "' - INTERVAL 1 MONTH
				$GroupBy
				ORDER BY (t.Seeders + t.Leechers) DESC
				LIMIT $Limit;";
                $DB->query($Query);
                $TopTorrentsActiveLastMonth = $DB->to_array(false, MYSQLI_ASSOC);
                $Cache->cache_value('top10tor_month_' . $Limit . $WhereSum . $GroupBySum, $TopTorrentsActiveLastMonth, 3600 * 6);
                $Cache->clear_query_lock('top10');
            } else {
                $TopTorrentsActiveLastMonth = false;
            }
        }
        generate_torrent_table(Lang::get('top10', 'in_the_past_month'), 'month', $TopTorrentsActiveLastMonth, $Limit);
    }

    if ($Details == 'all' || $Details == 'year') {
        $TopTorrentsActiveLastYear = $Cache->get_value('top10tor_year_' . $Limit . $WhereSum . $GroupBySum);
        if ($TopTorrentsActiveLastYear === false) {
            if ($Cache->get_query_lock('top10')) {
                // IMPORTANT NOTE - we use WHERE t.Seeders>200 in order to speed up this query. You should remove it!
                $Query = $BaseQuery . ' WHERE ';
                if ($Details == 'all' && !$Filtered) {
                    $Query .= 't.Seeders>=200 AND ';
                    if (!empty($Where)) {
                        $Query .= $Where . ' AND ';
                    }
                } elseif (!empty($Where)) {
                    $Query .= $Where . ' AND ';
                }
                $Query .= "
				t.Time>'" . sqltime() . "' - INTERVAL 1 YEAR
				$GroupBy
				ORDER BY (t.Seeders + t.Leechers) DESC
				LIMIT $Limit;";
                $DB->query($Query);
                $TopTorrentsActiveLastYear = $DB->to_array(false, MYSQLI_ASSOC);
                $Cache->cache_value('top10tor_year_' . $Limit . $WhereSum . $GroupBySum, $TopTorrentsActiveLastYear, 3600 * 6);
                $Cache->clear_query_lock('top10');
            } else {
                $TopTorrentsActiveLastYear = false;
            }
        }
        generate_torrent_table(Lang::get('top10', 'in_the_past_year'), 'year', $TopTorrentsActiveLastYear, $Limit);
    }

    if ($Details == 'all' || $Details == 'overall') {
        $TopTorrentsActiveAllTime = $Cache->get_value('top10tor_overall_' . $Limit . $WhereSum . $GroupBySum);
        if ($TopTorrentsActiveAllTime === false) {
            if ($Cache->get_query_lock('top10')) {
                // IMPORTANT NOTE - we use WHERE t.Seeders>500 in order to speed up this query. You should remove it!
                $Query = $BaseQuery;
                if ($Details == 'all' && !$Filtered) {
                    $Query .= " WHERE t.Seeders>=500 ";
                    if (!empty($Where)) {
                        $Query .= ' AND ' . $Where;
                    }
                } elseif (!empty($Where)) {
                    $Query .= ' WHERE ' . $Where;
                }
                $Query .= "
				$GroupBy
				ORDER BY (t.Seeders + t.Leechers) DESC
				LIMIT $Limit;";
                $DB->query($Query);
                $TopTorrentsActiveAllTime = $DB->to_array(false, MYSQLI_ASSOC);
                $Cache->cache_value('top10tor_overall_' . $Limit . $WhereSum . $GroupBySum, $TopTorrentsActiveAllTime, 3600 * 6);
                $Cache->clear_query_lock('top10');
            } else {
                $TopTorrentsActiveAllTime = false;
            }
        }
        generate_torrent_table(Lang::get('top10', 'most_torrents'), 'overall', $TopTorrentsActiveAllTime, $Limit);
    }

    if (($Details == 'all' || $Details == 'snatched') && !$Filtered) {
        $TopTorrentsSnatched = $Cache->get_value('top10tor_snatched_' . $Limit . $WhereSum . $GroupBySum);
        if ($TopTorrentsSnatched === false) {
            if ($Cache->get_query_lock('top10')) {
                $Query = $BaseQuery;
                if (!empty($Where)) {
                    $Query .= ' WHERE ' . $Where;
                }
                $Query .= "
				$GroupBy
				ORDER BY t.Snatched DESC
				LIMIT $Limit;";
                $DB->query($Query);
                $TopTorrentsSnatched = $DB->to_array(false, MYSQLI_ASSOC);
                $Cache->cache_value('top10tor_snatched_' . $Limit . $WhereSum . $GroupBySum, $TopTorrentsSnatched, 3600 * 6);
                $Cache->clear_query_lock('top10');
            } else {
                $TopTorrentsSnatched = false;
            }
        }
        generate_torrent_table(Lang::get('top10', 'most_snatched'), 'snatched', $TopTorrentsSnatched, $Limit);
    }

    if (($Details == 'all' || $Details == 'data') && !$Filtered) {
        $TopTorrentsTransferred = $Cache->get_value('top10tor_data_' . $Limit . $WhereSum . $GroupBySum);
        if ($TopTorrentsTransferred === false) {
            if ($Cache->get_query_lock('top10')) {
                // IMPORTANT NOTE - we use WHERE t.Snatched>100 in order to speed up this query. You should remove it!
                $Query = $BaseQuery;
                if ($Details == 'all') {
                    $Query .= " WHERE t.Snatched>=100 ";
                    if (!empty($Where)) {
                        $Query .= ' AND ' . $Where;
                    }
                }
                $Query .= "
				$GroupBy
				ORDER BY Data DESC
				LIMIT $Limit;";
                $DB->query($Query);
                $TopTorrentsTransferred = $DB->to_array(false, MYSQLI_ASSOC);
                $Cache->cache_value('top10tor_data_' . $Limit . $WhereSum . $GroupBySum, $TopTorrentsTransferred, 3600 * 6);
                $Cache->clear_query_lock('top10');
            } else {
                $TopTorrentsTransferred = false;
            }
        }
        generate_torrent_table(Lang::get('top10', 'most_data'), 'data', $TopTorrentsTransferred, $Limit);
    }

    if (($Details == 'all' || $Details == 'seeded') && !$Filtered) {
        $TopTorrentsSeeded = $Cache->get_value('top10tor_seeded_' . $Limit . $WhereSum . $GroupBySum);
        if ($TopTorrentsSeeded === false) {
            if ($Cache->get_query_lock('top10')) {
                $Query = $BaseQuery;
                if (!empty($Where)) {
                    $Query .= ' WHERE ' . $Where;
                }
                $Query .= "
				$GroupBy
				ORDER BY t.Seeders DESC
				LIMIT $Limit;";
                $DB->query($Query);
                $TopTorrentsSeeded = $DB->to_array(false, MYSQLI_ASSOC);
                $Cache->cache_value('top10tor_seeded_' . $Limit . $WhereSum . $GroupBySum, $TopTorrentsSeeded, 3600 * 6);
                $Cache->clear_query_lock('top10');
            } else {
                $TopTorrentsSeeded = false;
            }
        }
        generate_torrent_table(Lang::get('top10', 'most_seed'), 'seeded', $TopTorrentsSeeded, $Limit);
    }

    ?>
</div>
<?
View::show_footer();

// generate a table based on data from most recent query to $DB
function generate_torrent_table($Caption, $Tag, $Details, $Limit) {
    global $LoggedUser, $Categories, $ReleaseTypes, $GroupBy;
?>
    <h3><?= Lang::get('top10', 'top') ?> <?= "$Limit $Caption" ?>
        <? if (empty($_GET['advanced'])) { ?>
            <small class="top10_quantity_links">
                <?
                switch ($Limit) {
                    case 100: ?>
                        - <a href="top10.php?details=<?= $Tag ?>" class="brackets"><?= Lang::get('top10', 'top') ?> 10</a>
                        - <span class="brackets"><?= Lang::get('top10', 'top') ?> 100</span>
                        - <a href="top10.php?type=torrents&amp;limit=250&amp;details=<?= $Tag ?>" class="brackets"><?= Lang::get('top10', 'top') ?> 250</a>
                    <? break;
                    case 250: ?>
                        - <a href="top10.php?details=<?= $Tag ?>" class="brackets"><?= Lang::get('top10', 'top') ?> 10</a>
                        - <a href="top10.php?type=torrents&amp;limit=100&amp;details=<?= $Tag ?>" class="brackets"><?= Lang::get('top10', 'top') ?> 100</a>
                        - <span class="brackets"><?= Lang::get('top10', 'top') ?> 250</span>
                    <? break;
                    default: ?>
                        - <span class="brackets"><?= Lang::get('top10', 'top') ?> 10</span>
                        - <a href="top10.php?type=torrents&amp;limit=100&amp;details=<?= $Tag ?>" class="brackets"><?= Lang::get('top10', 'top') ?> 100</a>
                        - <a href="top10.php?type=torrents&amp;limit=250&amp;details=<?= $Tag ?>" class="brackets"><?= Lang::get('top10', 'top') ?> 250</a>
                <?      } ?>
            </small>
        <?  } ?>
    </h3>
    <div class="table_container border mgb">
        <table class="torrent_table list cats numbering border m_table">
            <?
            print_torrent_table_header(TorrentTableScene::Top10, ['GroupResults' => true]);
            // Server is already processing a top10 query. Starting another one will make things slow
            if ($Details === false) {
            ?>
                <tr class="rowb">
                    <td colspan="9" class="center">
                        <?= Lang::get('top10', 'server_is_busy_processing_another_top_list_request') ?>
                    </td>
                </tr>
        </table>
    </div>
<?
                return;
            }
            // in the unlikely event that query finds 0 rows...
            if (empty($Details)) {
?>
    <tr class="rowb">
        <td colspan="9" class="center">
            <?= Lang::get('top10', 'found_no_torrents_matching_the_criteria') ?>
        </td>
    </tr>
    </table>
    </div>
<?
                return;
            }
            $Rank = 0;
            foreach ($Details as $Detail) {
                $GroupIDs[] = $Detail['ID'];
            }
            $Artists = Artists::get_artists($GroupIDs);

            foreach ($Details as $Detail) {
                list(
                    $TorrentID, $GroupID, $Name, $GroupCategoryID, $WikiImage, $TagsList, $SubName, $IMDBID, $TrailerLink, $IMDBRating, $DoubanRating, $RTRating,
                    $Codec, $Source, $Container, $Resolution, $Proessing, $RemasterYear, $Scene, $Jinzhuan, $Diy, $Buy, $Allow, $Year,
                    $RemasterTitle, $RemasterCustomTitle, $NotMainMovie, $Snatched, $Seeders, $Leechers, $Data, $ReleaseType, $Size
                ) = array_values($Detail);
                $Director = Artists::get_first_directors($Artists[$GroupID]);
                $IsBookmarked = Bookmarks::has_bookmarked('torrent', $GroupID);
                // highlight every other row
                $Rank++;

                // bad code
                $Detail['ID'] = $Detail['TorrentID'];
                $TorrentTags = new Tags($TagsList);
                $Detail['GroupID'] = $GroupID;
                $GroupDetail = array_replace([], $Detail);
                $GroupDetail['Flags']['Snatched'] = $Snatched;
                $GroupDetail['WikiImage'] = $GroupDetail['wikiImage'];
                print_ungroup_info($Detail, $GroupDetail, $GroupCategoryID, $TorrentTags, $Director, "action=basic", $Rank);
                // print row
            } //foreach ($Details as $Detail)
?>
</table>
</div>
<?
}
?>