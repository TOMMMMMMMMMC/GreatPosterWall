<?
View::show_header('Logchecker', 'upload');

?>
<div class="linkbox">
    <a href="logchecker.php" class="brackets"><?= Lang::get('logchecker', 'test') ?></a>
    <a href="logchecker.php?action=upload" class="brackets"><?= Lang::get('logchecker', 'upload') ?></a>
</div>
<div class="thin">
    <h2 class="center"><?= Lang::get('logchecker', 'update') ?></h2>
    <div class="box pad">
        <p>
            <?= Lang::get('logchecker', 'update_text') ?>
            <!-- This form allows you to update the logs for any torrent that you've uploaded.
        Select a torrent and upload the log files in the form <u>below</u>, making sure to add
        all logs that you wish to upload. This will overwrite any previously uploaded logs for
        this torrent. If you wish to just have a torrent manually rescored, please report it
        to staff. -->
        </p>
        <br />
        <form action="" method="post" enctype="multipart/form-data">
            <input type="hidden" name="action" value="take_upload" />
            <input type="hidden" name="from_action" value="update" />
            <table class="form_post vertical_margin">
                <tr class="colhead">
                    <td colspan="2"><?= Lang::get('logchecker', 'select_torrent') ?></td>
                </tr>
                <?

                $DB->query("
	SELECT 
		ID, GroupID, `Format`, Encoding, HasCue, HasLog, HasLogDB, LogScore, 
		LogChecksum
	FROM torrents
	WHERE HasLog='1' AND HasLogDB='1' AND UserID = " . $LoggedUser['ID']);

                if ($DB->has_results()) {
                    $GroupIDs = $DB->collect('GroupID');
                    $TorrentsInfo = $DB->to_array('ID');
                    $Groups = Torrents::get_groups($GroupIDs);
                    foreach ($TorrentsInfo as $TorrentID => $Torrent) {
                        list($ID, $GroupID, $Format, $Encoding, $HasCue, $HasLog, $HasLogDB, $LogScore, $LogChecksum) = $Torrent;
                        $Group = $Groups[(int) $GroupID];
                        $GroupName = $Group['Name'];
                        $GroupYear = $Group['Year'];
                        $ExtendedArtists = $Group['ExtendedArtists'];
                        $Artists = $Group['Artists'];
                        if (!empty($ExtendedArtists[1]) || !empty($ExtendedArtists[4]) || !empty($ExtendedArtists[5])) {
                            unset($ExtendedArtists[2]);
                            unset($ExtendedArtists[3]);
                            $DisplayName = Artists::display_artists($ExtendedArtists);
                        } elseif (!empty($Artists)) {
                            $DisplayName = Artists::display_artists(array(1 => $Artists));
                        } else {
                            $DisplayName = '';
                        }
                        $DisplayName .= '<a href="torrents.php?id=' . $GroupID . '&amp;torrentid=' . $ID . '" class="tooltip" title="' . Lang::get('global', 'view_torrent') . '" dir="ltr">' . $GroupName . '</a>';
                        if ($GroupYear > 0) {
                            $DisplayName .= " [{$GroupYear}]";
                        }
                        $Info = array();
                        if (!empty($Data['Format'])) {
                            $Info[] = $Data['Format'];
                        }
                        if (!empty($Data['Encoding'])) {
                            $Info[] = $Data['Encoding'];
                        }
                        if (!empty($Info)) {
                            $DisplayName .= ' [' . implode('/', $Info) . ']';
                        }
                        if ($HasLog == '1') {
                            $DisplayName .= ' / Log' . ($HasLogDB == '1' ? " ({$LogScore}%)" : "");
                        }
                        if ($HasCue == '1') {
                            $DisplayName .= ' / Cue';
                        }
                        if ($LogChecksum == '0') {
                            $DisplayName .= ' / ' . Format::torrent_label('Bad/Missing Checksum');
                        }
                        echo "\t\t\t\t<tr><td style=\"width: 5%;\"><input type=\"radio\" name=\"torrentid\" value=\"$ID\"></td><td>{$DisplayName}</td></tr>";
                    }
                    $AcceptValues = Logchecker::get_accept_values();
                ?>
                    <tr class="colhead">
                        <td colspan="2"><?= Lang::get('logchecker', 'upload_torrent') ?></td>
                    </tr>
                    <tr>
                        <td colspan="2" id="logfields">
                            <?= Lang::get('logchecker', 'check_before_upload') ?>
                            <!-- Check your log files before uploading <a href="logchecker.php" target="_blank">here</a>. For multi-disc releases, click the "<span class="brackets">+</span>" button to add multiple log files. -->
                            <br />
                            <input id="file" type="file" accept="<?= $AcceptValues ?>" multiple name="logfiles[]" size="50" required /> <a href="javascript:;" onclick="AddLogField();" class="brackets">+</a> <a href="javascript:;" onclick="RemoveLogField();" class="brackets">&minus;</a>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <input type="submit" value="Upload Logs!" name="logsubmit" />
                        </td>
                    </tr>
                <?

                } else {
                    echo "\t\t\t<tr><td colspan='2'>No uploads found.</td></tr>";
                }
                ?>
            </table>
    </div>
    </form>
</div>
<?

View::show_footer();
