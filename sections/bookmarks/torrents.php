<?php
include(SERVER_ROOT . '/classes/torrenttable.class.php');
ini_set('max_execution_time', 600);
set_time_limit(0);

//~~~~~~~~~~~ Main bookmarks page ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~//

function compare($X, $Y) {
    return ($Y['count'] - $X['count']);
}

if (!empty($_GET['userid'])) {
    if (!check_perms('users_override_paranoia')) {
        error(403);
    }
    $UserID = $_GET['userid'];
    if (!is_number($UserID)) {
        error(404);
    }
    $DB->query("
		SELECT Username
		FROM users_main
		WHERE ID = '$UserID'");
    list($Username) = $DB->next_record();
} else {
    $UserID = $LoggedUser['ID'];
}

$Sneaky = $UserID !== $LoggedUser['ID'];
$Title = $Sneaky ? "$Username" . Lang::get('bookmarks', 's_bookmarked_torrent_groups') : Lang::get('bookmarks', 'your_bookmarked_torrent_groups');

$NumGroups = 0;
$ArtistCount = array();

list($GroupIDs, $CollageDataList, $TorrentList) = Users::get_bookmarks($UserID);
foreach ($GroupIDs as $Idx => $GroupID) {
    if (!isset($TorrentList[$GroupID])) {
        unset($GroupIDs[$Idx]);
        continue;
    }
    // Handle stats and stuff
    $NumGroups++;
    extract(Torrents::array_group($TorrentList[$GroupID]));
    if ($Artists) {
        foreach ($Artists as $Artist) {
            if (!isset($ArtistCount[$Artist['id']])) {
                $ArtistCount[$Artist['id']] = array('artist' => $Artist, 'count' => 1);
            } else {
                $ArtistCount[$Artist['id']]['count']++;
            }
        }
    }
    // We need to append the tag list to the Tags::$all array
    new Tags($TagList);
}

$GroupIDs = array_values($GroupIDs);

$CollageCovers = isset($LoggedUser['CollageCovers']) ? (int)$LoggedUser['CollageCovers'] : 25;

View::show_header($Title, 'browse,collage');
?>
<div class="thin">
    <div class="header">
        <h2>
            <? if (!$Sneaky) { ?><a href="feeds.php?feed=torrents_bookmarks_t_<?= $LoggedUser['torrent_pass'] ?>&amp;user=<?= $LoggedUser['ID'] ?>&amp;auth=<?= $LoggedUser['RSS_Auth'] ?>&amp;passkey=<?= $LoggedUser['torrent_pass'] ?>&amp;authkey=<?= $LoggedUser['AuthKey'] ?>&amp;name=<?= urlencode(SITE_NAME . ': Bookmarked Torrents') ?>"><img src="<?= STATIC_SERVER ?>/common/symbols/rss.png" alt="RSS feed" /></a>&nbsp;
                <? } ?><?= $Title ?></h2>
        <div class="linkbox">
            <a href="bookmarks.php?type=torrents" class="brackets"><?= Lang::get('global', 'torrents') ?></a>
            <a href="bookmarks.php?type=artists" class="brackets"><?= Lang::get('global', 'artists') ?></a>
            <?
            if (ENABLE_COLLAGES) {
            ?>
                <a href="bookmarks.php?type=collages" class="brackets"><?= Lang::get('bookmarks', 'collages') ?></a>
            <?
            }
            ?>
            <a href="bookmarks.php?type=requests" class="brackets"><?= Lang::get('global', 'requests') ?></a>
            <? if (count($TorrentList) > 0) { ?>
                <br /><br />
                <a href="bookmarks.php?action=remove_snatched&amp;auth=<?= $LoggedUser['AuthKey'] ?>" class="brackets" onclick="return confirm('<?= Lang::get('bookmarks', 'remove_snatched_confirm') ?>');"><?= Lang::get('bookmarks', 'remove_snatched') ?></a>
                <a href="bookmarks.php?action=edit&amp;type=torrents" class="brackets"><?= Lang::get('bookmarks', 'manage_torrents') ?></a>
            <? } ?>
        </div>
    </div>
    <? if (count($TorrentList) === 0) { ?>
        <div class="box pad" align="center">
            <h2><?= Lang::get('bookmarks', 'no_bookmarked_torrents') ?></h2>
        </div>
</div>
<!--content-->
<?
        View::show_footer();
        die();
    } ?>
<div class="grid_container">
    <div class="sidebar">
        <div class="box box_info box_statistics_bookmarked_torrents">
            <div class="head"><strong><?= Lang::get('bookmarks', 'stats') ?></strong></div>
            <ul class="stats nobullet">
                <li><?= Lang::get('bookmarks', 'torrent_groups') ?>: <?= $NumGroups ?></li>
                <li><?= Lang::get('global', 'artists') ?>: <?= count($ArtistCount) ?></li>
            </ul>
        </div>
        <div class="box box_tags">
            <div class="head"><strong><?= Lang::get('bookmarks', 'top_tags') ?></strong></div>
            <div class="pad">
                <ol>
                    <? Tags::format_top(5) ?>
                </ol>
            </div>
        </div>
        <div class="box box_artists">
            <div class="head"><strong><?= Lang::get('bookmarks', 'top_artists') ?></strong></div>
            <div class="pad">
                <?
                $Indent = "\t\t\t\t";
                if (count($ArtistCount) > 0) {
                    echo "$Indent<ol>\n";
                    uasort($ArtistCount, 'compare');
                    $i = 0;
                    foreach ($ArtistCount as $ID => $Artist) {
                        $i++;
                        if ($i > 10) {
                            break;
                        }
                ?>
                        <?= Artists::display_artist($Artist['artist']) ?>
                <?
                    }
                    echo "$Indent</ol>\n";
                } else {
                    echo "$Indent<ul class=\"nobullet\">\n";
                    echo "$Indent\t<li>There are no artists to display.</li>\n";
                    echo "$Indent</ul>\n";
                }
                ?>
            </div>
        </div>
    </div>
    <div class="main_column">
        <?

        if ($CollageCovers !== 0) { ?>
            <div id="coverart" class="box">
                <div class="head" id="coverhead"><strong><?= Lang::get('bookmarks', 'cover_art') ?></strong></div>
                <ul class="collage_images" id="collage_page0">
                    <?
                    for ($Idx = 0; $Idx < min($NumGroups, $CollageCovers); $Idx++) {
                        echo Collages::collage_cover_row($TorrentList[$GroupIDs[$Idx]]);
                    }
                    ?>
                </ul>
            </div>
            <? if ($NumGroups > $CollageCovers) { ?>
                <div class="linkbox pager" style="clear: left;" id="pageslinksdiv">
                    <span id="firstpage" class="invisible"><a href="#" class="pageslink" onclick="collageShow.page(0); return false;">&lt;&lt; First</a> | </span>
                    <span id="prevpage" class="invisible"><a href="#" id="prevpage" class="pageslink" onclick="collageShow.prevPage(); return false;">&lt; Prev</a> | </span>
                    <? for ($i = 0; $i < $NumGroups / $CollageCovers; $i++) { ?>
                        <span id="pagelink<?= $i ?>" class="<?= (($i > 4) ? 'hidden' : '') ?><?= (($i === 0) ? ' selected' : '') ?>"><a href="#" class="pageslink" onclick="collageShow.page(<?= $i ?>, this); return false;"><?= ($CollageCovers * $i + 1) ?>-<?= min($NumGroups, $CollageCovers * ($i + 1)) ?></a><?= (($i !== ceil($NumGroups / $CollageCovers) - 1) ? ' | ' : '') ?></span>
                    <?      } ?>
                    <!--<span id="nextbar" class="<?= (($NumGroups / $CollageCovers > 5) ? 'hidden' : '') ?>"> | </span>-->
                    <span id="nextpage"><a href="#" class="pageslink" onclick="collageShow.nextPage(); return false;">Next
                            &gt;</a></span>
                    <span id="lastpage" class="<?= (ceil($NumGroups / $CollageCovers) === 2 ? 'invisible' : '') ?>"> | <a href="#" id="lastpage" class="pageslink" onclick="collageShow.page(<?= (ceil($NumGroups / $CollageCovers) - 1) ?>); return false;">Last
                            &gt;&gt;</a></span>
                </div>
                <script type="text/javascript">
                    <?php
                    $CollagePages = array();
                    for ($i = 0; $i < $NumGroups / $CollageCovers; $i++) {
                        $Groups = array_slice($GroupIDs, $i * $CollageCovers, $CollageCovers);
                        $CollagePages[] = implode(
                            '',
                            array_map(
                                function ($GroupID) use ($TorrentList) {
                                    return Collages::collage_cover_row($TorrentList[$GroupID]);
                                },
                                $Groups
                            )
                        );
                    } ?>
                    collageShow.init(<?= json_encode($CollagePages) ?>);
                </script>
        <?php
                unset($CollagePages);
            }
        }
        ?>
        <table class="cmp-torrent-table torrent_table grouping cats m_table" id="torrent_table">
            <?php
            print_torrent_table_header(TorrentTableScene::Bookmarks);
            print_all_group($GroupIDs, $TorrentList, $CollageDataList);
            ?>
        </table>
    </div>
</div>
</div>

<?php
View::show_footer();
