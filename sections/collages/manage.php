<?
$CollageID = $_GET['collageid'];
if (!is_number($CollageID)) {
    error(0);
}

$DB->query("
	SELECT Name, UserID, CategoryID
	FROM collages
	WHERE ID = '$CollageID'");
list($Name, $UserID, $CategoryID) = $DB->next_record();
if ($CategoryID == 0 && $UserID != $LoggedUser['ID'] && !check_perms('site_collages_delete')) {
    error(403);
}
if ($CategoryID == array_search(ARTIST_COLLAGE, $CollageCats)) {
    error(404);
}

$DB->query("
	SELECT
		ct.GroupID,
		um.ID,
		um.Username,
		ct.Sort,
		tg.CatalogueNumber
	FROM collages_torrents AS ct
		JOIN torrents_group AS tg ON tg.ID = ct.GroupID
		LEFT JOIN users_main AS um ON um.ID = ct.UserID
	WHERE ct.CollageID = '$CollageID'
	ORDER BY ct.Sort");

$GroupIDs = $DB->collect('GroupID');

$CollageDataList = $DB->to_array('GroupID', MYSQLI_ASSOC);
if (count($GroupIDs) > 0) {
    $TorrentList = Torrents::get_groups($GroupIDs);
} else {
    $TorrentList = array();
}

View::show_header(Lang::get('collages', 'manage_collage') . ": $Name", 'jquery-ui,jquery.tablesorter,sort');

?>
<div class="thin">
    <div class="header">
        <h2><?= Lang::get('collages', 'manage_collage') ?> <a href="collages.php?id=<?= $CollageID ?>"><?= $Name ?></a></h2>
    </div>
    <table width="100%" class="layout" id="sorting_table">
        <tr class="colhead">
            <td id="sorting_head"><?= Lang::get('collages', 'sorting') ?></td>
        </tr>
        <tr>
            <td id="drag_drop_textnote"><?= Lang::get('collages', 'drag_drop_textnote') ?></td>
        </tr>
    </table>

    <div class="drag_drop_save hidden">
        <input type="button" name="submit" value="Save All Changes" class="save_sortable_collage" />
    </div>
    <table id="manage_collage_table">
        <thead>
            <tr class="colhead">
                <th style="width: 7%;" data-sorter="false"><?= Lang::get('collages', 'order') ?></th>
                <th style="width: 1%;"><span><abbr class="tooltip" title="Current rank">#</abbr></span></th>
                <th style="width: 7%;"><span><?= Lang::get('collages', 'cat_number') ?></span></th>
                <th style="width: 1%;"><span><?= Lang::get('collages', 'year') ?></span></th>
                <th style="width: 15%;" data-sorter="ignoreArticles"><span><?= Lang::get('global', 'artist') ?></span></th>
                <th data-sorter="ignoreArticles"><span><?= Lang::get('global', 'torrent') ?></span></th>
                <th style="width: 1%;"><span><?= Lang::get('collages', 'adder') ?></span></th>
                <th style="width: 1%; text-align: right;" class="nobr" data-sorter="false"><span><abbr class="tooltip" title="<?= Lang::get('collages', 'tweak_title') ?>"><?= Lang::get('collages', 'tweak') ?></abbr></span></th>
            </tr>
        </thead>
        <tbody>
            <?

            $Number = 0;
            foreach ($GroupIDs as $GroupID) {
                if (!isset($TorrentList[$GroupID])) {
                    continue;
                }
                $Group = $TorrentList[$GroupID];
                extract(Torrents::array_group($Group));
                list(, $UserID, $Username, $Sort, $CatNum) = array_values($CollageDataList[$GroupID]);

                $Number++;

                $DisplayName = '';
                if (!empty($ExtendedArtists[1]) || !empty($ExtendedArtists[4]) || !empty($ExtendedArtists[5]) || !empty($ExtendedArtists[6])) {
                    unset($ExtendedArtists[2]);
                    unset($ExtendedArtists[3]);
                    $DisplayName .= Artists::display_artists($ExtendedArtists, true, false);
                } elseif (count($Artists) > 0) {
                    $DisplayName .= Artists::display_artists(array('1' => $Artists), true, false);
                }
                $TorrentLink = "<a href=\"torrents.php?id=$GroupID\" class=\"tooltip\" title=\"" . Lang::get('global', 'view_torrent_group') . "\">$GroupName</a>";
                $GroupYear = $GroupYear > 0 ? $GroupYear : '';
                if ($GroupVanityHouse) {
                    $DisplayName .= ' [<abbr class="tooltip" title="' . Lang::get('global', 'this_is_vh') . '">VH</abbr>]';
                }

                $AltCSS = ($Number % 2 === 0) ? 'rowa' : 'rowb';
            ?>
                <tr class="drag <?= $AltCSS ?>" id="li_<?= $GroupID ?>">
                    <form class="manage_form" name="collage" action="collages.php" method="post">
                        <td>
                            <input class="sort_numbers" type="text" name="sort" value="<?= $Sort ?>" id="sort_<?= $GroupID ?>" size="4" />
                        </td>
                        <td><?= $Number ?></td>
                        <td><?= trim($CatNum) ?: '&nbsp;' ?></td>
                        <td><?= trim($GroupYear) ?: '&nbsp;' ?></td>
                        <td><?= trim($DisplayName) ?: '&nbsp;' ?></td>
                        <td><?= trim($TorrentLink) ?></td>
                        <td class="nobr"><?= Users::format_username($UserID, $Username, false, false, false) ?></td>
                        <td class="nobr">
                            <input type="hidden" name="action" value="manage_handle" />
                            <input type="hidden" name="auth" value="<?= $LoggedUser['AuthKey'] ?>" />
                            <input type="hidden" name="collageid" value="<?= $CollageID ?>" />
                            <input type="hidden" name="groupid" value="<?= $GroupID ?>" />
                            <input type="submit" name="submit" value="Edit" />
                            <input type="submit" name="submit" value="Remove" />
                        </td>
                    </form>
                </tr>
            <? } ?>
        </tbody>
    </table>
    <div class="drag_drop_save hidden">
        <input type="button" name="submit" value="Save All Changes" class="save_sortable_collage" />
    </div>
    <form class="dragdrop_form hidden" name="collage" action="collages.php" method="post" id="drag_drop_collage_form">
        <div>
            <input type="hidden" name="action" value="manage_handle" />
            <input type="hidden" name="auth" value="<?= $LoggedUser['AuthKey'] ?>" />
            <input type="hidden" name="collageid" value="<?= $CollageID ?>" />
            <input type="hidden" name="groupid" value="1" />
            <input type="hidden" name="drag_drop_collage_sort_order" id="drag_drop_collage_sort_order" readonly="readonly" value="" />
        </div>
    </form>
</div>
<? View::show_footer(); ?>