<?

if (isset($_GET['userid']) && check_perms('site_can_invite_always')) {
    if (!is_number($_GET['userid'])) {
        error(403);
    }

    $UserID = $_GET['userid'];
    $Sneaky = true;
} else {
    if (!$UserCount = $Cache->get_value('stats_user_count')) {
        $DB->query("
			SELECT COUNT(ID)
			FROM users_main
			WHERE Enabled = '1'");
        list($UserCount) = $DB->next_record();
        $Cache->cache_value('stats_user_count', $UserCount, 0);
    }

    $UserID = $LoggedUser['ID'];
    $Sneaky = false;
}

list($UserID, $Username, $PermissionID) = array_values(Users::user_info($UserID));


$DB->query("
	SELECT InviteKey, Email, Expires
	FROM invites
	WHERE InviterID = '$UserID'
	ORDER BY Expires");
$Pending =  $DB->to_array();

$OrderWays = array('username', 'email', 'joined', 'lastseen', 'uploaded', 'downloaded', 'ratio');

if (empty($_GET['order'])) {
    $CurrentOrder = 'id';
    $CurrentSort = 'asc';
    $NewSort = 'desc';
} else {
    if (in_array($_GET['order'], $OrderWays)) {
        $CurrentOrder = $_GET['order'];
        if ($_GET['sort'] == 'asc' || $_GET['sort'] == 'desc') {
            $CurrentSort = $_GET['sort'];
            $NewSort = ($_GET['sort'] == 'asc' ? 'desc' : 'asc');
        } else {
            error(404);
        }
    } else {
        error(404);
    }
}

switch ($CurrentOrder) {
    case 'username':
        $OrderBy = "um.Username";
        break;
    case 'email':
        $OrderBy = "um.Email";
        break;
    case 'joined':
        $OrderBy = "ui.JoinDate";
        break;
    case 'lastseen':
        $OrderBy = "um.LastAccess";
        break;
    case 'uploaded':
        $OrderBy = "um.Uploaded";
        break;
    case 'downloaded':
        $OrderBy = "um.Downloaded";
        break;
    case 'ratio':
        $OrderBy = "(um.Uploaded / um.Downloaded)";
        break;
    default:
        $OrderBy = "um.ID";
        break;
}

$CurrentURL = Format::get_url(array('action', 'order', 'sort'));

$DB->query("
	SELECT
		ID,
		Email,
		Uploaded,
		Downloaded,
		JoinDate,
		LastAccess
	FROM users_main AS um
		LEFT JOIN users_info AS ui ON ui.UserID = um.ID
	WHERE ui.Inviter = '$UserID'
	ORDER BY $OrderBy $CurrentSort");

$Invited = $DB->to_array();

View::show_header(Lang::get('user', 'invites'));

?>
<div class="thin">
    <div class="header">
        <h2><?= Users::format_username($UserID, false, false, false) ?> &gt; <?= Lang::get('user', 'invites') ?></h2>
        <div class="linkbox">
            <a href="user.php?action=invitetree<? if ($Sneaky) {
                                                    echo '&amp;userid=' . $UserID;
                                                } ?>" class="brackets"><?= Lang::get('user', 'invite_tree') ?></a>
        </div>
    </div>
    <? if ($UserCount >= USER_LIMIT && !check_perms('site_can_invite_always')) { ?>
        <div class="box pad notice">
            <p><?= Lang::get('user', 'because_the_user_limit_has_been_reached_you_are_unable_to_send_invites_at_this_time') ?></p>
        </div>
    <? }

    /*
    Users cannot send invites if they:
        -Are on ratio watch
        -Have disabled leeching
        -Have disabled invites
        -Have no invites (Unless have unlimited)
        -Cannot 'invite always' and the user limit is reached
*/

    $DB->query("
	SELECT can_leech
	FROM users_main
	WHERE ID = $UserID");
    list($CanLeech) = $DB->next_record();


    if (
        !$Sneaky
        && !$LoggedUser['RatioWatch']
        && $CanLeech
        && empty($LoggedUser['DisableInvites'])
        && check_perms('site_can_invite')
        && ($LoggedUser['Invites'] > 0 || check_perms('site_send_unlimited_invites'))
        && ($UserCount <= USER_LIMIT || USER_LIMIT == 0 || check_perms('site_can_invite_always'))
    ) { ?>
        <div class="box pad" id="invite_rules_container">
            <li><?= Lang::get('user', 'invite_rules_1') ?></li>
            <li><?= Lang::get('user', 'invite_rules_2') ?></li>
            <li><?= Lang::get('user', 'invite_rules_3') ?></li>
            <li><?= Lang::get('user', 'invite_rules_4') ?></li>
            <li><?= Lang::get('user', 'invite_rules_5') ?></li>
            </ul>
            <br>
            <strong class="important_text"><?= Lang::get('user', 'invite_rules_6') ?></strong><br>
            <br><?= Lang::get('user', 'invite_rules_7') ?>
        </div>
        <div class="box box2">
            <form class="send_form pad" name="invite" action="user.php" method="post">
                <input type="hidden" name="action" value="take_invite" />
                <input type="hidden" name="auth" value="<?= $LoggedUser['AuthKey'] ?>" />
                <div class="field_div">
                    <div class="label"><?= Lang::get('user', 'email_address') ?>:</div>
                    <div class="input">
                        <input type="email" name="email" size="60" />
                        <input type="submit" value="Invite" />
                    </div>
                </div>
                <? if (check_perms('users_invite_notes')) { ?>
                    <div class="field_div">
                        <div class="label"><?= Lang::get('user', 'invite_note') ?>:</div>
                        <div class="input">
                            <input type="text" name="reason" size="60" maxlength="255" />
                        </div>
                    </div>
                <?  } ?>
            </form>
        </div>

    <?
    } elseif (!empty($LoggedUser['DisableInvites'])) { ?>
        <div class="box pad" style="text-align: center;">
            <strong class="important_text"><?= Lang::get('user', 'your_invites_have_been_disabled') ?></strong>
        </div>
    <?
    } elseif ($LoggedUser['RatioWatch'] || !$CanLeech) { ?>
        <div class="box pad" style="text-align: center;">
            <strong class="important_text"><?= Lang::get('user', 'you_may_not_send_invites_while_on_ratio_watch_or') ?></strong>
        </div>
    <?
    }

    if (!empty($Pending)) {
    ?>
        <h3 id="pending_invites_header"><?= Lang::get('user', 'pending_invites') ?></h3>
        <div class="box pad" id="pending_invites_container">
            <table width="100%">
                <tr class="colhead">
                    <td><?= Lang::get('user', 'email_address') ?></td>
                    <td><?= Lang::get('user', 'expires_in') ?></td>
                    <td><?= Lang::get('user', 'invite_link') ?></td>
                    <td><?= Lang::get('user', 'delete_invite') ?></td>
                </tr>
                <?
                $Row = 'a';
                foreach ($Pending as $Invite) {
                    list($InviteKey, $Email, $Expires) = $Invite;
                    $Row = $Row === 'a' ? 'b' : 'a';
                ?>
                    <tr class="row<?= $Row ?>">
                        <td><?= display_str($Email) ?></td>
                        <td><?= time_diff($Expires) ?></td>
                        <td><a href="register.php?invite=<?= $InviteKey ?>"><?= Lang::get('user', 'invite_link') ?></a></td>
                        <td><a href="user.php?action=delete_invite&amp;invite=<?= $InviteKey ?>&amp;auth=<?= $LoggedUser['AuthKey'] ?>" onclick="return confirm('<?= Lang::get('user', 'are_you_sure_you_want_to_delete_this_invite') ?>');"><?= Lang::get('user', 'delete_invite') ?></a></td>
                    </tr>
                <?  } ?>
            </table>
        </div>
    <?
    }

    ?>
    <h3 id="invite_table_header"><?= Lang::get('user', 'invitee_list') ?></h3>
    <div class="box pad" id="invite_table_container">
        <table class="invite_table m_table " width="100%">
            <tr class="colhead">
                <td class="m_th_left"><a href="user.php?action=invite&amp;order=username&amp;sort=<?= (($CurrentOrder == 'username') ? $NewSort : 'desc') ?>&amp;<?= $CurrentURL ?>"><?= Lang::get('user', 'username') ?></a></td>
                <td><a href="user.php?action=invite&amp;order=email&amp;sort=<?= (($CurrentOrder == 'email') ? $NewSort : 'desc') ?>&amp;<?= $CurrentURL ?>"><?= Lang::get('user', 'email') ?></a></td>
                <td><a href="user.php?action=invite&amp;order=joined&amp;sort=<?= (($CurrentOrder == 'joined') ? $NewSort : 'desc') ?>&amp;<?= $CurrentURL ?>"><?= Lang::get('user', 'joined') ?></a></td>
                <td><a href="user.php?action=invite&amp;order=lastseen&amp;sort=<?= (($CurrentOrder == 'lastseen') ? $NewSort : 'desc') ?>&amp;<?= $CurrentURL ?>"><?= Lang::get('user', 'last_seen') ?></a></td>
                <td class="m_th_right"><a href="user.php?action=invite&amp;order=uploaded&amp;sort=<?= (($CurrentOrder == 'uploaded') ? $NewSort : 'desc') ?>&amp;<?= $CurrentURL ?>"><?= Lang::get('user', 'upload') ?></a></td>
                <td class="m_th_right"><a href="user.php?action=invite&amp;order=downloaded&amp;sort=<?= (($CurrentOrder == 'downloaded') ? $NewSort : 'desc') ?>&amp;<?= $CurrentURL ?>"><?= Lang::get('global', 'download') ?></a></td>
                <td class="m_th_right"><a href="user.php?action=invite&amp;order=ratio&amp;sort=<?= (($CurrentOrder == 'ratio') ? $NewSort : 'desc') ?>&amp;<?= $CurrentURL ?>"><?= Lang::get('user', 'ratio') ?></a></td>
            </tr>
            <?
            $Row = 'a';
            foreach ($Invited as $User) {
                list($ID, $Email, $Uploaded, $Downloaded, $JoinDate, $LastAccess) = $User;
                $Row = $Row === 'a' ? 'b' : 'a';
            ?>
                <tr class="row<?= $Row ?>">
                    <td class="td_username"><?= Users::format_username($ID, true, true, true, true) ?></td>
                    <td class="td_email"><?= display_str($Email) ?></td>
                    <td class="td_join_date"><?= time_diff($JoinDate, 1) ?></td>
                    <td class="td_last_access"><?= time_diff($LastAccess, 1); ?></td>
                    <td class="td_up m_td_right"><?= Format::get_size($Uploaded) ?></td>
                    <td class="td_dl m_td_right"><?= Format::get_size($Downloaded) ?></td>
                    <td class="td_ratio m_td_right"><?= Format::get_ratio_html($Uploaded, $Downloaded) ?></td>
                </tr>
            <?  } ?>
        </table>
    </div>
</div>
<? View::show_footer(); ?>