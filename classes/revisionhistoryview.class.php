<?
class RevisionHistoryView {
    /**
     * Render the revision history
     * @param array $RevisionHistory see RevisionHistory::get_revision_history
     * @param string $BaseURL
     */
    public static function render_revision_history($RevisionHistory, $BaseURL) {
?>
        <div class="table_container border">
            <table cellpadding="6" cellspacing="1" border="0" width="100%" class="border" id="revision_table">
                <tr class="colhead">
                    <td><?= Lang::get('global', 'revision') ?></td>
                    <td><?= Lang::get('global', 'date') ?></td>
                    <td><?= Lang::get('global', 'user') ?></td>
                    <td><?= Lang::get('global', 'summary') ?></td>
                </tr>
                <?
                $Row = 'a';
                foreach ($RevisionHistory as $Entry) {
                    list($RevisionID, $Summary, $Time, $UserID) = $Entry;
                    $Row = (($Row == 'a') ? 'b' : 'a');
                ?>
                    <tr class="row<?= $Row ?>">
                        <td>
                            <?= "<a href=\"$BaseURL&amp;revisionid=$RevisionID\">#$RevisionID</a>" ?>
                        </td>
                        <td>
                            <?= $Time ?>
                        </td>
                        <td>
                            <?= Users::format_username($UserID, false, false, false) ?>
                        </td>
                        <td>
                            <?= ($Summary ? $Summary : '(empty)') ?>
                        </td>
                    </tr>
                <?      } ?>
            </table>
        </div>
<?
    }
}
