<?
$TorrentID = $_GET['torrentid'];
if (!$TorrentID || !is_number($TorrentID)) {
    error(404);
}


$DB->query("
	SELECT
		t.UserID,
		t.Time,
		COUNT(x.uid)
	FROM torrents AS t
		LEFT JOIN xbt_snatched AS x ON x.fid = t.ID
	WHERE t.ID = $TorrentID
	GROUP BY t.UserID");

if (!$DB->has_results()) {
    error(Lang::get('torrents', 'torrent_already_deleted'));
}

if ($Cache->get_value('torrent_' . $TorrentID . '_lock')) {
    error(Lang::get('torrents', 'torrent_cannot_be_deleted_because_the_upload_process_is_not_completed_yet'));
}


list($UserID, $Time, $Snatches) = $DB->next_record();


if ($LoggedUser['ID'] != $UserID && !check_perms('torrents_delete')) {
    error(403);
}

if (isset($_SESSION['logged_user']['multi_delete']) && $_SESSION['logged_user']['multi_delete'] >= 3 && !check_perms('torrents_delete_fast')) {
    error(Lang::get('torrents', 'you_have_recently_deleted_3_torrents'));
}

if (time_ago($Time) > 3600 * 24 * 7 && !check_perms('torrents_delete')) { // Should this be torrents_delete or torrents_delete_fast?
    error(Lang::get('torrents', 'you_can_no_longer_delete_this_torrent_as_it_has_been_uploaded_for_over_a_week'));
}

if ($Snatches > 4 && !check_perms('torrents_delete')) { // Should this be torrents_delete or torrents_delete_fast?
    error(Lang::get('torrents', 'you_can_no_longer_delete_this_torrent_as_it_has_been_snatched_by_5_or_more_users'));
}


View::show_header(Lang::get('torrents', 'delete_torrent'), 'reportsv2');
?>
<div class="thin">
    <div class="box box2" id="torrent_delete_reason">
        <div class="head colhead"><?= Lang::get('torrents', 'delete_torrent') ?></div>
        <div class="pad">
            <form class="delete_form" name="torrent" action="torrents.php" method="post">
                <input type="hidden" name="action" value="takedelete" />
                <input type="hidden" name="auth" value="<?= $LoggedUser['AuthKey'] ?>" />
                <input type="hidden" name="torrentid" value="<?= $TorrentID ?>" />
                <div class="center">
                    <p><strong class="important_text"><?= Lang::get('torrents', 'delete_torrent_note') ?></strong></p>
                </div>
                <div class="field_div">
                    <strong><?= Lang::get('torrents', 'reason') ?>: </strong>
                    <select name="reason">
                        <option value="Dead"><?= Lang::get('torrents', 'dead') ?></option>
                        <option value="Dupe"><?= Lang::get('torrents', 'dupe') ?></option>
                        <option value="Trumped"><?= Lang::get('torrents', 'trumped') ?></option>
                        <option value="Rules Broken"><?= Lang::get('torrents', 'rules_broken') ?></option>
                        <option value="" selected="selected"><?= Lang::get('torrents', 'other') ?></option>
                    </select>
                </div>
                <div class="field_div">
                    <strong><?= Lang::get('torrents', 'extra_info') ?>: </strong>
                    <input type="text" name="extra" size="30" placeholder="<?= Lang::get('torrents', 'extra_info_placeholder') ?>" />
                    <input value="Delete" type="submit" />
                </div>
            </form>
        </div>
    </div>
</div>
<?
if (check_perms('admin_reports')) {
?>
    <div id="all_reports" style="margin-left: auto; margin-right: auto;">
        <?
        include(SERVER_ROOT . '/classes/reports.class.php');

        //require(SERVER_ROOT.'/sections/reportsv2/array.php');
        include(Lang::getLangfilePath("report_types"));
        // TODO fix data
        $ReportID = 0;
        $ReporterID = 0;
        $DB->query("
			SELECT
				tg.Name,
                tg.SubName,
				tg.ID,
				CASE COUNT(ta.GroupID)
					WHEN 1 THEN aa.ArtistID
					WHEN 0 THEN '0'
					ELSE '0'
				END AS ArtistID,
				CASE COUNT(ta.GroupID)
					WHEN 1 THEN aa.Name
					WHEN 0 THEN ''
					ELSE 'Various Artists'
				END AS ArtistName,
				tg.Year,
				tg.CategoryID,
				t.Time,
				t.Remastered,
				t.RemasterTitle,
				t.RemasterYear,
				t.Source,
				t.Codec,
				t.Container,
				t.Resolution,
                t.Processing,
				t.Size,
				t.HasLog,
				t.HasLogDB,
				t.LogScore,
				t.LogChecksum,
				t.UserID AS UploaderID,
				uploader.Username
			FROM torrents AS t
				LEFT JOIN torrents_group AS tg ON tg.ID = t.GroupID
				LEFT JOIN torrents_artists AS ta ON ta.GroupID = tg.ID AND ta.Importance = '1'
				LEFT JOIN artists_alias AS aa ON aa.AliasID = ta.AliasID
				LEFT JOIN users_main AS uploader ON uploader.ID = t.UserID
			WHERE t.ID = $TorrentID");

        if (!$DB->has_results()) {
            die();
        }
        $Data = $DB->next_record(MYSQLI_ASSOC, false);
        list(
            $GroupName, $SubName, $GroupID, $ArtistID, $ArtistName, $Year, $CategoryID, $Time, $Remastered, $RemasterTitle,
            $RemasterYear, $Source, $Codec, $Container, $Resolution, $Processing, $Size, $HasLog, $HasLogDB, $LogScore, $LogChecksum, $UploaderID, $UploaderName
        ) = array_values($Data);

        $Type = 'dupe'; //hardcoded default

        if (array_key_exists($Type, $Types[$CategoryID])) {
            $ReportType = $Types[$CategoryID][$Type];
        } elseif (array_key_exists($Type, $Types['master'])) {
            $ReportType = $Types['master'][$Type];
        } else {
            //There was a type but it wasn't an option!
            $Type = 'other';
            $ReportType = $Types['master']['other'];
        }
        $RemasterDisplayString = Reports::format_reports_remaster_info($Remastered, $RemasterTitle, $Year);

        $RawName = Torrents::torrent_group_name($Data);
        $LinkName = "<a href=\"torrents.php?torrentid=$TorrentID\">$RawName</a>";
        $BBName = "[url=torrents.php?torrentid=$TorrentID] $RawName [/url]";
        ?>
        <div id="report<?= $ReportID ?>" class="report">
            <form class="create_form" name="report" id="reportform_<?= $ReportID ?>" action="reports.php" method="post">
                <?
                /*
                * Some of these are for takeresolve, some for the JavaScript.
                */
                ?>
                <div>
                    <input type="hidden" name="auth" value="<?= $LoggedUser['AuthKey'] ?>" />
                    <input type="hidden" id="reportid<?= $ReportID ?>" name="reportid" value="<?= $ReportID ?>" />
                    <input type="hidden" id="torrentid<?= $ReportID ?>" name="torrentid" value="<?= $TorrentID ?>" />
                    <input type="hidden" id="uploader<?= $ReportID ?>" name="uploader" value="<?= $UploaderName ?>" />
                    <input type="hidden" id="uploaderid<?= $ReportID ?>" name="uploaderid" value="<?= $UploaderID ?>" />
                    <input type="hidden" id="reporterid<?= $ReportID ?>" name="reporterid" value="<?= $ReporterID ?>" />
                    <input type="hidden" id="raw_name<?= $ReportID ?>" name="raw_name" value="<?= $RawName ?>" />
                    <input type="hidden" id="type<?= $ReportID ?>" name="type" value="<?= $Type ?>" />
                    <input type="hidden" id="categoryid<?= $ReportID ?>" name="categoryid" value="<?= $CategoryID ?>" />
                    <input type="hidden" id="pm_type<?= $ReportID ?>" name="pm_type" value="Uploader" />
                    <input type="hidden" id="from_delete<?= $ReportID ?>" name="from_delete" value="<?= $GroupID ?>" />
                </div>
                <div class="table_container border">
                    <table cellpadding="5" class="box layout">
                        <tr>
                            <td class="label"><?= Lang::get('global', 'torrent') ?>:</td>
                            <td colspan="3">
                                <? if (!$GroupID) { ?>
                                    <a href="log.php?search=Torrent+<?= $TorrentID ?>"><?= $TorrentID ?></a> (<?= Lang::get('torrents', 'deleted') ?>)
                                <?      } else { ?>
                                    <?= $LinkName ?>
                                    <a href="torrents.php?action=download&amp;id=<?= $TorrentID ?>&amp;authkey=<?= $LoggedUser['AuthKey'] ?>&amp;torrent_pass=<?= $LoggedUser['torrent_pass'] ?>" class="brackets tooltip" title="<?= Lang::get('global', 'download') ?>">DL</a>
                                    <?= Lang::get('torrents', 'upload_by_before') ?><a href="user.php?id=<?= $UploaderID ?>"><?= $UploaderName ?></a><?= Lang::get('torrents', 'upload_by_after') ?> <?= time_diff($Time) ?>
                                    <br />
                                    <? $DB->query("
				SELECT r.ID
				FROM reportsv2 AS r
					LEFT JOIN torrents AS t ON t.ID = r.TorrentID
				WHERE r.Status != 'Resolved'
					AND t.GroupID = $GroupID");
                                    $GroupOthers = ($DB->has_results());

                                    if ($GroupOthers > 0) { ?>
                                        <div style="text-align: right;">
                                            <a href="reportsv2.php?view=group&amp;id=<?= $GroupID ?>"><?= Lang::get('torrents', 'there_are_n_other_reports_for_torrents_in_this_group_1') ?><?= (($GroupOthers > 1) ? Lang::get('torrents', 'there_are_n_other_reports_for_torrents_in_this_group_2') . " $GroupOthers " . Lang::get('torrents', 'there_are_n_other_reports_for_torrents_in_this_group_3') : Lang::get('torrents', 'there_are_n_other_reports_for_torrents_in_this_group_4')) ?><?= Lang::get('torrents', 'there_are_n_other_reports_for_torrents_in_this_group_5') ?></a>
                                        </div>
                                    <?          }

                                    $DB->query("
				SELECT t.UserID
				FROM reportsv2 AS r
					JOIN torrents AS t ON t.ID = r.TorrentID
				WHERE r.Status != 'Resolved'
					AND t.UserID = $UploaderID");
                                    $UploaderOthers = ($DB->has_results());

                                    if ($UploaderOthers > 0) { ?>
                                        <div style="text-align: right;">
                                            <a href="reportsv2.php?view=uploader&amp;id=<?= $UploaderID ?>"><?= Lang::get('torrents', 'there_are_n_other_reports_for_torrents_uploaded_by_this_user_1') ?><?= (($UploaderOthers > 1) ? Lang::get('torrents', 'there_are_n_other_reports_for_torrents_uploaded_by_this_user_2') . "are $UploaderOthers reports" . Lang::get('torrents', 'there_are_n_other_reports_for_torrents_uploaded_by_this_user_3') : Lang::get('torrents', 'there_are_n_other_reports_for_torrents_uploaded_by_this_user_4')) ?><?= Lang::get('torrents', 'there_are_n_other_reports_for_torrents_uploaded_by_this_user_5') ?></a>
                                        </div>
                                        <?          }

                                    $DB->query("
				SELECT DISTINCT req.ID,
					req.FillerID,
					um.Username,
					req.TimeFilled
				FROM requests AS req
					JOIN users_main AS um ON um.ID = req.FillerID
				AND req.TorrentID = $TorrentID");
                                    $Requests = ($DB->has_results());
                                    if ($Requests > 0) {
                                        while (list($RequestID, $FillerID, $FillerName, $FilledTime) = $DB->next_record()) {
                                        ?>
                                            <div style="text-align: right;">
                                                <strong class="important_text"><a href="user.php?id=<?= $FillerID ?>"><?= $FillerName ?></a> <?= Lang::get('torrents', 'used_this_torrent_to_fill') ?> <a href="requests.php?action=viewrequest&amp;id=<?= $RequestID ?>"><?= Lang::get('torrents', 'this_request') ?></a> <?= time_diff($FilledTime) ?></strong>
                                            </div>
                                <?              }
                                    }
                                }
                                ?>
                            </td>
                        </tr>
                        <?              /* END REPORTED STUFF :|: BEGIN MOD STUFF */ ?>
                        <tr>
                            <td class="label">
                                <a href="javascript:Load('<?= $ReportID ?>')" class="tooltip" title="<?= Lang::get('torrents', 'resolve_title') ?>"><?= Lang::get('torrents', 'resolve') ?>:</a>
                            </td>
                            <td colspan="3">
                                <select name="resolve_type" id="resolve_type<?= $ReportID ?>" onchange="ChangeResolve(<?= $ReportID ?>);">
                                    <?
                                    $TypeList = $Types['master'] + $Types[$CategoryID];
                                    $Priorities = array();
                                    foreach ($TypeList as $Key => $Value) {
                                        $Priorities[$Key] = $Value['priority'];
                                    }
                                    array_multisort($Priorities, SORT_ASC, $TypeList);

                                    foreach ($TypeList as $IType => $Data) {
                                    ?>
                                        <option value="<?= $IType ?>" <?= (($Type == $IType) ? ' selected="selected"' : '') ?>><?= $Data['title'] ?></option>
                                    <?
                                    }
                                    ?>
                                </select>
                                <span id="options<?= $ReportID ?>">
                                    <span class="tooltip" title="<?= Lang::get('torrents', 'delete_title') ?>">
                                        <label for="delete<?= $ReportID ?>"><strong><?= Lang::get('global', 'delete') ?></strong></label>
                                        <input type="checkbox" name="delete" id="delete<?= $ReportID ?>" <?= ($ReportType['resolve_options']['delete'] ? ' checked="checked"' : '') ?> />
                                    </span>
                                    <span class="tooltip" title="<?= Lang::get('torrents', 'warning_title') ?>">
                                        <label for="warning<?= $ReportID ?>"><strong><?= Lang::get('torrents', 'warning') ?></strong></label>
                                        <select name="warning" id="warning<?= $ReportID ?>">
                                            <? for ($i = 0; $i < 9; $i++) { ?>
                                                <option value="<?= $i ?>" <?= (($ReportType['resolve_options']['warn'] == $i) ? ' selected="selected"' : '') ?>><?= $i ?></option>
                                            <?  } ?>
                                        </select>
                                    </span>
                                    <?
                                    $DB->query("select firsttorrent from users_main where id=$UploaderID and firsttorrent=$TorrentID");
                                    $FirstTorrent = $DB->has_results();
                                    if ($FirstTorrent) {
                                    ?>
                                        <span class="tooltip" title="<?= Lang::get('torrents', 'first_torrent_title') ?>"><strong class="important_text"><?= Lang::get('torrents', 'first_torrent') ?></strong></span>
                                    <?
                                    }
                                    ?>
                                    <span class="tooltip" title="<?= Lang::get('torrents', 'remove_upload_privileges_title') ?>">
                                        <label for="upload<?= $ReportID ?>"><strong><?= Lang::get('torrents', 'remove_upload_privileges') ?></strong></label>
                                        <input type="checkbox" name="upload" id="upload<?= $ReportID ?>" <?= ($ReportType['resolve_options']['upload'] ? ' checked="checked"' : '') ?> />
                                    </span>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="label"><?= Lang::get('torrents', 'pm_uploader') ?>:</td>
                            <td colspan="3">
                                <span class="tooltip" title="<?= Lang::get('torrents', 'appended_to_the_regular_message_unless_using_send_now') ?>">
                                    <textarea name="uploader_pm" id="uploader_pm<?= $ReportID ?>" cols="50" rows="1"></textarea>
                                </span>
                                <input type="button" value="Send now" onclick="SendPM(<?= $ReportID ?>);" />
                            </td>
                        </tr>
                        <tr>
                            <td class="label"><strong><?= Lang::get('torrents', 'extra') ?></strong><?= Lang::get('torrents', 'space_log_message') ?>:</td>
                            <td>
                                <input type="text" name="log_message" id="log_message<?= $ReportID ?>" size="40" />
                            </td>
                            <td class="label"><strong><?= Lang::get('torrents', 'extra') ?></strong><?= Lang::get('torrents', 'space_staff_notes') ?>:</td>
                            <td>
                                <input type="text" name="admin_message" id="admin_message<?= $ReportID ?>" size="40" />
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" style="text-align: center;">
                                <input type="button" value="Submit" onclick="TakeResolve(<?= $ReportID ?>);" />
                            </td>
                        </tr>
                    </table>
                </div>
            </form>
            <br />
        </div>
    </div>
<?
}
View::show_footer(); ?>