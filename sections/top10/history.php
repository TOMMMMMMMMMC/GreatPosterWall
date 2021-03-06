<?
if (!check_perms('users_mod')) {
    error(404);
}
// if (!check_perms('site_top10_history')) {
//  error(403);
// }
View::show_header(Lang::get('top10', 'top_10_torrents_history'));
?>
<div class="thin">
    <div class="header">
        <h2><?= Lang::get('top10', 'top_10_torrents') ?></h2>
        <? Top10View::render_linkbox(); ?>
    </div>
    <div class="pad box">
        <form class="search_form" name="top10" method="get" action="">
            <input type="hidden" name="type" value="history" />
            <h3><?= Lang::get('top10', 'search_for_a_date_after') ?></h3>
            <table class="layout">
                <tr>
                    <td class="label"><?= Lang::get('top10', 'date') ?>:</td>
                    <td><input type="text" id="date" name="date" value="<?= !empty($_GET['date']) ? display_str($_GET['date']) : 'YYYY-MM-DD' ?>" onfocus="if ($('#date').raw().value == 'YYYY-MM-DD') { $('#date').raw().value = ''; }" /></td>
                </tr>
                <tr>
                    <td class="label"><?= Lang::get('top10', 'type') ?>:</td>
                    <td>
                        <input type="radio" name="datetype" value="day" checked="checked"> <?= Lang::get('top10', 'day') ?>
                        <input type="radio" name="datetype" value="week"> <?= Lang::get('top10', 'week') ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <input type="submit" value="Submit" />
                    </td>
                </tr>
            </table>
        </form>
    </div>
    <?
    if (!empty($_GET['date'])) {
        $Date = $_GET['date'];
        $SQLTime = $Date . ' 00:00:00';
        if (!validDate($SQLTime)) {
            error(Lang::get('top10', 'sth_is_wrong_with_the_date_you_provided'));
        }

        if (empty($_GET['datetype']) || $_GET['datetype'] == 'day') {
            $Type = 'day';
            $Where = "
			WHERE th.Date BETWEEN '$SQLTime' AND '$SQLTime' + INTERVAL 24 HOUR
				AND Type = 'Daily'";
        } else {
            $Type = 'week';
            $Where = "
			WHERE th.Date BETWEEN '$SQLTime' - AND '$SQLTime' + INTERVAL 7 DAY
				AND Type = 'Weekly'";
        }

        $Details = $Cache->get_value("top10_history_$SQLTime");
        if ($Details === false) {
            $DB->query("
			SELECT
				tht.Rank,
				tht.TitleString,
				tht.TagString,
				tht.TorrentID,
				g.ID,
				g.Name,
				g.CategoryID,
				g.TagList,
				t.Format,
				t.Encoding,
				t.Media,
				t.Scene,
				t.HasLog,
				t.HasCue,
				t.HasLogDB,
				t.LogScore,
				t.LogChecksum,
				t.RemasterYear,
				g.Year,
				t.RemasterTitle
			FROM top10_history AS th
				LEFT JOIN top10_history_torrents AS tht ON tht.HistoryID = th.ID
				LEFT JOIN torrents AS t ON t.ID = tht.TorrentID
				LEFT JOIN torrents_group AS g ON g.ID = t.GroupID
			$Where
			ORDER BY tht.Rank ASC");

            $Details = $DB->to_array();

            $Cache->cache_value("top10_history_$SQLTime", $Details, 3600 * 24);
        }
    ?>

        <br />
        <div class="pad box">
            <h3><?= Lang::get('top10', 'top_10_for_before') ?><?= ($Type == 'day' ? $Date : Lang::get('top10', 'the_first_week_after_before') . "$Date" . Lang::get('top10', 'the_first_week_after_after')) ?><?= Lang::get('top10', 'top_10_for_after') ?></h3>
            <table class="torrent_table cats numbering border">
                <tr class="colhead">
                    <td class="center" style="width: 15px;"></td>
                    <td class="center"></td>
                    <td><strong><?= Lang::get('top10', 'name') ?></strong></td>
                </tr>
                <?
                foreach ($Details as $Detail) {
                    list(
                        $Rank, $TitleString, $TagString, $TorrentID, $GroupID, $GroupName, $GroupCategoryID, $TorrentTags,
                        $Format, $Encoding, $Media, $Scene, $HasLog, $HasCue, $HasLogDB, $LogScore, $LogChecksum, $Year, $GroupYear,
                        $RemasterTitle, $Snatched, $Seeders, $Leechers, $Data
                    ) = $Detail;

                    // highlight every other row
                    $Highlight = ($Rank % 2 ? 'a' : 'b');

                    if ($GroupID) {
                        // Group still exists
                        $DisplayName = '';

                        $Artists = Artists::get_artist($GroupID);

                        if (!empty($Artists)) {
                            $DisplayName = Artists::display_artists($Artists, true, true);
                        }

                        $DisplayName .= "<a href=\"torrents.php?id=$GroupID&amp;torrentid=$TorrentID\" class=\"tooltip\" title=\"" . Lang::get('global', 'view_torrent') . "\" dir=\"ltr\">$GroupName</a>";

                        if ($GroupCategoryID == 1 && $GroupYear > 0) {
                            $DisplayName .= " [$GroupYear]";
                        }

                        // append extra info to torrent title
                        $ExtraInfo = '';
                        $AddExtra = '';
                        if ($Format) {
                            $ExtraInfo .= $Format;
                            $AddExtra = ' / ';
                        }
                        if ($Encoding) {
                            $ExtraInfo .= $AddExtra . $Encoding;
                            $AddExtra = ' / ';
                        }
                        //"FLAC / Lossless / Log (100%) / Cue / CD";
                        if ($HasLog) {
                            $ExtraInfo .= "$AddExtra Log" . ($HasLogDB ? " ($LogScore%)" : '');
                            $AddExtra = ' / ';
                        }
                        if ($HasCue) {
                            $ExtraInfo .= "{$AddExtra}Cue";
                            $AddExtra = ' / ';
                        }
                        if ($Media) {
                            $ExtraInfo .= $AddExtra . $Media;
                            $AddExtra = ' / ';
                        }
                        if ($Scene) {
                            $ExtraInfo .= "{$AddExtra}Scene";
                            $AddExtra = ' / ';
                        }
                        if ($Year > 0) {
                            $ExtraInfo .= $AddExtra . $Year;
                            $AddExtra = ' ';
                        }
                        if ($RemasterTitle) {
                            $ExtraInfo .= $AddExtra . $RemasterTitle;
                        }
                        if ($ExtraInfo != '') {
                            $ExtraInfo = "- [$ExtraInfo]";
                        }

                        $DisplayName .= $ExtraInfo;
                        $TorrentTags = new Tags($TorrentTags);
                    } else {
                        $DisplayName = "$TitleString (Deleted)";
                        $TorrentTags = new Tags($TagString);
                    } // if ($GroupID)

                ?>
                    <tr class="group_torrent row<?= $Highlight ?>">
                        <td style="padding: 8px; text-align: center;"><strong><?= $Rank ?></strong></td>
                        <td class="center cats_col">
                            <div title="<?= $TorrentTags->title() ?>" class="tooltip <?= Format::css_category($GroupCategoryID) ?> <?= $TorrentTags->css_name() ?>"></div>
                        </td>
                        <td>
                            <span><?= ($GroupID ? '<a href="torrents.php?action=download&amp;id=' . $TorrentID . '&amp;authkey=' . $LoggedUser['AuthKey'] . '&amp;torrent_pass=' . $LoggedUser['torrent_pass'] . ' title="Download" class="brackets tooltip">DL</a>' : '(Deleted)') ?></span>
                            <?= $DisplayName ?>
                            <div class="tags"><?= $TorrentTags->format() ?></div>
                        </td>
                    </tr>
                <?
                } //foreach ($Details as $Detail)
                ?>
            </table><br />
        </div>
</div>
<?
    }
    View::show_footer();
?>