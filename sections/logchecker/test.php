<?
View::show_header('Logchecker');
/*
$DB->query("
SELECT t.ID, g.Name as AlbumName, a.Name as ArtistName, g.Year, t.Format, t.Encoding
FROM torrents t
JOIN torrents_group g ON t.GroupID = g.ID
JOIN torrents_artists ta ON g.ID = ta.GroupID
JOIN artists_group a ON a.ArtistID = ta.ArtistID
WHERE t.HasLog='1' AND t.LogScore=0 AND t.UserID = " . $LoggedUser['ID']);

if ($DB->has_results()) {
    $output = '';
    while (list($ID, $AlbumName, $ArtistName, $Year, $Format, $Encoding) = $DB->next_record()) {
        $output .= "<tr><td style=\"width: 5%\"><input type=\"radio\" name=\"torrentid\" value=\"$ID\"></td><td><a href=\"/torrents.php?torrentid=$ID\">$ArtistName - $AlbumName [$Year] [$Format/$Encoding]</a></td></tr>";
    }
}
*/

$AcceptValues = Logchecker::get_accept_values();
?>

<div class="linkbox">
    <a href="logchecker.php?action=upload" class="brackets" title="<?= Lang::get('logchecker', 'upload_title') ?>"><?= Lang::get('logchecker', 'upload') ?></a>
    <a href="logchecker.php?action=update" class="brackets" title="<?= Lang::get('logchecker', 'update_title') ?>"><?= Lang::get('logchecker', 'update') ?></a>
</div>
<div class="thin">
    <h2 class="center">DIC Logchecker: EAC & XLD</h2>
    <div class="box pad">
        <p><?= Lang::get('logchecker', 'logchecker_note') ?></p>
        <div class="table_container mgb">
            <table class="forum_post vertical_margin">
                <tr class="colhead">
                    <td colspan="2"><?= Lang::get('logchecker', 'upload_file') ?></td>
                </tr>
                <tr>
                    <td>
                        <form action="" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="take_test" />
                            <input type="file" accept="{$AcceptValues}" name="log" size="40" />
                            <input type="submit" value="Upload log" name="submit" />
                        </form>
                    </td>
                </tr>
            </table>
        </div>
        <div class="table_container">
            <table class="forum_post vertical_margin">
                <tr class="colhead">
                    <td colspan="2"><?= Lang::get('logchecker', 'paste_log') ?></td>
                </tr>
                <tr>
                    <td>
                        <form action="" method="post">
                            <input type="hidden" name="action" value="take_test" />
                            <textarea rows="20" style="width: 99%" name="pastelog" wrap="soft"></textarea>
                            <br /><br />
                            <input type="submit" value="Upload log" name="submit" />
                        </form>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>
<?

View::show_footer();
