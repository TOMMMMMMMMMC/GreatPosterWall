<?
$GroupID = $_GET['groupid'];
if (!is_number($GroupID)) {
    error(404);
}

View::show_header(Lang::get('torrents', 'history_for_group_before') . "$GroupID" . Lang::get('torrents', 'history_for_group_after'));

$Groups = Torrents::get_groups(array($GroupID), true, true, false);
$Title = Torrents::torrent_group_name($Groups[$GroupID], true, true);
?>

<div class="thin">
    <div class="header">
        <h2><?= Lang::get('torrents', 'history_for_before') ?><?= $Title ?><?= Lang::get('torrents', 'history_for_after') ?></h2>
    </div>
    <div class="table_container border">
        <table id="history_for_group_table">
            <tr class="colhead">
                <td><?= Lang::get('torrents', 'date') ?></td>
                <td><?= Lang::get('global', 'torrent') ?></td>
                <td><?= Lang::get('torrents', 'user') ?></td>
                <td><?= Lang::get('torrents', 'info') ?></td>
            </tr>
            <?
            $Log = $DB->query("
			SELECT TorrentID, UserID, Info, Time
			FROM group_log
			WHERE GroupID = $GroupID
			ORDER BY Time DESC");
            $LogEntries = $DB->to_array(false, MYSQLI_NUM);
            foreach ($LogEntries as $LogEntry) {
                list($TorrentID, $UserID, $Info, $Time) = $LogEntry;
            ?>
                <tr class="rowa">
                    <td><?= $Time ?></td>
                    <?
                    if ($TorrentID != 0) {
                        $DB->query("
					SELECT Media, Format, Encoding
					FROM torrents
					WHERE ID = $TorrentID");
                        list($Media, $Format, $Encoding) = $DB->next_record();
                        if (!$DB->has_results()) { ?>
                            <td><a href="torrents.php?torrentid=<?= $TorrentID ?>"><?= $TorrentID ?></a> (<?= Lang::get('torrents', 'deleted') ?>)</td><?
                                                                                                                                                    } elseif ($Media == '') { ?>
                            <td><a href="torrents.php?torrentid=<?= $TorrentID ?>"><?= $TorrentID ?></a></td><?
                                                                                                                                                    } else { ?>
                            <td><a href="torrents.php?torrentid=<?= $TorrentID ?>"><?= $TorrentID ?></a> (<?= $Format ?>/<?= $Encoding ?>/<?= $Media ?>)</td>
                        <?              }
                                                                                                                                                } else { ?>
                        <td></td>
                    <?          }   ?>
                    <td><?= Users::format_username($UserID, false, false, false) ?></td>
                    <td><?= $Info ?></td>
                </tr>
            <?
            }
            ?>
        </table>
    </div>
</div>
<?
View::show_footer();
?>