<?php

if (!check_perms('users_mod')) {
    error(403);
}

$TorrentID = intval($_GET['torrentid']);
$LogID = intval($_GET['logid']);

$DB->query("SELECT GroupID FROM torrents WHERE ID='{$TorrentID}'");
if (!$DB->has_results()) {
    error(404);
}
list($GroupID) = $DB->next_record(MYSQLI_NUM);
$Group = Torrents::array_group(Torrents::get_groups(array($GroupID))[$GroupID]);
$TorrentTags = new Tags($Group['TagList']);

if (!empty($Group['ExtendedArtists'][1]) || !empty($Group['ExtendedArtists'][4]) || !empty($Group['ExtendedArtists'][5])) {
    unset($Group['ExtendedArtists'][2]);
    unset($Group['ExtendedArtists'][3]);
    $DisplayName = Artists::display_artists($Group['ExtendedArtists']);
} elseif (!empty($Artists)) {
    $DisplayName = Artists::display_artists(array(1 => $Group['Artists']));
} else {
    $DisplayName = '';
}
$DisplayName .= '<a href="torrents.php?id=' . $GroupID . '&amp;torrentid=' . $TorrentID . '" class="tooltip" title="' . Lang::get('global', 'view_torrent') . '" dir="ltr">' . $Group['GroupName'] . '</a>';
if ($Group['GroupYear'] > 0) {
    $DisplayName .= " ({$Group['GroupYear']})";
}
$ExtraInfo = Torrents::torrent_info($Group['Torrents'][$TorrentID]);
if ($Group['GroupVanityHouse'] || $ExtraInfo) {
    $DisplayName .= ' [';
}
if ($Group['GroupVanityHouse']) {
    $DisplayName .= '<abbr class="tooltip" title="' . Lang::get('global', 'this_is_vh') . '">VH</abbr>';
}
if ($ExtraInfo) {
    $DisplayName .= ($Group['GroupVanityHouse'] ? ' ' : '') . $ExtraInfo;
}
if ($Group['GroupVanityHouse'] || $ExtraInfo) {
    $DisplayName .= ']';
}
$DB->query("SELECT Log, FileName, Details, Score, Checksum, Adjusted, AdjustedScore, AdjustedChecksum, AdjustedBy, AdjustmentReason, AdjustmentDetails FROM torrents_logs WHERE TorrentID='{$TorrentID}' AND LogID='{$LogID}'");
if (!$DB->has_results()) {
    error(404);
}

$Log = $DB->next_record(MYSQLI_ASSOC, array('AdjustmentDetails'));

$Checksum = ($Log['Checksum'] == '1') ? 'Good' : 'Missing/Invalid Checksum';
$Details = "";
if (!empty($Log['Details'])) {
    $Log['Details'] = Logchecker::translateDetail($Log['Details']);
    $Log['Details'] = explode("\r\n", $Log['Details']);
    $Details .= '<ul>';
    foreach ($Log['Details'] as $Entry) {
        $Details .= '<li>' . $Entry . '</li>';
    }
    $Details .= '</ul>';
}

$AdjustedScore = (!isset($Log['AdjustedScore']) || $Log['Adjusted'] == '0') ? $Log['Score'] : $Log['AdjustedScore'];
$AdjustedUser = (!empty($Log['AdjustedBy'])) ? "(By: " . Users::format_username($Log['AdjustedBy']) . ")" : "";
$AdjustedChecksum = ($Log['Adjusted'] == '0') ? $Log['Checksum'] : $Log['AdjustedChecksum'];
$AdjustmentDetails = array('tracks' => array('crc_mismatches' => 0, 'suspicious_positions' => 0, 'timing_problems' => 0));
if (!empty($Log['AdjustmentDetails'])) {
    $AdjustmentDetails = unserialize($Log['AdjustmentDetails']);
}
View::show_header(Lang::get('torrents', 'edit_log'), 'edit_log');

?>
<div class="thin">
    <h2 id="edit_log_h2"><?= Lang::get('torrents', 'edit_log') ?></h2>
    <form action="torrents.php?action=take_editlog" method="post" name="edit_log">
        <input type="hidden" name="logid" value="<?= $LogID ?>" />
        <input type="hidden" name="torrentid" value="<?= $TorrentID ?>" />
        <table class="layout border" id="edit_log_table">
            <tr class="colhead">
                <td colspan="3"><?= Lang::get('torrents', 'log_details') ?></td>
            </tr>
            <tr>
                <td><?= Lang::get('global', 'torrent') ?></td>
                <td colspan="2"><?= $DisplayName ?></td>
            </tr>
            <tr>
                <td><?= Lang::get('torrents', 'log_file') ?></td>
                <td colspan="2"><?= $Log['FileName'] ?> (<a href="logs/<?= $TorrentID ?>_<?= $LogID ?>.log" target="_blank"><?= Lang::get('torrents', 'view_raw') ?></a>)</td>
            </tr>
            <tr>
                <td><?= Lang::get('torrents', 'log_score') ?></td>
                <td colspan="2"><?= $Log['Score'] ?> (<a href="torrents.php?action=rescore_log&logid=<?= $LogID ?>&torrentid=<?= $TorrentID ?>"><?= Lang::get('torrents', 'rescore_log') ?></a>)</td>
            </tr>
            <tr>
                <td><?= Lang::get('torrents', 'checksum') ?></td>
                <td colspan="2"><?= $Checksum ?></td>
            </tr>
            <tr>
                <td><?= Lang::get('torrents', 'log_validation_report') ?></td>
                <td colspan="2"><?= $Details ?></td>
            </tr>
            <tr class="colhead">
                <td colspan="3"><?= Lang::get('torrents', 'manual_adjustment') ?></td>
            </tr>
            <tr>
                <td><?= Lang::get('torrents', 'manually_adjusted') ?></td>
                <td colspan="2"><input type="checkbox" name="adjusted" <?= ($Log['Adjusted'] == '1' ? 'checked' : '') ?> /> <?= $AdjustedUser ?></td>
            </tr>
            <tr>
                <td><?= Lang::get('torrents', 'adjusted_score') ?></td>
                <td colspan="2"><input type="text" name="adjusted_score" value="<?= $AdjustedScore ?>" disabled="disabled" data-actual="100" /></td>
            </tr>
            <tr>
                <td><?= Lang::get('torrents', 'checksum_valid') ?></td>
                <td colspan="2"><input type="checkbox" name="adjusted_checksum" <?= ($AdjustedChecksum == '1' ? 'checked' : '') ?> /></td>
            </tr>
            <tr>
                <td><?= Lang::get('torrents', 'adjustment_reason') ?></td>
                <td colspan="2"><input type="text" name="adjustment_reason" value="<?= $Log['AdjustmentReason'] ?>" size="100" /></td>
            </tr>
            <tr>
                <td rowspan="4"><?= Lang::get('torrents', 'audio_deductions') ?></td>
                <td><label><input type="checkbox" name="read_mode_secure" <?= isset_array_checked($AdjustmentDetails, 'read_mode_secure') ?> data-score="20" /><?= Lang::get('torrents', 'non_secure_mode_used') ?></label></td>
                <td><label><input type="checkbox" name="audio_cache" <?= isset_array_checked($AdjustmentDetails, 'audio_cache') ?> data-score="10" /><?= Lang::get('torrents', 'should_defeat_audio_cache') ?></label></td>
            </tr>
            <tr>
                <td style="display: none"></td>
                <td><label><input type="checkbox" name="c2_points" <?= isset_array_checked($AdjustmentDetails, 'c2_points') ?> data-score="10" /><?= Lang::get('torrents', 'c2_enabled') ?></td>
                <td><label><input type="checkbox" name="drive_offset" <?= isset_array_checked($AdjustmentDetails, 'drive_offset') ?> data-score="5" /><?= Lang::get('torrents', 'incorrect_offset') ?></td>
            </tr>
            <tr>
                <td style="display: none"></td>
                <td><label><input type="checkbox" name="fill_offsets" <?= isset_array_checked($AdjustmentDetails, 'fill_offsets') ?> data-score="5" /><?= Lang::get('torrents', 'not_fill_missing_offset_with_silence') ?></td>
                <td><label><input type="checkbox" name="deletes_ofsets" <?= isset_array_checked($AdjustmentDetails, 'deletes_ofsets') ?> data-score="5" /><?= Lang::get('torrents', 'deletes_silent_blocks') ?></td>
            </tr>
            <tr>
                <td style="display: none"></td>
                <td><label><input type="checkbox" name="gap_handling" <?= isset_array_checked($AdjustmentDetails, 'gap_handling') ?> data-score="10" /><?= Lang::get('torrents', 'gap_should_be_appended') ?></td>
                <td><label><input type="checkbox" name="test_and_copy" <?= isset_array_checked($AdjustmentDetails, 'test_and_copy') ?> data-score="10" /><?= Lang::get('torrents', 'test_and_copy_not_used') ?></td>
            </tr>
            <tr>
                <td rowspan="3"><?= Lang::get('torrents', 'track_deductions') ?></td>
                <td><?= Lang::get('torrents', 'crc_mismatches') ?></td>
                <td><input type="text" name="crc_mismatches" value="<?= $AdjustmentDetails['tracks']['crc_mismatches'] ?>" data-score="30" /></td>
            </tr>
            <tr>
                <td style="display:none"></td>
                <td><?= Lang::get('torrents', 'suspicious_positions') ?></td>
                <td><input type="text" name="suspicious_positions" value="<?= $AdjustmentDetails['tracks']['suspicious_positions'] ?>" data-score="20" /></td>
            </tr>
            <tr>
                <td style="display:none"></td>
                <td><?= Lang::get('torrents', 'timing_problems') ?></td>
                <td><input type="text" name="timing_problems" value="<?= $AdjustmentDetails['tracks']['timing_problems'] ?>" data-score="20" /></td>
            </tr>
            <tr>
                <td rowspan="2"><?= Lang::get('torrents', 'non_audio_deductions') ?></td>
                <td><label><input type="checkbox" name="range_rip" <?= isset_array_checked($AdjustmentDetails, 'range_rip') ?> data-score="30" /><?= Lang::get('torrents', 'range_rip') ?></td>
                <td><label><input type="checkbox" name="null_samples" <?= isset_array_checked($AdjustmentDetails, 'null_samples') ?> data-score="5" /><?= Lang::get('torrents', 'crc_cal_should_use_null_samples') ?></td>
            </tr>
            <tr>
                <td style="display:none"></td>
                <td><label><input type="checkbox" name="eac_old" <?= isset_array_checked($AdjustmentDetails, 'eac_old') ?> data-score="30" /><?= Lang::get('torrents', 'eac_older_than_0.99') ?></td>
                <td><label><input type="checkbox" name="id3_tags" <?= isset_array_checked($AdjustmentDetails, 'id3_tags') ?> data-score="1" /><?= Lang::get('torrents', 'id3_found') ?></td>
            </tr>
            <tr>
                <td rowspan="1"><?= Lang::get('torrents', 'other_reasons') ?></td>
                <td><label><input type="checkbox" name="foreign_log" <?= isset_array_checked($AdjustmentDetails, 'foreign_log') ?> /><?= Lang::get('torrents', 'foreign_log') ?></label></td>
                <td><label><input type="checkbox" name="combined_log" <?= isset_array_checked($AdjustmentDetails, 'combined_log') ?> /><?= Lang::get('torrents', 'combined_log') ?></label></td>
            </tr>
            <tr style="text-align: center">
                <td colspan="3"><input type="submit" value="Rescore Log" /></td>
            </tr>
        </table>
    </form>
</div>

<?php
View::show_footer();
