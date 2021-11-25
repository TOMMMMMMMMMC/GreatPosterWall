<?
if (isset($_POST['auth'])) {
    authorize();
    $Role = array_key_exists('role', $_POST) ? trim($_POST['role']) : '';
    $Body = array_key_exists('body', $_POST) ? trim($_POST['body']) : '';

    if (strlen($Role)) {
        if (strlen($Body) > 80) {
            $Applicant = new Applicant($LoggedUser['ID'], $Role, $Body);
            header('Location: /apply.php?action=view&id=' . $Applicant->id());
            exit;
        } else {
            $Error = Lang::get('apply', 'you_need_explain_more');
        }
    } else {
        $Error = Lang::get('apply', 'you_need_choose_role');
    }
} else {
    $Role = '';
    $Body = '';
}
View::show_header(Lang::get('apply', 'apply'), 'apply,bbcode');
?>

<div class="thin">
    <div class="header">
        <h2><?= Lang::get('apply', 'apply_for_a_role_at_before') ?><?= SITE_NAME ?><?= Lang::get('apply', 'apply_for_a_role_at_after') ?></h2>
        <? if (check_perms('admin_manage_applicants') || Applicant::user_is_applicant($LoggedUser['ID'])) { ?>
            <div class="linkbox">
                <? if (check_perms('admin_manage_applicants')) { ?>
                    <a href="/apply.php?action=view" class="brackets"><?= Lang::get('apply', 'current_applications') ?></a>
                    <a href="/apply.php?action=view&status=resolved" class="brackets"><?= Lang::get('apply', 'resolved_applications') ?></a>
                    <a href="/apply.php?action=admin" class="brackets"><?= Lang::get('apply', 'manage_roles') ?></a>
                <? }
                if (Applicant::user_is_applicant($LoggedUser['ID'])) { ?>
                    <a href="/apply.php?action=view" class="brackets"><?= Lang::get('apply', 'view_your_application') ?></a>
                <? } ?>
            </div>
        <? } ?>
    </div>
    <div class="thin">
        <ur>
            <li><?= Lang::get('apply', 'referral_note') ?></li>
            </ul>
    </div>
    <?php
    $Roles = ApplicantRole::get_list();
    if (count($Roles)) { ?>
        <div class="box" id="role_box">
            <div class="head"><?= Lang::get('apply', 'open_roles') ?></div>
            <div class="pad">
                <table>
                    <? foreach ($Roles as $title => $info) { ?>
                        <tr>
                            <td>
                                <div class="role_container">
                                    <div class="head"><?= $title ?></div>
                                    <div class="pad"><?= Text::full_format($info['description']) ?></div>
                                </div>
                            </td>
                        </tr>
                    <?  } /* foreach */ ?>
                </table>
            </div>
        </div>
    <? } ?>

    <? if (count($Roles) == 0) { ?>
        <div class="box pad">
            <p><?= Lang::get('apply', 'thanks_for_your_interest_in_helping_dic') ?></p>
        </div>
        <?
    } else {
        if ($Error) {
        ?>
            <div class="important"><?= $Error ?></div>
        <?
        }
        ?>
        <form class="send_form" id="applicationform" name="apply" action="/apply.php?action=save" method="post">
            <div class="box">
                <div id="quickpost">
                    <div class="head"><?= Lang::get('apply', 'your_role_at_before') ?><?= SITE_NAME ?><?= Lang::get('apply', 'your_role_at_after') ?></div>
                    <div class="pad">
                        <div><?= Lang::get('apply', 'choose_a_role_from_the_list') ?>:</div>
                        <select name="role">
                            <option value="">---</option>
                            <? foreach (array_keys($Roles) as $title) { ?>
                                <option value="<?= $title ?>" <?= $Role == $title ? ' selected' : '' ?>><?= $title ?></option>
                            <?  } ?>
                        </select>
                    </div>
                    <div class="head"><?= Lang::get('apply', 'your_cover_letter') ?></div>
                    <div class="pad"><?= Lang::get('apply', 'at_least_80_characters') ?>
                        <?
                        $text = new TEXTAREA_PREVIEW('body', 'body', $Body, 95, 20, false, false);
                        echo $text->preview();
                        ?>
                    </div>
                </div>

                <div id="buttons" class="center">
                    <input type="hidden" name="auth" value="<?= $LoggedUser['AuthKey'] ?>" />
                    <input type="button" value="Preview" class="hidden button_preview_<?= $text->getId() ?>" />
                    <input type="submit" value="Send Application" />
                </div>
            </div>
        </form>
    <? } /* else */ ?>
</div>

<? View::show_footer();
