<?php

/*
 * Yeah, that's right, edit and new are the same place again.
 * It makes the page uglier to read but ultimately better as the alternative means
 * Designed by GPW.
 * maintaining 2 copies of almost identical files.
 */


$NewRequest = $_GET['action'] === 'new';

$RequestTaxPercent = ($RequestTax * 100);

if (!$NewRequest) {
    $RequestID = $_GET['id'];
    if (!is_number($RequestID)) {
        error(404);
    }
}


if ($NewRequest && ($LoggedUser['BytesUploaded'] < 250 * 1024 * 1024 || !check_perms('site_submit_requests'))) {
    error(Lang::get('requests', 'you_do_not_have_enough_uploaded'));
}

if (!$NewRequest) {
    if (empty($ReturnEdit)) {

        $Request = Requests::get_request($RequestID);
        if ($Request === false) {
            error(404);
        }

        // Define these variables to simplify _GET['groupid'] requests later on
        $CategoryID = $Request['CategoryID'];
        $Title = $Request['Title'];
        $Year = $Request['Year'];
        $Image = $Request['Image'];
        $ReleaseType = $Request['ReleaseType'];
        $GroupID = $Request['GroupID'];
        $IMDBID = $Request['IMDBID'];
        $Subtitle = $Request['Subtitle'];
        $SourceTorrent = $Request['SourceTorrent'];
        $PurchasableAt = $Request['PurchasableAt'];


        $VoteArray = Requests::get_votes_array($RequestID);
        $VoteCount = count($VoteArray['Voters']);

        $IsFilled = !empty($Request['TorrentID']);
        $CategoryName = $Categories[$CategoryID - 1];

        $ProjectCanEdit = (check_perms('project_team') && !$IsFilled && ($CategoryID === '0' || ($CategoryName === 'Music' && $Request['Year'] === '0')));
        $CanEdit = ((!$IsFilled && $LoggedUser['ID'] === $Request['UserID'] && $VoteCount < 2) || $ProjectCanEdit || check_perms('site_moderate_requests'));

        if (!$CanEdit) {
            error(403);
        }

        $ArtistForm = Requests::get_artists($RequestID);

        $CodecArray = array();
        if ($Request['CodecList'] == 'Any') {
            $CodecArray = array_keys($Codecs);
        } else {
            $CodecArray = array_keys(array_intersect($Codecs, explode('|', $Request['CodecList'])));
        }

        $ResolutionArray = array();
        if ($Request['ResolutionList'] == 'Any') {
            $ResolutionArray = array_keys($Resolutions);
        } else {
            foreach ($Resolutions as $Key => $Val) {
                if (strpos($Request['ResolutionList'], $Val) !== false) {
                    $ResolutionArray[] = $Key;
                }
            }
        }

        $ContainerArray = array();
        if ($Request['ContainerList'] == 'Any') {
            $MediaArray = array_keys($Containers);
        } else {
            $ContainerTemp = explode('|', $Request['ContainerList']);
            foreach ($Containers as $Key => $Val) {
                if (in_array($Val, $ContainerTemp)) {
                    $ContainerArray[] = $Key;
                }
            }
        }

        $SourceArray = array();
        if ($Request['SourceList'] == 'Any') {
            $SourceArray = array_keys($Sources);
        } else {
            $SourceTemp = explode('|', $Request['SourceList']);
            foreach ($Sources as $Key => $Val) {
                if (in_array($Val, $SourceTemp)) {
                    $SourceArray[] = $Key;
                }
            }
        }


        $Tags = implode(', ', $Request['Tags']);
    }
}

if ($NewRequest && !empty($_GET['artistid']) && is_number($_GET['artistid'])) {
    $DB->query("
		SELECT Name
		FROM artists_group
		WHERE artistid = " . $_GET['artistid'] . "
		LIMIT 1");
    list($ArtistName) = $DB->next_record();
    $ArtistForm = array(
        1 => array(array('name' => trim($ArtistName))),
        2 => array(),
        3 => array()
    );
    // TODO by qwerty IMDBID autofill 兼容情况
} elseif ($NewRequest && !empty($_GET['groupid']) && is_number($_GET['groupid'])) {
    $ArtistForm = Artists::get_artist($_GET['groupid']);
    $DB->query("
		SELECT
			tg.Name,
			tg.Year,
			tg.SubName,
			tg.IMDBID,
			tg.ReleaseType,
			tg.WikiImage,
			GROUP_CONCAT(t.Name SEPARATOR ', '),
			tg.CategoryID
		FROM torrents_group AS tg
			JOIN torrents_tags AS tt ON tt.GroupID = tg.ID
			JOIN tags AS t ON t.ID = tt.TagID
		WHERE tg.ID = " . $_GET['groupid']);
    if (list($Title, $Year, $SubName, $IMDBID, $ReleaseType, $Image, $Tags, $CategoryID) = $DB->next_record()) {
        $GroupID = trim($_REQUEST['groupid']);
        $Disabled = ' readonly';
        $DisabledFlag = true;
        $Subtitle = $SubName;
    }
}

View::show_header(($NewRequest ? Lang::get('requests', 'new_create') : Lang::get('requests', 'new_edit')), 'requests,form_validate');
?>
<div class="thin">
    <div class="header">
        <h2><?= ($NewRequest ? Lang::get('requests', 'new_create') : Lang::get('requests', 'new_edit')) ?></h2>
    </div>

    <div class="box pad">
        <form action="" method="post" class="request_form" id="request_form" onsubmit="Calculate();">
            <div>
                <? if (!$NewRequest) { ?>
                    <input type="hidden" name="requestid" value="<?= $RequestID ?>" />
                <?  } ?>
                <input type="hidden" name="auth" value="<?= $LoggedUser['AuthKey'] ?>" />
                <input type="hidden" name="action" value="<?= ($NewRequest ? 'takenew' : 'takeedit') ?>" />
            </div>

            <table class="layout">
                <tr>
                    <td colspan="2" class="center"><?= Lang::get('requests', 'new_rules') ?></td>
                </tr>
                <? if ($NewRequest || $CanEdit) { ?>
                    <tr>
                        <td class="label">
                            <?= Lang::get('upload', 'type') ?>:
                        </td>
                        <td>
                            <select id="categories" name="type" onchange="Categories();">
                                <? foreach (Misc::display_array($Categories) as $Cat) { ?>
                                    <option value="<?= $Cat ?>" <?= (!empty($CategoryName) && ($CategoryName === $Cat) ? ' selected="selected"' : '') ?>><?= $Cat ?></option>
                                <?      } ?>
                            </select>
                        </td>
                    </tr>
                    <tr id="imdb_tr">
                        <td class="label">IMDb:</td>
                        <td id="imdbfield">
                            <input type="text" id="imdb" name="imdb" size="45" value="<?= $IMDBID ?>" <?= $Disabled ?>>
                            <input <?= $DisabledFlag ? 'disabled' : '' ?> onclick="MovieAutofill()" type="button" value="<?= Lang::get('upload', 'movie_fill') ?>" id="imdb_button">
                            <input type="checkbox" name="no_imdb_link" id="no_imdb_link"><label for="no_imdb_link"><?= Lang::get('upload', 'no_imdb_link') ?></label>
                            <div class="imdb error-message important-text"><?= Lang::get('upload', 'imdb_empty_warning') ?></div>
                        </td>
                    </tr>
                    <tr id="artist_tr">
                        <td class="label"><?= Lang::get('global', 'artist') ?>:</td>
                        <td id="artistfields">
                            <!-- <p id="vawarning"><?= Lang::get('requests', 'artist_note') ?></p> -->
                            <?
                            if (!empty($ArtistForm)) {
                                $First = true;
                                $cnt = 0;
                                foreach ($ArtistForm[1] as $Artist) {
                            ?>
                                    <input type="hidden" id="artist_id" name="artist_ids[]" value="<?= display_str($Artist['imdbid']) ?>" size="45" />
                                    <input type="text" id="artist_<?= $cnt ?>" <?= $Disabled ?> name="artists[]" <? Users::has_autocomplete_enabled('other'); ?> size="45" value="<?= display_str($Artist['name']) ?>" />
                                    <input type="text" id="artist_chinese" name="artists_chinese[]" size="25" value="<?= display_str($Artist['cname']) ?>" placeholder="<?= Lang::get('upload', 'chinese_name') ?>" <?
                                                                                                                                                                                                                    Users::has_autocomplete_enabled('other'); ?> />
                                    <select id="importance" name="importance[]">
                                        <option value="1" <?= ($Importance == '1' ? ' selected="selected"' : '') ?>><?= Lang::get('upload', 'director') ?></option>
                                    </select>
                                    <? if ($First) { ?><a href="#" onclick="AddArtistField(); return false;" class="brackets">+</a> <a href="#" onclick="RemoveArtistField(); return false;" class="brackets">&minus;</a><? }
                                                                                                                                                                                                                        $First = false; ?>
                                    <br />
                                <?
                                    $cnt++;
                                }
                            } else {

                                ?>
                                <input type="hidden" id="artist_id" name="artist_ids[]" size="45" />
                                <input type="text" id="artist" name="artists[]" <? Users::has_autocomplete_enabled('other'); ?> size="45" />
                                <input type="text" id="artist_chinese" name="artists_chinese[]" size="25" placeholder="<?= Lang::get('upload', 'chinese_name') ?>" <?
                                                                                                                                                                    Users::has_autocomplete_enabled('other'); ?> />
                                <select id="importance" name="importance[]">
                                    <option value="1"><?= Lang::get('upload', 'director') ?></option>
                                </select>
                                <a href="#" onclick="AddArtistField(); return false;" class="brackets">+</a> <a href="#" onclick="RemoveArtistField(); return false;" class="brackets">&minus;</a>
                            <?
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="label"><?= Lang::get('upload', 'movie_title') ?>:</td>
                        <td>
                            <input type="text" id="title" name="title" <?= $Disabled ?> size="45" value="<?= (!empty($Title) ? $Title : '') ?>" />
                        </td>
                    </tr>
                    <tr>
                        <td class="label"><?= Lang::get('upload', 'chinese_title') ?>:</td>
                        <td>
                            <input type="text" id="subtitle" name="subtitle" <?= $Disabled ?> size="45" value="<?= (!empty($Subtitle) ? $Subtitle : '') ?>" />
                        </td>
                    </tr>
                <?  } ?>
                <tr id="year_tr">
                    <td class="label"><?= Lang::get('requests', 'year') ?>:</td>
                    <td>
                        <input type="text" id="year" name="year" <?= $Disabled ?> size="5" value="<?= (!empty($Year) ? $Year : '') ?>" />
                    </td>
                </tr>
                <? if ($NewRequest || $CanEdit) { ?>
                    <tr id="image_tr">
                        <td class="label"><?= Lang::get('requests', 'image') ?>:</td>
                        <td>
                            <input type="text" id="image" name="image" <?= $Disabled ?> size="45" value="<?= (!empty($Image) ? $Image : '') ?>" />
                        </td>
                    </tr>
                <?  } ?>
                <tr>
                    <td class="label"><?= Lang::get('requests', 'tags') ?>:</td>
                    <td>
                        <?
                        $GenreTags = $Cache->get_value('genre_tags');
                        if (!$GenreTags) {
                            $DB->query('
			SELECT Name
			FROM tags
			WHERE TagType = \'genre\'
			ORDER BY Name');
                            $GenreTags = $DB->collect('Name');
                            $Cache->cache_value('genre_tags', $GenreTags, 3600 * 6);
                        }
                        ?>
                        <select id="genre_tags" name="genre_tags" onchange="add_tag(); return false;">
                            <option>---</option>
                            <? foreach (Misc::display_array($GenreTags) as $Genre) { ?>
                                <option value="<?= $Genre ?>"><?= $Genre ?></option>
                            <?  } ?>
                        </select>
                        <input <?= $Disabled ?> type="text" id="tags" name="tags" size="45" value="<?= (!empty($Tags) ? display_str($Tags) : '') ?>" <? Users::has_autocomplete_enabled('other'); ?> />
                        <br />
                        <?= Lang::get('requests', 'tags_note') ?>
                    </td>
                </tr>
                <? if ($NewRequest || $CanEdit) { ?>
                    <tr id="releasetypes_tr">
                        <td class="label"><?= Lang::get('requests', 'release_list') ?>:</td>
                        <td>
                            <select id="releasetype" name="releasetype">
                                <?
                                foreach ($ReleaseTypes as $Key => $Val) {
                                    //echo '<h1>'.$ReleaseType.'</h1>'; die();
                                ?> <option value="<?= $Key ?>" <?= !empty($ReleaseType) ? ($Key == $ReleaseType ? ' selected="selected"' : ($DisabledFlag ? 'disabled' : '')) : '' ?>><?= Lang::get('torrents', 'release_types')[$Key] ?></option>
                                <?
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr id="sources_tr">
                        <td class="label"><?= Lang::get('requests', 'acceptable_sources') ?>:</td>
                        <td>
                            <input type="checkbox" name="all_sources" id="toggle_sources" onchange="Toggle('sources', <?= ($NewRequest ? 1 : 0) ?>);" <?= !empty($SourceArray) && (count($SourceArray) === count($Sources)) ? ' checked="checked"' : ''; ?> /><label for="toggle_sources"> <?= Lang::get('requests', 'all') ?></label>
                            <? foreach ($Sources as $Key => $Val) {
                                if ($Key % 8 === 0) {
                                    echo '<br />';
                                } ?>
                                <input type="checkbox" name="sources[]" value="<?= $Key ?>" onchange="if (!this.checked) { $('#toggle_sources').raw().checked = false; }" id="source_<?= $Key ?>" <?= (!empty($SourceArray) && in_array($Key, $SourceArray) ? ' checked="checked"' : '') ?> /><label for="source_<?= $Key ?>"> <?= $Val ?></label>
                            <?      } ?>
                        </td>
                    </tr>
                    <tr id="codecs_tr">
                        <td class="label"><?= Lang::get('requests', 'acceptable_codecs') ?>:</td>
                        <td>
                            <input type="checkbox" name="all_codecs" id="toggle_codecs" onchange="Toggle('codecs', <?= ($NewRequest ? 1 : 0) ?>);" <?= (!empty($CodecArray) && (count($CodecArray) === count($Codecs)) ? ' checked="checked"' : '') ?> /><label for="toggle_codecs"> <?= Lang::get('requests', 'all') ?></label>
                            <? foreach ($Codecs as $Key => $Val) {
                                if ($Key % 8 === 0) {
                                    echo '<br />';
                                } ?>
                                <input type="checkbox" name="codecs[]" value="<?= $Key ?>" id="codec_<?= $Key ?>" <?= (!empty($CodecArray) && in_array($Key, $COdecArray) ? ' checked="checked" ' : '') ?> onchange="if (!this.checked) { $('#toggle_codecs').raw().checked = false; }" /><label for="codec_<?= $Key ?>"> <?= $Val ?></label>
                            <?      } ?>
                        </td>
                    </tr>
                    <tr id="containers_tr">
                        <td class="label"><?= Lang::get('requests', 'acceptable_containers') ?>:</td>
                        <td>
                            <input type="checkbox" name="all_containers" id="toggle_containers" onchange="Toggle('containers', <?= ($NewRequest ? 1 : 0) ?>);" <?= (!empty($ContainerArray) && (count($ContainerArray) === count($Containers)) ? ' checked="checked"' : '') ?> /><label for="toggle_containers"> <?= Lang::get('requests', 'all') ?></label>
                            <? foreach ($Containers as $Key => $Val) {
                                if ($Key % 8 === 0) {
                                    echo '<br />';
                                } ?>
                                <input type="checkbox" name="containers[]" value="<?= $Key ?>" id="container_<?= $Key ?>" <?= (!empty($ContainerArray) && in_array($Key, $ContainerArray) ? ' checked="checked" ' : '') ?> onchange="if (!this.checked) { $('#toggle_containers').raw().checked = false; }" /><label for="container_<?= $Key ?>"> <?= $Val ?></label>
                            <?      } ?>
                        </td>
                    </tr>
                    <tr id="resolutions_tr">
                        <td class="label"><?= Lang::get('requests', 'acceptable_resolutions') ?>:</td>
                        <td>
                            <input type="checkbox" name="all_resolutions" id="toggle_resolutions" onchange="Toggle('resolutions', <?= ($NewRequest ? 1 : 0) ?>);" <?= (!empty($ResolutionArray) && (count($ResolutionArray) === count($Resolutions)) ? ' checked="checked"' : '') ?> /><label for="toggle_resolutions"> <?= Lang::get('requests', 'all') ?></label>
                            <? foreach ($Resolutions as $Key => $Val) {
                                if ($Key % 8 === 0) {
                                    echo '<br />';
                                } ?>
                                <input type="checkbox" name="resolutions[]" value="<?= $Key ?>" id="resolution_<?= $Key ?>" <?= (!empty($ResolutionArray) && in_array($Key, $ResolutionArray) ? ' checked="checked" ' : '') ?> onchange="if (!this.checked) { $('#toggle_resolutions').raw().checked = false; }" /><label for="resolution_<?= $Key ?>"> <?= $Val ?></label>
                            <?      } ?>
                        </td>
                    </tr>
                <?  } ?>
                <tr>
                    <td class="label"><?= Lang::get('requests', 'source_torrent') ?>:</td>
                    <td>
                        <input type="text" name="source_torrent" value="<?= $SourceTorrent ?>" style="width: 70%; min-width: 400px;" placeholder="<?= Lang::get('requests', 'source_torrent_placeholder') ?>">
                    </td>
                </tr>
                <tr>
                    <td class="label"><?= Lang::get('requests', 'purchasable_at') ?>:</td>
                    <td>
                        <input type="text" name="purchasable_at" value="<?= $PurchasableAt ?>" style="width: 70%; min-width: 400px;" placeholder="<?= Lang::get('requests', 'purchasable_at_placeholder') ?>">
                    </td>
                </tr>
                <tr>
                    <td class="label"><?= Lang::get('requests', 'description') ?>:</td>
                    <td>
                        <textarea name="description" cols="70" rows="7"><?= (!empty($Request['Description']) ? $Request['Description'] : '') ?></textarea><?= Lang::get('requests', 'description_note') ?>
                    </td>
                </tr>
                <? if (check_perms('site_moderate_requests')) { ?>
                    <tr>
                        <td class="label"><?= Lang::get('requests', 't_group') ?>:</td>
                        <td>
                            <?= site_url() ?>torrents.php?id=<input type="text" name="groupid" value="<?= $GroupID ?>" size="15" /><br />
                            <?= Lang::get('requests', 't_group_note') ?>
                        </td>
                    </tr>
                <?  } elseif ($GroupID && ($CategoryID == 1)) { ?>
                    <tr>
                        <td class="label"><?= Lang::get('requests', 't_group') ?></td>
                        <td>
                            <a href="torrents.php?id=<?= $GroupID ?>"><?= site_url() ?>torrents.php?id=<?= $GroupID ?></a><br />
                            <?= Lang::get('requests', 'this_request') ?><?= ($NewRequest ? Lang::get('requests', 'will_be') : Lang::get('requests', 'is')) ?><?= Lang::get('requests', 'associated_with_the_above_torrent_group') ?>
                            <? if (!$NewRequest) { ?>
                                <?= Lang::get('requests', 'if_this_is_incorrect_please') ?><a href="reports.php?action=report&amp;type=request&amp;id=<?= $RequestID ?>"><?= Lang::get('requests', 'report_this_request') ?></a><?= Lang::get('requests', 'so_that_staff_can_fix_it') ?>
                            <?      }   ?>
                            <input type="hidden" name="groupid" value="<?= $GroupID ?>" />
                        </td>
                    </tr>
                <?  }
                if ($NewRequest) { ?>
                    <tr id="voting">
                        <td class="label"><?= Lang::get('requests', 'bounty') ?>:</td>
                        <td>
                            <input type="text" id="amount_box" size="8" value="<?= (!empty($Bounty) ? $Bounty : '100') ?>" onchange="Calculate();" />
                            <select id="unit" name="unit" onchange="Calculate();">
                                <option value="mb" <?= (!empty($_POST['unit']) && $_POST['unit'] === 'mb' ? ' selected="selected"' : '') ?>>MB</option>
                                <option value="gb" <?= (!empty($_POST['unit']) && $_POST['unit'] === 'gb' ? ' selected="selected"' : '') ?>>GB</option>
                            </select>
                            <input type="button" value="Preview" onclick="Calculate();" />
                            <?= $RequestTax > 0 ? "<strong>{$RequestTaxPercent}% of this is deducted as tax by the system.</strong>" : '' ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="label"><?= Lang::get('requests', 'pst') ?>:</td>
                        <td>
                            <input type="hidden" id="amount" name="amount" value="<?= (!empty($Bounty) ? $Bounty : '100') ?>" />
                            <input type="hidden" id="current_uploaded" value="<?= $LoggedUser['BytesUploaded'] ?>" />
                            <input type="hidden" id="current_downloaded" value="<?= $LoggedUser['BytesDownloaded'] ?>" />
                            <?= $RequestTax > 0 ? 'Bounty after tax: <strong><span id="bounty_after_tax">90.00 MB</span></strong><br />' : '' ?>
                            <?= Lang::get('requests', 'pst_1') ?><strong> <span id="new_bounty">100.00 MB</span></strong> <?= Lang::get('requests', 'pst_2') ?> <br />
                            <?= Lang::get('requests', 'uploaded') ?>: <span id="new_uploaded"><?= Format::get_size($LoggedUser['BytesUploaded']) ?></span><br />
                            <?= Lang::get('requests', 'ratio') ?>: <span id="new_ratio"><?= Format::get_ratio_html($LoggedUser['BytesUploaded'], $LoggedUser['BytesDownloaded']) ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="center">
                            <input type="submit" id="button" value="Create request" />
                        </td>
                    </tr>
                <?  } else { ?>
                    <tr>
                        <td colspan="2" class="center">
                            <input type="submit" id="button" value="Edit request" />
                        </td>
                    </tr>
                <?  } ?>
            </table>
        </form>
        <script type="text/javascript">
            Categories();
        </script>
    </div>
</div>
<?
View::show_footer();
?>