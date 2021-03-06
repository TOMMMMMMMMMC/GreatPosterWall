<?php

View::show_header(Lang::get('staffpm', 'staff_inbox'));

$View = display_str($_GET['view']);
$UserLevel = $LoggedUser['EffectiveClass'];


$LevelCap = 1000;


// Setup for current view mode
$SortStr = 'IF(AssignedToUser = ' . $LoggedUser['ID'] . ', 0, 1) ASC, ';
switch ($View) {
    case 'unanswered':
        $ViewString = Lang::get('staffpm', 'unanswered');
        $Status = "Unanswered";
        break;
    case 'open':
        $ViewString = Lang::get('staffpm', 'unresolved');
        $Status = "Open', 'Unanswered";
        $SortStr = '';
        break;
    case 'resolved':
        $ViewString = Lang::get('staffpm', 'resolved');
        $Status = "Resolved";
        $SortStr = '';
        break;
    case 'my':
        $ViewString = Lang::get('staffpm', 'your_unanswered');
        $Status = "Unanswered";
        break;
    default:
        $Status = "Unanswered";
        if ($UserLevel >= $Classes[FORUM_MOD]['Level']) {
            $ViewString = Lang::get('staffpm', 'your_unanswered');
        } else {
            // FLS
            $ViewString = Lang::get('staffpm', 'unanswered');
        }
        break;
}

$WhereCondition = "
	WHERE (LEAST($LevelCap, spc.Level) <= $UserLevel OR spc.AssignedToUser = '" . $LoggedUser['ID'] . "')
	  AND spc.Status IN ('$Status')";

if ($ViewString == 'Your Unanswered') {
    if ($UserLevel >= $Classes[MOD]['Level']) {
        $WhereCondition .= " AND spc.Level >= " . $Classes[MOD]['Level'];
    } else if ($UserLevel >= $Classes[FORUM_MOD]['Level']) {
        $WhereCondition .= " AND spc.Level >= " . $Classes[FORUM_MOD]['Level'];
    }
}

list($Page, $Limit) = Format::page_limit(MESSAGES_PER_PAGE);
// Get messages
$StaffPMs = $DB->query("
	SELECT
		SQL_CALC_FOUND_ROWS
		spc.ID,
		spc.Subject,
		spc.UserID,
		spc.Status,
		spc.Level,
		spc.AssignedToUser,
		spc.Date,
		spc.Unread,
		COUNT(spm.ID) AS NumReplies,
		spc.ResolverID
	FROM staff_pm_conversations AS spc
	JOIN staff_pm_messages spm ON spm.ConvID = spc.ID
	$WhereCondition
	GROUP BY spc.ID
	ORDER BY $SortStr spc.Level DESC, spc.Date DESC
	LIMIT $Limit
");

$DB->query('SELECT FOUND_ROWS()');
list($NumResults) = $DB->next_record();
$DB->set_query_id($StaffPMs);

$CurURL = Format::get_url();
if (empty($CurURL)) {
    $CurURL = 'staffpm.php?';
} else {
    $CurURL = "staffpm.php?$CurURL&";
}
$Pages = Format::get_pages($Page, $NumResults, MESSAGES_PER_PAGE, 9);

$Row = 'a';

// Start page
?>
<div class="thin">
    <div class="header">
        <h2><?= $ViewString ?><?= Lang::get('staffpm', 'space_staff_pms') ?></h2>
        <div class="linkbox">
            <? if ($IsStaff) { ?>
                <a href="staffpm.php" class="brackets"><?= Lang::get('staffpm', 'view_your_unanswered') ?></a>
            <?  } ?>
            <a href="staffpm.php?view=unanswered" class="brackets"><?= Lang::get('staffpm', 'view_all_unanswered') ?></a>
            <a href="staffpm.php?view=open" class="brackets"><?= Lang::get('staffpm', 'view_unresolved') ?></a>
            <a href="staffpm.php?view=resolved" class="brackets"><?= Lang::get('staffpm', 'view_resolved') ?></a>
            <? if ($IsStaff) { ?>
                <a href="staffpm.php?action=scoreboard" class="brackets"><?= Lang::get('staffpm', 'view_scoreboard') ?></a>
            <?  }

            if ($IsFLS && !$IsStaff) { ?>
                <span class="tooltip" title="This is the inbox where replies to Staff PMs you have sent are."><a href="staffpm.php?action=userinbox" class="brackets"><?= Lang::get('staffpm', 'personal_staff_inbox') ?></a></span>
            <?  } ?>
        </div>
    </div>
    <br />
    <br />
    <div class="linkbox">
        <?= $Pages ?>
    </div>
    <div class="box pad" id="inbox">
        <?

        if (!$DB->has_results()) {
            // No messages
        ?>
            <h2><?= Lang::get('staffpm', 'no_messages') ?></h2>
            <?

        } else {
            // Messages, draw table
            if ($ViewString != 'Resolved' && $IsStaff) {
                // Open multiresolve form
            ?>
                <form class="manage_form" name="staff_messages" method="post" action="staffpm.php" id="messageform">
                    <input type="hidden" name="action" value="multiresolve" />
                    <input type="hidden" name="view" value="<?= strtolower($View) ?>" />
                <?
            }

            // Table head
                ?>
                <div class="table_container border">
                    <table class="message_table<?= ($ViewString != 'Resolved' && $IsStaff) ? ' checkboxes' : '' ?>">
                        <tr class="colhead">
                            <? if ($ViewString != 'Resolved' && $IsStaff) { ?>
                                <td width="10"><input type="checkbox" onclick="toggleChecks('messageform', this);" /></td>
                            <?  } ?>
                            <td width="50%"><?= Lang::get('staffpm', 'subject') ?></td>
                            <td><?= Lang::get('staffpm', 'sender') ?></td>
                            <td><?= Lang::get('staffpm', 'date') ?></td>
                            <td><?= Lang::get('staffpm', 'assigned_to') ?></td>
                            <td><?= Lang::get('staffpm', 'replies') ?></td>
                            <? if ($ViewString == 'Resolved') { ?>
                                <td><?= Lang::get('staffpm', 'resolved_by') ?></td>
                            <?  } ?>
                        </tr>
                        <?

                        // List messages
                        while (list($ID, $Subject, $UserID, $Status, $Level, $AssignedToUser, $Date, $Unread, $NumReplies, $ResolverID) = $DB->next_record()) {
                            $Row = $Row === 'a' ? 'b' : 'a';
                            $RowClass = "row$Row";

                            //$UserInfo = Users::user_info($UserID);
                            $UserStr = Users::format_username($UserID, true, true, true, true);

                            // Get assigned
                            if ($AssignedToUser == '') {
                                // Assigned to class
                                $Assigned = ($Level == 0) ? 'First Line Support' : $ClassLevels[$Level]['Name'];
                                // No + on Sysops
                                if ($Assigned != 'Sysop') {
                                    $Assigned .= '+';
                                }
                            } else {
                                // Assigned to user
                                // $UserInfo = Users::user_info($AssignedToUser);
                                $Assigned = Users::format_username($AssignedToUser, true, true, true, true);
                            }

                            // Get resolver
                            if ($ViewString == 'Resolved') {
                                //$UserInfo = Users::user_info($ResolverID);
                                $ResolverStr = Users::format_username($ResolverID, true, true, true, true);
                            }

                            // Table row
                        ?>
                            <tr class="<?= $RowClass ?>">
                                <? if ($ViewString != 'Resolved' && $IsStaff) { ?>
                                    <td class="center"><input type="checkbox" name="id[]" value="<?= $ID ?>" /></td>
                                <?      } ?>
                                <td><a href="staffpm.php?action=viewconv&amp;id=<?= $ID ?>"><?= display_str($Subject) ?></a></td>
                                <td><?= $UserStr ?></td>
                                <td><?= time_diff($Date, 2, true) ?></td>
                                <td><?= $Assigned ?></td>
                                <td><?= $NumReplies - 1 ?></td>
                                <? if ($ViewString == 'Resolved') { ?>
                                    <td><?= $ResolverStr ?></td>
                                <?      } ?>
                            </tr>
                        <?

                            $DB->set_query_id($StaffPMs);
                        } //while

                        // Close table and multiresolve form
                        ?>
                    </table>
                </div>
                <? if ($ViewString != 'Resolved' && $IsStaff) { ?>
                    <div class="submit_div">
                        <input type="submit" value="Resolve selected" />
                    </div>
                </form>
        <?
                }
            } //if (!$DB->has_results())
        ?>
    </div>
    <div class="linkbox">
        <?= $Pages ?>
    </div>
</div>
<?

View::show_footer();

?>