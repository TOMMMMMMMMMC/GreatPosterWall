<?php
/*
 * This is the frontend of reporting a torrent, it's what users see when
 * they visit reportsv2.php?id=xxx
 */

include(SERVER_ROOT . '/sections/torrents/functions.php');
include(SERVER_ROOT . '/classes/torrenttable.class.php');

//If we're not coming from torrents.php, check we're being returned because of an error.
if (!isset($_GET['id']) || !is_number($_GET['id'])) {
    if (!isset($Err)) {
        error(404);
    }
} else {
    $TorrentID = $_GET['id'];
    $DB->query("
		SELECT tg.CategoryID, t.GroupID
		FROM torrents_group AS tg
			LEFT JOIN torrents AS t ON t.GroupID = tg.ID
		WHERE t.ID = " . $_GET['id']);
    list($CategoryID, $GroupID) = $DB->next_record();
    if (empty($CategoryID) || empty($GroupID)) {
        // Deleted torrent
        header("Location: log.php?search=Torrent+" . $TorrentID);
        die();
    }
    $Artists = Artists::get_artist($GroupID);
    $TorrentCache = get_group_info($GroupID, true);
    $GroupDetails = $TorrentCache[0];
    $TorrentList = $TorrentCache[1];
    // Resolve the torrentlist to the one specific torrent being reported
    foreach ($TorrentList as &$Torrent) {
        // Remove unneeded entries
        if ($Torrent['ID'] != $TorrentID) {
            unset($TorrentList[$Torrent['ID']]);
        }
    }
    // Group details
    list(
        $WikiBody, $WikiImage, $IMDBID, $IMDBRating, $Duration, $ReleaseDate, $Region, $Language, $RTRating, $DoubanRating, $IMDBVote, $DoubanVote, $DoubanID, $RTTitle, $GroupID, $GroupName, $GroupYear,
        $GroupRecordLabel, $GroupCatalogueNumber, $ReleaseType, $GroupCategoryID,
        $GroupTime, $GroupVanityHouse, $TorrentTags, $TorrentTagIDs, $TorrentTagUserIDs,
        $TagPositiveVotes, $TagNegativeVotes, $SubName
    ) = array_values($GroupDetails);
    $AltName = $Title = $DisplayName = Torrents::torrent_group_name($GroupDetails, true);
    $WikiBody = Text::full_format($WikiBody);
}

View::show_header(Lang::get('reportsv2', 'report'), 'reportsv2,browse,torrent,bbcode,recommend');
?>

<div class="thin">
    <div class="header">
        <h2><?= Lang::get('reportsv2', 'report_a_torrent') ?></h2>
    </div>
    <div class="header">
        <h3><?= $DisplayName ?></h3>
    </div>
    <div class="thin">
        <table class="torrent_table show details<?= ($GroupFlags['IsSnatched'] ? ' snatched' : '') ?>" id="torrent_details">
            <tr class="colhead_dark">
                <td width="80%"><strong><?= Lang::get('reportsv2', 'reported_torrent') ?></strong></td>
                <td class="number_column">
                    <i class="fa fa-hdd tooltip" aria-hidden="true" title="<?= Lang::get('global', 'size') ?>"></i>
                    <!-- <strong>大小</strong> -->
                </td>
                <td class="number_column sign snatches">
                    <i class="fa fa-check tooltip" aria-hidden="true" title="<?= Lang::get('global', 'snatched') ?>"></i>
                    <!-- <img src="static/styles/<?= ($LoggedUser['StyleName']) ?>/images/snatched.png" class="tooltip" alt="Snatches" title="已完成用户" /> -->
                </td>
                <td class="number_column sign seeders">
                    <i class="fa fa-upload tooltip" aria-hidden="true" title="<?= Lang::get('global', 'seeders') ?>"></i>
                    <!-- <img src="static/styles/<?= ($LoggedUser['StyleName']) ?>/images/seeders.png" class="tooltip" alt="Seeders" title="做种中" /> -->
                </td>
                <td class="number_column sign leechers">
                    <i class="fa fa-download tooltip" aria-hidden="true" title="<?= Lang::get('global', 'leechers') ?>"></i>
                    <!-- <img src="static/styles/<?= ($LoggedUser['StyleName']) ?>/images/leechers.png" class="tooltip" alt="Leechers" title="下载中" /> -->
                </td>
            </tr>
            <?
            print_group($LoggedUser, $GroupID, $GroupName, $GroupCategoryID, $ReleaseType, $TorrentList, $Types, false);
            ?>
        </table>
    </div>

    <form class="create_form" name="report" action="reportsv2.php?action=takereport" enctype="multipart/form-data" method="post" id="reportform">
        <div>
            <input type="hidden" name="submit" value="true" />
            <input type="hidden" name="auth" value="<?= $LoggedUser['AuthKey'] ?>" />
            <input type="hidden" name="torrentid" value="<?= $TorrentID ?>" />
            <input type="hidden" name="categoryid" value="<?= $CategoryID ?>" />
        </div>

        <h3 id="report-torrent"><?= Lang::get('reportsv2', 'report_information') ?></h3>
        <div id="report-torrent-body" class="box pad">
            <table class="layout">
                <tr>
                    <td class="label"><?= Lang::get('reportsv2', 'reason') ?>:</td>
                    <td>
                        <select id="type" name="type" onchange="ChangeReportType();">
                            <?
                            if (!empty($Types[$CategoryID])) {
                                $TypeList = $Types['master'] + $Types[$CategoryID];
                                $Priorities = array();
                                foreach ($TypeList as $Key => $Value) {
                                    $Priorities[$Key] = $Value['priority'];
                                }
                                array_multisort($Priorities, SORT_ASC, $TypeList);
                            } else {
                                $TypeList = $Types['master'];
                            }
                            foreach ($TypeList as $Type => $Data) {
                            ?>
                                <option value="<?= ($Type) ?>" <?= $Type == $_GET['type'] ? 'selected="selected"' : "" ?>><?= ($Data['title']) ?></option>
                            <?              } ?>
                        </select>
                    </td>
                </tr>
            </table>
            <?= Lang::get('reportsv2', 'report_introduction') ?>

            <div id="dynamic_form">
                <?
                /*
                 * THIS IS WHERE SEXY AJAX COMES IN
                 * The following malarky is needed so that if you get sent back here, the fields are filled in.
                 */
                ?>
                <input id="sitelink" type="hidden" name="sitelink" size="50" value="<?= (!empty($_POST['sitelink']) ? display_str($_POST['sitelink']) : '') ?>" />
                <input id="image" type="hidden" name="image" size="50" value="<?= (!empty($_POST['image']) ? display_str($_POST['image']) : '') ?>" />
                <input id="track" type="hidden" name="track" size="8" value="<?= (!empty($_POST['track']) ? display_str($_POST['track']) : '') ?>" />
                <input id="link" type="hidden" name="link" size="50" value="<?= (!empty($_POST['link']) ? display_str($_POST['link']) : '') ?>" />
                <input id="extra" type="hidden" name="extra" value="<?= (!empty($_POST['extra']) ? display_str($_POST['extra']) : '') ?>" />

                <script type="text/javascript">
                    ChangeReportType();
                </script>
            </div>
        </div>
        <div align="center" id="submit-report">
            <input type="submit" value="Submit report" />
        </div>
    </form>
</div>
<?php
View::show_footer();
?>