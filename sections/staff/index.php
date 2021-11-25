<?
enforce_login();
View::show_header(Lang::get('staff', 'index'));

include(SERVER_ROOT . '/sections/staff/functions.php');

$SupportStaff = get_support();

$action = $_GET['action'];

list($FrontLineSupport, $Staff) = $SupportStaff;
?>



<div class="thin">
    <div class="header">
        <h2><?= SITE_NAME ?> <?= Lang::get('staff', 'index') ?></h2>
    </div>
    <? if (check_perms('admin_manage_applicants')) { ?>
        <!-- <div class="linkbox">
    <a href="apply.php"><?= Lang::get('staff', 'role_applications') ?></a>
</div> -->
    <?  } ?>
    <div class="box pad">
        <!-- <br /> -->
        <h2><?= Lang::get('staff', 'contact_staff') ?></h2>
        <div id="below_box"><?= Lang::get('staff', 'contact_staff_note') ?>

        </div>
        <? View::parse('generic/reply/staffpm.php', array('Hidden' => $action == 'donate' ? false : true)); ?>
    </div>
    <div class="box pad" id="role-apply">
        <!-- <br /> -->
        <h2 style='display:inline-block'><?= Lang::get('staff', 'role_applications') ?></h2>
        <div style='display:inline'><?= Lang::get('staff', 'role_applications_sub') ?></div>
        <div id="below_box"><?= Lang::get('staff', 'role_applications_note') ?><div><?= Lang::get('apply', 'referral_note') ?></div>
        </div>
        <? View::parse('generic/reply/staffpm.php', array('Hidden' => true)); ?>
    </div>
    <? if (check_perms('show_admin_team')) { ?>
        <div class="box pad">
            <!-- <br /> -->
            <h2><?= Lang::get('staff', 'community_help') ?></h2>
            <?= Lang::get('staff', 'fl_support_note') ?><br />
            <div class="box pad">
                <h3 id="fls" style="font-size: 17px;"><i><?= Lang::get('staff', 'first_line_support') ?></i></h3>
                <table class="staff" width="100%">
                    <tr class="colhead">
                        <td style="width: 130px;"><?= Lang::get('staff', 'username') ?></td>
                        <td style="width: 130px;"><?= Lang::get('staff', 'lastseen') ?></td>
                        <td><strong><?= Lang::get('staff', 'support') ?></strong></td>
                    </tr>
                    <?
                    $Row = 'a';
                    foreach ($FrontLineSupport as $Support) {
                        list($ID, $Class, $Username, $Paranoia, $LastAccess, $SupportFor) = $Support;

                        $Row = make_staff_row($Row, $ID, $Paranoia, $Class, $LastAccess, $SupportFor);
                    } ?>
                </table>
                <!-- <br /> -->
                <h3 style="font-size: 17px;" id="fls"><i><?= Lang::get('staff', 'torrent_inspector') ?></i></h3>
                <table class="staff" width="100%">
                    <tr class="colhead">
                        <td style="width: 130px;"><?= Lang::get('staff', 'username') ?></td>
                        <td style="width: 130px;"><?= Lang::get('staff', 'lastseen') ?></td>
                        <td><strong><?= Lang::get('staff', 'support') ?></strong></td>
                    </tr>
                    <?
                    $Row = 'a';
                    $TorrentWatching = get_tw();
                    foreach ($TorrentWatching as $tw) {
                        list($ID, $Class, $Username, $Paranoia, $LastAccess, $SupportFor) = $tw;

                        $Row = make_staff_row($Row, $ID, $Paranoia, $Class, $LastAccess, $SupportFor);
                    } ?>
                </table>
            </div>
            <br />
            <?php

            foreach ($Staff as $SectionName => $StaffSection) {
                if (count($StaffSection) === 0) {
                    continue;
                }
            ?>
                <div class="box pad">
                    <h2 style='text-align: left;'><?= $SectionName ?></h2>
                    <?
                    $CurClass = 0;
                    $CloseTable = false;
                    foreach ($StaffSection as $StaffMember) {
                        list($ID, $ClassID, $Class, $ClassName, $StaffGroup, $Username, $Paranoia, $LastAccess, $Remark) = $StaffMember;
                        if ($Class != $CurClass) { // Start new class of staff members
                            $Row = 'a';
                            if ($CloseTable) {
                                $CloseTable = false;
                                // the "\t" and "\n" are used here to make the HTML look pretty
                                echo "\t\t</table>\n\t\t<br />\n";
                            }
                            $CurClass = $Class;
                            $CloseTable = true;

                            $HTMLID = str_replace(' ', '_', strtolower($ClassName));
                            echo "\t\t<h3 style=\"font-size: 17px;\" id=\"$HTMLID\"><i>" . $ClassName . "s</i></h3>\n";
                    ?>
                            <table class="staff" width="100%">
                                <tr class="colhead">
                                    <td style="width: 130px;"><?= Lang::get('staff', 'username') ?></td>
                                    <td style="width: 130px;"><?= Lang::get('staff', 'lastseen') ?></td>
                                    <td><strong><?= Lang::get('staff', 'remark') ?></strong></td>
                                </tr>
                        <?
                        } // End new class header

                        $HiddenBy = Lang::get('staff', 'hidden_by_staff_member');

                        // Display staff members for this class
                        $Row = make_staff_row($Row, $ID, $Paranoia, $Class, $LastAccess, $Remark, $HiddenBy);
                    } ?>
                            </table>

                </div>
                <br />
        <?php }
        } ?>
        </div>
        <?
        View::show_footer();
        ?>