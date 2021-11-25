<?
/*
User collage subscription page
*/
if (!check_perms('site_collages_subscribe')) {
    error(403);
}

View::show_header(Lang::get('userhistory', 'subscribed_collages'), 'browse');

$ShowAll = !empty($_GET['showall']);

if (!$ShowAll) {
    $sql = "
		SELECT
			c.ID,
			c.Name,
			c.NumTorrents,
			s.LastVisit
		FROM collages AS c
			JOIN users_collage_subs AS s ON s.CollageID = c.ID
			JOIN collages_torrents AS ct ON ct.CollageID = c.ID
		WHERE s.UserID = $LoggedUser[ID] AND c.Deleted = '0'
			AND ct.AddedOn > s.LastVisit
		GROUP BY c.ID";
} else {
    $sql = "
		SELECT
			c.ID,
			c.Name,
			c.NumTorrents,
			s.LastVisit
		FROM collages AS c
			JOIN users_collage_subs AS s ON s.CollageID = c.ID
			LEFT JOIN collages_torrents AS ct ON ct.CollageID = c.ID
		WHERE s.UserID = $LoggedUser[ID] AND c.Deleted = '0'
		GROUP BY c.ID";
}

$DB->query($sql);
$NumResults = $DB->record_count();
$CollageSubs = $DB->to_array();
?>
<div class="thin">
    <div class="header">
        <h2><?= Lang::get('userhistory', 'subscribed_collages') ?><?= ($ShowAll ? '' : Lang::get('userhistory', 'with_new_additions')) ?></h2>

        <div class="linkbox">
            <?
            if ($ShowAll) {
            ?>
                <br /><br />
                <a href="userhistory.php?action=subscribed_collages&amp;showall=0" class="brackets"><?= Lang::get('userhistory', 'only_display_collages_with_new_additions') ?></a>
            <?
            } else {
            ?>
                <br /><br />
                <a href="userhistory.php?action=subscribed_collages&amp;showall=1" class="brackets"><?= Lang::get('userhistory', 'show_all_subscribed_collages') ?></a>
            <?
            }
            ?>
            <a href="userhistory.php?action=catchup_collages&amp;auth=<?= $LoggedUser['AuthKey'] ?>" class="brackets"><?= Lang::get('userhistory', 'catch_up') ?></a>
        </div>
    </div>
    <?
    if (!$NumResults) {
    ?>
        <div class="center">
            <?= Lang::get('userhistory', 'no_subscribed_collages') ?><?= ($ShowAll ? '' : Lang::get('userhistory', 'with_new_additions')) ?>
        </div>
        <?
    } else {
        $HideGroup = '';
        $ActionTitle = 'Hide';
        $ActionURL = 'hide';
        $ShowGroups = 0;

        foreach ($CollageSubs as $Collage) {
            unset($TorrentTable);

            list($CollageID, $CollageName, $CollageSize, $LastVisit) = $Collage;
            $RS = $DB->query("
			SELECT GroupID
			FROM collages_torrents
			WHERE CollageID = $CollageID
				AND AddedOn > '" . db_string($LastVisit) . "'
			ORDER BY AddedOn");
            $NewTorrentCount = $DB->record_count();

            $GroupIDs = $DB->collect('GroupID', false);
            if (count($GroupIDs) > 0) {
                $TorrentList = Torrents::get_groups($GroupIDs);
            } else {
                $TorrentList = array();
            }

            $Artists = Artists::get_artists($GroupIDs);
            $Number = 0;

            foreach ($GroupIDs as $GroupID) {
                if (!isset($TorrentList[$GroupID])) {
                    continue;
                }
                $Group = $TorrentList[$GroupID];
                extract(Torrents::array_group($Group));

                $DisplayName = '';

                $TorrentTags = new Tags($TagList);

                if (!empty($ExtendedArtists[1]) || !empty($ExtendedArtists[4]) || !empty($ExtendedArtists[5]) || !empty($ExtendedArtists[6])) {
                    unset($ExtendedArtists[2]);
                    unset($ExtendedArtists[3]);
                    $DisplayName .= Artists::display_artists($ExtendedArtists);
                } elseif (count($Artists) > 0) {
                    $DisplayName .= Artists::display_artists(array('1' => $Artists));
                }
                $DisplayName .= "<a href=\"torrents.php?id=$GroupID\" class=\"tooltip\" title=\"" . Lang::get('global', 'view_torrent_group') . "\" dir=\"ltr\">$GroupName</a>";
                if ($GroupYear > 0) {
                    $DisplayName = "$DisplayName [$GroupYear]";
                }
                if ($GroupVanityHouse) {
                    $DisplayName .= ' [<abbr class="tooltip" title="' . Lang::get('global', 'this_is_vh') . '">VH</abbr>]';
                }

                $SnatchedGroupClass = $GroupFlags['IsSnatched'] ? ' snatched_group' : '';

                // Start an output buffer, so we can store this output in $TorrentTable
                ob_start();
                if (count($Torrents) > 1 || $GroupCategoryID == 1) {
        ?>
                    <tr class="group discog<?= $SnatchedGroupClass ?>" id="group_<?= $CollageID ?><?= $GroupID ?>">
                        <td class="center td_collapse">
                            <div id="showimg_<?= $CollageID ?><?= $GroupID ?>" class="<?= ($ShowGroups ? 'hide' : 'show') ?>_torrents">
                                <a href="#" class="tooltip show_torrents_link" onclick="toggle_group(<?= $CollageID ?><?= $GroupID ?>, this, event);" title="<?= Lang::get('global', 'expand_this_group_title') ?>"></a>
                            </div>
                        </td>
                        <td colspan="5" class="big_info">
                            <? if ($LoggedUser['CoverArt']) { ?>
                                <div class="group_image float_left clear">
                                    <? ImageTools::cover_thumb($WikiImage, $GroupCategoryID) ?>
                                </div>
                            <? } ?>
                            <div class="group_info clear">
                                <strong><?= $DisplayName ?></strong>
                                <div class="tags"><?= $TorrentTags->format() ?></tags>
                                </div>
                        </td>
                    </tr>
                    <?
                    $LastRemasterYear = '-';
                    $LastRemasterTitle = '';
                    $LastRemasterRecordLabel = '';
                    $LastRemasterCatalogueNumber = '';
                    $LastMedia = '';

                    $EditionID = 0;
                    unset($FirstUnknown);

                    foreach ($Torrents as $TorrentID => $Torrent) {

                        if ($Torrent['Remastered'] && !$Torrent['RemasterYear']) {
                            $FirstUnknown = !isset($FirstUnknown);
                        }
                        $SnatchedTorrentClass = $Torrent['IsSnatched'] ? ' snatched_torrent' : '';

                        if (
                            $Torrent['RemasterTitle'] != $LastRemasterTitle
                            || $Torrent['RemasterYear'] != $LastRemasterYear
                            || $Torrent['RemasterRecordLabel'] != $LastRemasterRecordLabel
                            || $Torrent['RemasterCatalogueNumber'] != $LastRemasterCatalogueNumber
                            || $FirstUnknown
                            || $Torrent['Media'] != $LastMedia
                        ) {
                            $EditionID++;
                    ?>
                            <tr class="group_torrent groupid_<?= $CollageID . $GroupID ?> edition<?= $SnatchedGroupClass ?> hidden">
                                <td colspan="6" class="edition_info"><strong><a href="#" onclick="torrentTable.toggleEdition(event, <?= $CollageID ?><?= $GroupID ?>, <?= $EditionID ?>);" class="tooltip" title="<?= Lang::get('global', 'collapse_this_edition_title') ?>">&minus;</a> <?= Torrents::edition_string($Torrent, $Group) ?></strong></td>
                            </tr>
                        <?
                        }
                        $LastRemasterTitle = $Torrent['RemasterTitle'];
                        $LastRemasterYear = $Torrent['RemasterYear'];
                        $LastRemasterRecordLabel = $Torrent['RemasterRecordLabel'];
                        $LastRemasterCatalogueNumber = $Torrent['RemasterCatalogueNumber'];
                        $LastMedia = $Torrent['Media'];
                        ?>
                        <tr class="group_torrent groupid_<?= $CollageID . $GroupID ?> edition_<?= $EditionID ?> hidden<?= $SnatchedTorrentClass . $SnatchedGroupClass ?>">
                            <td colspan="2">
                                <span>
                                    [ <a href="torrents.php?action=download&amp;id=<?= $TorrentID ?>&amp;authkey=<?= $LoggedUser['AuthKey'] ?>&amp;torrent_pass=<?= $LoggedUser['torrent_pass'] ?>" title="<?= Lang::get('global', 'download') ?>" class="brackets tooltip">DL</a>
                                    <? if (Torrents::can_use_token($Torrent)) { ?>
                                        | <a href="torrents.php?action=download&id=<?= $TorrentID ?>&authkey=<?= $LoggedUser['AuthKey'] ?>&amp;torrent_pass=<?= $LoggedUser['torrent_pass'] ?>&amp;usetoken=1" title="<?= Lang::get('global', 'use_fl_tokens') ?>" class="tooltip" onclick="return confirm(<?= FL_confirmation_msg($Torrent['Seeders'], $Torrent['Size']) ?>);">FL</a>
                                    <? } ?>
                                    ]
                                </span>
                                &nbsp;&nbsp;&raquo;&nbsp; <a class="torrent_specs" href="torrents.php?id=<?= $GroupID ?>&amp;torrentid=<?= $TorrentID ?>"><?= Torrents::torrent_info($Torrent) ?></a>
                            </td>
                            <td class="number_column nobr"><?= Format::get_size($Torrent['Size']) ?></td>
                            <td class="number_column"><?= number_format($Torrent['Snatched']) ?></td>
                            <td class="number_column<?= ($Torrent['Seeders'] == 0) ? ' r00' : '' ?>"><?= number_format($Torrent['Seeders']) ?></td>
                            <td class="number_column"><?= number_format($Torrent['Leechers']) ?></td>
                        </tr>
                    <?
                    }
                } else {
                    // Viewing a type that does not require grouping

                    list($TorrentID, $Torrent) = each($Torrents);

                    $DisplayName = "<a href=\"torrents.php?id=$GroupID\" class=\"tooltip\" title=\"" . Lang::get('global', 'view_torrent_group') . "\" dir=\"ltr\">$GroupName</a>";

                    if ($Torrent['IsSnatched']) {
                        $DisplayName .= ' ' . Format::torrent_label('Snatched!');
                    }
                    if (!empty($Torrent['FreeTorrent'])) {
                        $DisplayName .= ' ' . Format::torrent_label('Freeleech!');
                    }
                    $SnatchedTorrentClass = $Torrent['IsSnatched'] ? ' snatched_torrent' : '';
                    ?>
                    <tr class="torrent<?= $SnatchedTorrentClass ?>" id="group_<?= $CollageID . $GroupID ?>">
                        <td></td>
                        <td class="td_collage_category center">
                            <div title="<?= $TorrentTags->title() ?>" class="tooltip <?= Format::css_category($GroupCategoryID) ?> <?= $TorrentTags->css_name() ?>">
                            </div>
                        </td>
                        <td class="td_info big_info">
                            <? if ($LoggedUser['CoverArt']) { ?>
                                <div class="group_image float_left clear">
                                    <? ImageTools::cover_thumb($WikiImage, $GroupCategoryID) ?>
                                </div>
                            <? } ?>
                            <div class="group_info clear">
                                <span>
                                    [ <a href="torrents.php?action=download&amp;id=<?= $TorrentID ?>&amp;authkey=<?= $LoggedUser['AuthKey'] ?>&amp;torrent_pass=<?= $LoggedUser['torrent_pass'] ?>" class="tooltip" title="<?= Lang::get('global', 'download') ?>">DL</a>
                                    <? if (Torrents::can_use_token($Torrent)) { ?>
                                        | <a href="torrents.php?action=download&id=<?= $TorrentID ?>&authkey=<?= $LoggedUser['AuthKey'] ?>&amp;torrent_pass=<?= $LoggedUser['torrent_pass'] ?>&amp;usetoken=1" title="<?= Lang::get('global', 'use_fl_tokens') ?>" class="tooltip" onclick="return confirm(<?= FL_confirmation_msg($Torrent['Seeders'], $Torrent['Size']) ?>);">FL</a>
                                    <? } ?>
                                    | <a href="reportsv2.php?action=report&amp;id=<?= $TorrentID ?>" class="tooltip" title="<?= Lang::get('userhistory', 'report') ?>">RP</a> ]
                                </span>
                                <strong><?= $DisplayName ?></strong>
                                <div class="tags"><?= $TorrentTags->format() ?></div>
                            </div>
                        </td>
                        <td class="td_size number_column nobr"><?= Format::get_size($Torrent['Size']) ?></td>
                        <td class="td_snatched m_td_right number_column"><?= number_format($Torrent['Snatched']) ?></td>
                        <td class="td_seeders m_td_right number_column<?= ($Torrent['Seeders'] == 0) ? ' r00' : '' ?>"><?= number_format($Torrent['Seeders']) ?></td>
                        <td class="td_leechers m_td_right number_column"><?= number_format($Torrent['Leechers']) ?></td>
                    </tr>
            <?
                }
                $TorrentTable .= ob_get_clean();
            } ?>
            <!-- I hate that proton is making me do it like this -->
            <!--<div class="head colhead_dark" style="margin-top: 8px;">-->
            <table style="margin-top: 8px;" class="subscribed_collages_table">
                <tr class="colhead_dark">
                    <td>
                        <span style="float: left;">
                            <strong><a href="collage.php?id=<?= $CollageID ?>"><?= $CollageName ?></a></strong> (<?= $NewTorrentCount ?> <?= Lang::get('userhistory', 'new_torrent') ?><?= ($NewTorrentCount == 1 ? '' : Lang::get('userhistory', 's')) ?>)
                        </span>&nbsp;
                        <span style="float: right;">
                            <a href="#" onclick="$('#discog_table_<?= $CollageID ?>').gtoggle(); this.innerHTML = (this.innerHTML == 'Hide' ? 'Show' : 'Hide'); return false;" class="brackets"><?= ($ShowAll ? Lang::get('userhistory', 'show') : Lang::get('userhistory', 'hide')) ?></a>&nbsp;&nbsp;&nbsp;<a href="userhistory.php?action=catchup_collages&amp;auth=<?= $LoggedUser['AuthKey'] ?>&amp;collageid=<?= $CollageID ?>" class="brackets"><?= Lang::get('userhistory', 'catch_up') ?></a>&nbsp;&nbsp;&nbsp;<a href="#" onclick="CollageSubscribe(<?= $CollageID ?>); return false;" id="subscribelink<?= $CollageID ?>" class="brackets"><?= Lang::get('global', 'unsubscribe') ?></a>
                        </span>
                    </td>
                </tr>
            </table>
            <!--</div>-->
            <table class="torrent_table<?= $ShowAll ? ' hidden' : '' ?> m_table" id="discog_table_<?= $CollageID ?>">
                <tr class="colhead">
                    <td width="1%">
                        <!-- expand/collapse -->
                    </td>
                    <td class="m_th_left" width="70%"><strong><?= Lang::get('global', 'torrents') ?></strong></td>
                    <td><i class="fa fa-hdd tooltip" aria-hidden="true"></i></td>
                    <td class="number_column sign snatches"><i class="fa fa-check tooltip" aria-hidden="true" alt="Snatches"></i>
                        <!-- <img src="static/styles/<?= $LoggedUser['StyleName'] ?>/images/snatched.png" class="tooltip" alt="Snatches" title="Snatches" /> -->
                    </td>
                    <td class="number_column sign seeders"><i class="fa fa-upload tooltip" aria-hidden="true" alt="Seeders"></i>
                        <!-- <img src="static/styles/<?= $LoggedUser['StyleName'] ?>/images/seeders.png" class="tooltip" alt="Seeders" title="Seeders" /> -->
                    </td>
                    <td class="number_column sign leechers"><i class="fa fa-download tooltip" aria-hidden="true" alt="Leechers"></i>
                        <!-- <img src="static/styles/<?= $LoggedUser['StyleName'] ?>/images/leechers.png" class="tooltip" alt="Leechers" title="Leechers" /> -->
                    </td>
                </tr>
                <?= $TorrentTable ?>
            </table>
    <?
        } // foreach ()
    } // else -- if (empty($NumResults))
    ?>
</div>
<?

View::show_footer();

?>