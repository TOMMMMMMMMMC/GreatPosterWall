<?
View::show_header(Lang::get('apply', 'view_applications'), 'apply');
$IS_STAFF = check_perms('admin_manage_applicants'); /* important for viewing the full story and full applicant list */
if (isset($_POST['id']) && is_number($_POST['id'])) {
    authorize();
    $ID = intval($_POST['id']);
    $App = Applicant::factory($ID);
    if (!$IS_STAFF && $App->user_id() != $LoggedUser['ID']) {
        error(403);
    }
    $note_delete = array_filter($_POST, function ($x) {
        return preg_match('/^note-delete-\d+$/', $x);
    }, ARRAY_FILTER_USE_KEY);
    if (is_array($note_delete) && count($note_delete) == 1) {
        $App->delete_note(
            trim(array_keys($note_delete)[0], 'note-delete-')
        );
    } elseif (isset($_POST['resolve'])) {
        if ($_POST['resolve'] === 'Resolve') {
            $App->resolve(true);
        } elseif ($_POST['resolve'] === 'Reopen') {
            $App->resolve(false);
        }
    } elseif (isset($_POST['note_reply'])) {
        $App->save_note(
            $LoggedUser['ID'],
            $_POST['note_reply'],
            $IS_STAFF && $_POST['visibility'] == 'staff' ? 'staff' : 'public'
        );
    }
} elseif (isset($_GET['id']) && is_number($_GET['id'])) {
    $ID = intval($_GET['id']);
    $App = Applicant::factory($ID);
    if (!$IS_STAFF && $App->user_id() != $LoggedUser['ID']) {
        error(403);
    }
}
$Resolved = (isset($_GET['status']) && $_GET['status'] === 'resolved');
?>

<div class="thin">
    <div class="linkbox">
        <a href="/apply.php" class="brackets"><?= Lang::get('apply', 'apply') ?></a>
        <? if (!$IS_STAFF && isset($ID)) { ?>
            <a href="/apply.php?action=view" class="brackets"><?= Lang::get('apply', 'view_your_application') ?></a>
            <?
        }
        if ($IS_STAFF) {
            if ($Resolved || (!$Resolved and isset($ID))) {
            ?>
                <a href="/apply.php?action=view" class="brackets"><?= Lang::get('apply', 'current_applications') ?></a>
            <?
            }
            if (!$Resolved) {
            ?>
                <a href="/apply.php?action=view&status=resolved" class="brackets"><?= Lang::get('apply', 'resolved_applications') ?></a>
            <?  } ?>
            <a href="/apply.php?action=admin" class="brackets"><?= Lang::get('apply', 'manage_roles') ?></a>
        <?
        }
        ?>
    </div>

    <? if (isset($ID)) { ?>
        <div class="box" id="user_application_reply_box">
            <div class="head" <?= $App->is_resolved() ? ' style="font-style: italic;"' : '' ?>><?= $App->role_title() ?>
                <? if ($IS_STAFF) { ?>
                    <div style="float: right;">
                        <form name="role_resolve" method="POST" action="/apply.php?action=view&amp;id=<?= $ID ?>">
                            <input type="submit" name="resolve" value="<?= $App->is_resolved() ? 'Reopen' : 'Resolve' ?>" />
                            <input type="hidden" name="id" value="<?= $ID ?>" />
                            <input type="hidden" name="auth" value="<?= $LoggedUser['AuthKey'] ?>" />
                        </form>
                    </div>
                    <br /><?= Lang::get('apply', 'application_received_from_1') ?><?= Users::format_username($App->user_id(), true, true, true, true, true, false) ?><?= Lang::get('apply', 'application_received_from_2') ?><?= time_diff($App->created(), 2) ?><?= Lang::get('apply', 'application_received_from_3') ?>
                <?  } ?>
            </div>

            <div class="pad">
                <div id="user_application_reply_text">
                    <p><?= Text::full_format($App->body()) ?></p>
                </div>
                <? if (!$App->is_resolved()) { ?>
                    <form id="thread_note_reply" name="thread_note_replay" method="POST" action="/apply.php?action=view&amp;id=<?= $ID ?>">
                    <?  } ?>
                    <div class="post_container border">
                        <table class="forum_post wrap_overflow box vertical_margin">
                            <?
                            foreach ($App->get_story() as $note) {
                                if (!$IS_STAFF && $note['visibility'] == 'staff') {
                                    continue;
                                }
                                $UserName = (!$IS_STAFF && $note['user_id'] != $LoggedUser['ID']) ? "Staff" : Users::format_username($note['user_id'], true, true, true, true, true, false)
                            ?>
                                <tr class="colhead_dark">
                                    <td colspan="2">
                                        <div style="float: left; padding-top: 10px;"><?= $UserName ?>
                                            - <?= time_diff($note['created'], 2) ?></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2" style="border: 2px solid <?= $IS_STAFF ? ($note['visibility'] == 'staff' ? '#FF8017' : '#347235') : '#808080' ?>;">
                                        <div style="margin: 5px 4px 20px 4px">
                                            <?= Text::full_format($note['body']) ?>
                                        </div>
                                        <? if ($IS_STAFF && !$App->is_resolved()) { ?>
                                            <div style="float: right; padding-top: 10px 0; margin-bottom: 6px;">
                                                <input type="submit" name="note-delete-<?= $note['id'] ?>" value="delete" style="height: 20px; padding: 0 3px;" />
                                            </div>
                                        <?      } ?>
                                    </td>
                                </tr>
                                <?
                            } /* foreach */
                            if (!$App->is_resolved()) {
                                if ($IS_STAFF) {
                                ?>
                                    <tr>
                                        <td class="label"><?= Lang::get('apply', 'visibility') ?></td>
                                        <td>
                                            <div>
                                                <input type="radio" name="visibility" value="public" /><label for="public"><?= Lang::get('apply', 'public') ?> <span style="color: #347235">(<?= Lang::get('apply', 'member_will_see_this_reply') ?>)</span></label><br />
                                                <input type="radio" name="visibility" value="staff" checked /><label for="staff"><?= Lang::get('apply', 'staff') ?> <span style="color: #FF8017">(<?= Lang::get('apply', 'only_staff_will_see_this_reply') ?>)</span></label><br />
                                            </div>
                                        </td>
                                    </tr>
                                <?      } /* $IS_STAFF */ ?>
                                <tr>
                                    <td class="label"><?= Lang::get('apply', 'reply') ?></td>
                                    <td>
                                        <?
                                        $reply = new TEXTAREA_PREVIEW('note_reply', 'note_reply', '', 60, 8, false, false);
                                        echo $reply->preview();
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <div style="text-align: center;">
                                            <input type="hidden" name="id" value="<?= $ID ?>" />
                                            <input type="hidden" name="auth" value="<?= $LoggedUser['AuthKey'] ?>" />
                                            <input type="button" value="Preview" class="hidden button_preview_<?= $reply->getId() ?>" />
                                            <input type="submit" id="submit" value="Save" />
                                        </div>
                                    </td>
                                </tr>
                            <?  } /* !$App->is_resolved() */ ?>
                        </table>
                    </div>
                    </form>
            </div>
        </div>
    <?
    } else { /* no id parameter given -- show list of applicant entries - all if staff, otherwise their own (if any) */
        $Page            = isset($_GET['page']) && is_number($_GET['page']) ? intval($_GET['page']) : 1;
        $UserID          = $IS_STAFF ? 0 : $LoggedUser['ID'];
        $ApplicationList = Applicant::get_list($Page, $Resolved, $UserID);
    ?>
        <h2><?= $Resolved ? Lang::get('apply', 'resolved') : Lang::get('apply', 'current') ?><?= Lang::get('apply', 'blank_applications') ?></h2>
        <? if (count($ApplicationList)) { ?>
            <div class="table_container border">
                <table>
                    <tr class="colhead">
                        <td><?= Lang::get('apply', 'role') ?></td>
                        <? if ($IS_STAFF) { ?>
                            <td><?= Lang::get('apply', 'applicant') ?></td>
                        <?      } ?>
                        <td><?= Lang::get('apply', 'date_created') ?></td>
                        <td><?= Lang::get('apply', 'comments') ?></td>
                        <td><?= Lang::get('apply', 'last_comment_from') ?></td>
                        <td><?= Lang::get('apply', 'last_comment_added') ?></td>
                    </tr>
                    <? foreach ($ApplicationList as $appl) { ?>
                        <tr>
                            <td><a href="/apply.php?action=view&amp;id=<?= $appl['ID'] ?>"><?= $appl['Role'] ?></a></td>
                            <? if ($IS_STAFF) { ?>
                                <td><a href="/user.php?id=<?= $appl['UserID'] ?>"><?= $appl['Username'] ?></a></td>
                            <?      } ?>
                            <td><?= time_diff($appl['Created'], 2) ?></td>
                            <td><?= $appl['nr_notes'] ?></td>
                            <td><a href="/user.php?id=<?= $appl['last_UserID'] ?>"><?= $appl['last_Username'] ?></a></td>
                            <td><?= strlen($appl['last_Created']) ? time_diff($appl['last_Created'], 2) : '' ?></td>
                        </tr>
                    <?  } /* foreach */ ?>
                </table>
            </div>
        <?
        } /* count($ApplicationList) > 0 */ else {
        ?>
            <div class="box pad"><?= Lang::get('apply', 'the_cupboard_is_empty') ?></div>
    <?
        } /* no applications */
    } /* show list of applicant entries */
    ?>

</div>

<?
View::show_footer();
