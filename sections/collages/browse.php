<?php
define('COLLAGES_PER_PAGE', 25);

list($Page, $Limit) = Format::page_limit(COLLAGES_PER_PAGE);


$OrderVals = array(
    'Time' => Lang::get('collages', 'search_time'),
    'Name' => Lang::get('collages', 'search_name'),
    'Subscribers' => Lang::get('collages', 'search_subscribers'),
    'Torrents' => Lang::get('collages', 'search_torrents'),
    'Updated' => Lang::get('collages', 'search_updated')
);
$WayVals = array('Ascending' => Lang::get('collages', 'search_ascending'), 'Descending' => Lang::get('collages', 'search_descending'));
$OrderTable = array('Time' => 'ID', 'Name' => 'c.Name', 'Subscribers' => 'c.Subscribers', 'Torrents' => 'NumTorrents', 'Updated' => 'c.Updated');
$WayTable = array('Ascending' => 'ASC', 'Descending' => 'DESC');

// Are we searching in bodies, or just names?
if (!empty($_GET['type'])) {
    $Type = $_GET['type'];
    if (!in_array($Type, array('c.name', 'description'))) {
        $Type = 'c.name';
    }
} else {
    $Type = 'c.name';
}

if (!empty($_GET['search'])) {
    // What are we looking for? Let's make sure it isn't dangerous.
    $Search = db_string(trim($_GET['search']));
    // Break search string down into individual words
    $Words = explode(' ', $Search);
}

if (!empty($_GET['tags'])) {
    $Tags = explode(',', db_string(trim($_GET['tags'])));
    foreach ($Tags as $ID => $Tag) {
        $Tags[$ID] = Misc::sanitize_tag($Tag);
    }
}

if (!empty($_GET['cats'])) {
    $Categories = $_GET['cats'];
    foreach ($Categories as $Cat => $Accept) {
        if (empty($CollageCats[$Cat]) || !$Accept) {
            unset($Categories[$Cat]);
        }
    }
    $Categories = array_keys($Categories);
} else {
    $Categories = array(1, 2, 3, 4, 5, 6, 7);
}

// Ordering
if (!empty($_GET['order_by']) && !empty($OrderTable[$_GET['order_by']])) {
    $Order = $OrderTable[$_GET['order_by']];
} else {
    $Order = 'ID';
}

if (!empty($_GET['order_way']) && !empty($WayTable[$_GET['order_way']])) {
    $Way = $WayTable[$_GET['order_way']];
} else {
    $Way = 'DESC';
}

$BookmarkView = !empty($_GET['bookmarks']);

if ($BookmarkView) {
    $Categories[] = 0;
    $BookmarkJoin = 'INNER JOIN bookmarks_collages AS bc ON c.ID = bc.CollageID';
} else {
    $BookmarkJoin = '';
}

$BaseSQL = $SQL = "
	SELECT
		SQL_CALC_FOUND_ROWS
		c.ID,
		c.Name,
		c.NumTorrents,
		c.TagList,
		c.CategoryID,
		c.UserID,
		c.Subscribers,
		c.Updated
	FROM collages AS c
		$BookmarkJoin
	WHERE Deleted = '0'";

if ($BookmarkView) {
    $SQL .= " AND bc.UserID = '" . $LoggedUser['ID'] . "'";
}

if (!empty($Search)) {
    $SQL .= " AND $Type LIKE '%";
    $SQL .= implode("%' AND $Type LIKE '%", $Words);
    $SQL .= "%'";
}

if (isset($_GET['tags_type']) && $_GET['tags_type'] === '0') { // Any
    $_GET['tags_type'] = '0';
} else { // All
    $_GET['tags_type'] = '1';
}

if (!empty($Tags)) {
    $SQL .= " AND (TagList LIKE '%";
    if ($_GET['tags_type'] === '0') {
        $SQL .= implode("%' OR TagList LIKE '%", $Tags);
    } else {
        $SQL .= implode("%' AND TagList LIKE '%", $Tags);
    }
    $SQL .= "%')";
}

if (!empty($_GET['userid'])) {
    $UserID = $_GET['userid'];
    if (!is_number($UserID)) {
        error(404);
    }
    $User = Users::user_info($UserID);
    $Perms = Permissions::get_permissions($User['PermissionID']);
    $UserClass = $Perms['Class'];

    $UserLink = '<a href="user.php?id=' . $UserID . '">' . $User['Username'] . '</a>';
    if (!empty($_GET['contrib'])) {
        if (!check_paranoia('collagecontribs', $User['Paranoia'], $UserClass, $UserID)) {
            error(403);
        }
        $DB->query("
			SELECT DISTINCT CollageID
			FROM collages_torrents
			WHERE UserID = $UserID");
        $CollageIDs = $DB->collect('CollageID');
        if (empty($CollageIDs)) {
            $SQL .= " AND 0";
        } else {
            $SQL .= " AND c.ID IN(" . db_string(implode(',', $CollageIDs)) . ')';
        }
    } else {
        if (!check_paranoia('collages', $User['Paranoia'], $UserClass, $UserID)) {
            error(403);
        }
        $SQL .= " AND UserID = '" . $_GET['userid'] . "'";
    }
    $Categories[] = 0;
}

if (!empty($Categories)) {
    $SQL .= " AND CategoryID IN(" . db_string(implode(',', $Categories)) . ')';
}

if (isset($_GET['action']) && $_GET['action'] === 'mine') {
    $SQL = $BaseSQL;
    $SQL .= "
		AND c.UserID = '" . $LoggedUser['ID'] . "'
		AND c.CategoryID = 0";
}

$SQL .= "
	ORDER BY $Order $Way
	LIMIT $Limit";
$DB->query($SQL);
$Collages = $DB->to_array();
$DB->query('SELECT FOUND_ROWS()');
list($NumResults) = $DB->next_record();

View::show_header(Lang::get('collages', 'browse_collages'));
?>
<div class="thin">
    <div class="header">
        <? if ($BookmarkView) { ?>
            <h2><?= Lang::get('collages', 'your_bookmarked_collages') ?></h2>
        <?  } else { ?>
            <h2><?= Lang::get('collages', 'browse_collages') ?><?= (!empty($UserLink) ? (isset($CollageIDs) ? " with contributions by $UserLink" : " started by $UserLink") : '') ?></h2>
        <?  } ?>
    </div>
    <? if (!$BookmarkView) { ?>
        <div>
            <form class="search_form" name="collages" action="" method="get">
                <div><input type="hidden" name="action" value="search" /></div>
                <table cellpadding="6" cellspacing="1" border="0" class="layout border" width="100%">
                    <tr id="search_terms">
                        <td class="label"><?= Lang::get('collages', 'ftb_searchstr') ?></td>
                        <td>
                            <input type="search" name="search" size="70" value="<?= (!empty($_GET['search']) ? display_str($_GET['search']) : '') ?>" />
                        </td>
                    </tr>
                    <tr id="tagfilter">
                        <td class="label"><?= Lang::get('collages', 'tags') ?></td>
                        <td>
                            <input type="text" id="tags" name="tags" size="70" value="<?= (!empty($_GET['tags']) ? display_str($_GET['tags']) : '') ?>" <? Users::has_autocomplete_enabled('other'); ?> />&nbsp;
                            <input type="radio" name="tags_type" id="tags_type0" value="0" <? Format::selected('tags_type', 0, 'checked') ?> /><label for="tags_type0"> <?= Lang::get('collages', 'any') ?></label>&nbsp;&nbsp;
                            <input type="radio" name="tags_type" id="tags_type1" value="1" <? Format::selected('tags_type', 1, 'checked') ?> /><label for="tags_type1"> <?= Lang::get('collages', 'all') ?></label>
                        </td>
                    </tr>
                    <tr id="categories">
                        <td class="label"><?= Lang::get('collages', 'type') ?></td>
                        <td>
                            <? foreach ($CollageCats as $ID => $Cat) { ?>
                                <input type="checkbox" value="1" name="cats[<?= $ID ?>]" id="cats_<?= $ID ?>" <? if (in_array($ID, $Categories)) {
                                                                                                                    echo ' checked="checked"';
                                                                                                                } ?> />
                                <label for="cats_<?= $ID ?>"><?= Lang::get('collages', 'collagecats')[$ID] ?></label>&nbsp;&nbsp;
                            <?      } ?>
                        </td>
                    </tr>
                    <tr id="search_name_description">
                        <td class="label"><?= Lang::get('collages', 'search_for') ?></td>
                        <td>
                            <input type="radio" name="type" value="c.name" <? if ($Type === 'c.name') {
                                                                                echo 'checked="checked" ';
                                                                            } ?> /> <?= Lang::get('collages', 'search_name') ?>&nbsp;&nbsp;
                            <input type="radio" name="type" value="description" <? if ($Type === 'description') {
                                                                                    echo 'checked="checked" ';
                                                                                } ?> /> <?= Lang::get('collages', 'search_descriptions') ?>
                        </td>
                    </tr>
                    <tr id="order_by">
                        <td class="label"><?= Lang::get('collages', 'ft_order') ?></td>
                        <td>
                            <select name="order_by" class="ft_order_by">
                                <? foreach ($OrderVals as $Key => $Cur) { ?>
                                    <option value="<?= $Key ?>" <? if (isset($_GET['order_by']) && $_GET['order_by'] === $Key || (!isset($_GET['order_by']) && $Key === 'Time')) {
                                                                    echo ' selected="selected"';
                                                                } ?>><?= $Cur ?></option>
                                <?      } ?>
                            </select>
                            <select name="order_way" class="ft_order_way">
                                <? foreach ($WayVals as $Key => $Cur) { ?>
                                    <option value="<?= $Key ?>" <? if (isset($_GET['order_way']) && $_GET['order_way'] === $Key || (!isset($_GET['order_way']) && $Key === 'Descending')) {
                                                                    echo ' selected="selected"';
                                                                } ?>><?= $Cur ?></option>
                                <?      } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="center">
                            <input type="submit" value="<?= Lang::get('collages', 'search') ?>" />
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    <?  } /* if (!$BookmarkView) */ ?>
    <div class="linkbox">
        <?
        if (!$BookmarkView) {
            if (check_perms('site_collages_create')) {
        ?>
                <a href="collages.php?action=new" class="brackets"><?= Lang::get('collages', 'create_collages') ?></a>
                <?
            }
            if (check_perms('site_collages_personal')) {

                $DB->query("
				SELECT ID
				FROM collages
				WHERE UserID = '$LoggedUser[ID]'
					AND CategoryID = '0'
					AND Deleted = '0'");
                $CollageCount = $DB->record_count();

                if ($CollageCount === 1) {
                    list($CollageID) = $DB->next_record();
                ?>
                    <a href="collages.php?id=<?= $CollageID ?>" class="brackets"><?= Lang::get('collages', 'personal_collage') ?></a>
                <?          } elseif ($CollageCount > 1) { ?>
                    <a href="collages.php?action=mine" class="brackets"><?= Lang::get('collages', 'personal_collages') ?></a>
                <?
                }
            }
            if (check_perms('site_collages_subscribe')) {
                ?>
                <a href="userhistory.php?action=subscribed_collages" class="brackets"><?= Lang::get('collages', 'subscribed_collages') ?></a>
            <?      } ?>
            <a href="bookmarks.php?type=collages" class="brackets"><?= Lang::get('collages', 'bookmarks_collages') ?></a>
            <? if (check_perms('site_collages_recover')) { ?>
                <a href="collages.php?action=recover" class="brackets"><?= Lang::get('collages', 'recover_collages') ?></a>
            <?
            }
            if (check_perms('site_collages_create') || check_perms('site_collages_personal') || check_perms('site_collages_recover')) {
            ?>
                <br />
            <?      } ?>
            <a href="collages.php?userid=<?= $LoggedUser['ID'] ?>" class="brackets"><?= Lang::get('collages', 'start_collages') ?></a>
            <a href="collages.php?userid=<?= $LoggedUser['ID'] ?>&amp;contrib=1" class="brackets"><?= Lang::get('collages', 'contributed_collages') ?></a>
            <a href="random.php?action=collage" class="brackets"><?= Lang::get('collages', 'random_collages') ?></a>
            <br /><br />
        <?  } else { ?>
            <a href="bookmarks.php?type=torrents" class="brackets"><?= Lang::get('global', 'torrents') ?></a>
            <a href="bookmarks.php?type=artists" class="brackets"><?= Lang::get('global', 'artists') ?></a>
            <a href="bookmarks.php?type=collages" class="brackets"><?= Lang::get('collages', 'collage') ?></a>
            <a href="bookmarks.php?type=requests" class="brackets"><?= Lang::get('global', 'requests') ?></a>
            <br /><br />
        <?
        }
        $Pages = Format::get_pages($Page, $NumResults, COLLAGES_PER_PAGE, 9);
        echo $Pages;
        ?>
    </div>
    <? if (count($Collages) === 0) { ?>
        <div class="box pad" align="center">
            <? if ($BookmarkView) { ?>
                <h2><?= Lang::get('collages', 'result_1') ?></h2>
            <?      } else { ?>
                <h2><?= Lang::get('collages', 'result_2') ?></h2>
                <p><?= Lang::get('collages', 'result_3') ?></p>
            <?      } ?>
        </div>
        <!--box-->
</div>
<!--content-->
<? View::show_footer();
        die();
    }
?>
<div class="table_container border">
    <table width="100%" class="collage_table m_table">
        <tr class="colhead">
            <td class="m_th_left"><?= Lang::get('collages', 'category') ?></td>
            <td><?= Lang::get('collages', 'collage') ?></td>
            <td class="m_th_right"><?= Lang::get('global', 'torrents') ?></td>
            <td class="m_th_right"><?= Lang::get('collages', 'subscribers') ?></td>
            <td><?= Lang::get('collages', 'updated') ?></td>
            <td><?= Lang::get('collages', 'author') ?></td>
        </tr>
        <?
        $Row = 'a'; // For the pretty colours
        foreach ($Collages as $Collage) {
            list($ID, $Name, $NumTorrents, $TagList, $CategoryID, $UserID, $Subscribers, $Updated) = $Collage;
            $Row = $Row === 'a' ? 'b' : 'a';
            $TorrentTags = new Tags($TagList);

            //Print results
        ?>
            <tr class="row<?= $Row ?><?= ($BookmarkView) ? " bookmark_$ID" : ''; ?>">
                <td class="td_collage_category">
                    <a href="collages.php?action=search&amp;cats[<?= (int)$CategoryID ?>]=1"><?= Lang::get('collages', 'collagecats')[(int)$CategoryID] ?></a>
                </td>
                <td class="td_info">
                    <a href="collages.php?id=<?= $ID ?>"><?= $Name ?></a>
                    <? if ($BookmarkView) { ?>
                        <span style="float: right;">
                            <a href="#" onclick="Unbookmark('collage', <?= $ID ?>, ''); return false;" class="brackets"><?= Lang::get('global', 'remove_bookmark') ?></a>
                        </span>
                    <?  } ?>
                    <div class="tags"><?= $TorrentTags->format('collages.php?action=search&amp;tags=') ?></div>
                </td>
                <td class="td_torrent_count m_td_right number_column"><?= number_format((int)$NumTorrents) ?></td>
                <td class="td_subscribers m_td_right number_column"><?= number_format((int)$Subscribers) ?></td>
                <td class="td_updated nobr"><?= time_diff($Updated) ?></td>
                <td class="td_author"><?= Users::format_username($UserID, false, false, false) ?></td>
            </tr>
        <?
        }
        ?>
    </table>
</div>
<div class="linkbox"><?= $Pages ?></div>
</div>
<? View::show_footer(); ?>