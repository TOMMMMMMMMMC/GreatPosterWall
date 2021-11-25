<?

View::show_header(Lang::get('staffpm', 'staff_pms'), 'staffpm');

// Get messages
$StaffPMs = $DB->query(
    "
	SELECT
		ID,
		Subject,
		UserID,
		Status,
		Level,
		AssignedToUser,
		Date,
		Unread
	FROM staff_pm_conversations
	WHERE UserID = " . $LoggedUser['ID'] . "
	ORDER BY Status, Date DESC"
);

// Start page
?>
<div class="thin">
    <div class="header">
        <h2><?= Lang::get('staffpm', 'staff_pms') ?></h2>
        <div class="linkbox">
            <a href="#" onclick="$('#compose').gtoggle();" class="brackets"><?= Lang::get('staffpm', 'compose_new') ?></a>
        </div>
    </div>
    <br />
    <br />
    <? View::parse('generic/reply/staffpm.php', array('Hidden' => true)); ?>
    <div class="box pad" id="inbox">
        <?

        if (!$DB->has_results()) {
            // No messages
        ?>
            <h2><?= Lang::get('staffpm', 'no_messages') ?></h2>
        <?
        } else {
            // Messages, draw table
        ?>
            <form class="manage_form" name="staff_messages" method="post" action="staffpm.php" id="messageform">
                <input type="hidden" name="action" value="multiresolve" />
                <h3><?= Lang::get('staffpm', 'open_messages') ?></h3>
                <div class="table_container border mgb">
                    <table class="message_table checkboxes">
                        <tr class="colhead">
                            <td width="10"><input type="checkbox" onclick="toggleChecks('messageform', this);" /></td>
                            <td width="50%"><?= Lang::get('staffpm', 'subject') ?></td>
                            <td><?= Lang::get('staffpm', 'date') ?></td>
                            <td><?= Lang::get('staffpm', 'assigned_to') ?></td>
                        </tr>
                        <?
                        // List messages
                        $Row = 'a';
                        $ShowBox = 1;
                        while (list($ID, $Subject, $UserID, $Status, $Level, $AssignedToUser, $Date, $Unread) = $DB->next_record()) {
                            if ($Unread === '1') {
                                $RowClass = 'unreadpm';
                            } else {
                                $Row = $Row === 'a' ? 'b' : 'a';
                                $RowClass = "row$Row";
                            }

                            if ($Status == 'Resolved') {
                                $ShowBox++;
                            }
                            if ($ShowBox == 2) {
                                // First resolved PM
                        ?>
                    </table>
                </div>

                <h3><?= Lang::get('staffpm', 'resolved_messages') ?></h3>
                <div class="table_container border">
                    <table class="message_table checkboxes">
                        <tr class="colhead">
                            <td width="10"><input type="checkbox" onclick="toggleChecks('messageform',this)" /></td>
                            <td width="50%"><?= Lang::get('staffpm', 'subject') ?></td>
                            <td><?= Lang::get('staffpm', 'date') ?></td>
                            <td><?= Lang::get('staffpm', 'assigned_to') ?></td>
                        </tr>
                    <?
                            }

                            // Get assigned
                            $Assigned = ($Level == 0) ? 'First Line Support' : $ClassLevels[$Level]['Name'];
                            // No + on Sysops
                            if ($Assigned != 'Sysop') {
                                $Assigned .= '+';
                            }

                            // Table row
                    ?>
                    <tr class="<?= $RowClass ?>">
                        <td class="center"><input type="checkbox" name="id[]" value="<?= $ID ?>" /></td>
                        <td><a href="staffpm.php?action=viewconv&amp;id=<?= $ID ?>"><?= display_str($Subject) ?></a></td>
                        <td><?= time_diff($Date, 2, true) ?></td>
                        <td><?= $Assigned ?></td>
                    </tr>
                <?
                            $DB->set_query_id($StaffPMs);
                        }

                        // Close table and multiresolve form
                ?>
                    </table>
                </div>
                <div class="submit_div">
                    <input type="submit" value="Resolve selected" />
                </div>
            </form>
        <?
        }
        ?>
    </div>
</div>
<? View::show_footer(); ?>