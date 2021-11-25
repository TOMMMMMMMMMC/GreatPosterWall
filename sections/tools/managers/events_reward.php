<?
function eventsRewardLog($UserIDs, $Invites, $InvitesTime, $Tokens, $TokensTime, $Bonus, $BadgeID, $Remark) {
    $Columns = "";
    $Values = "";
    if ($Invites) {
        $Columns .= ", Invites";
        $Values .= ", $Invites";
        if ($InvitesTime) {
            $Columns .= ", InvitesTime";
            $Values .= ", '$InvitesTime'";
        }
    }
    if ($Tokens) {
        $Columns .= ", Tokens";
        $Values .= ", $Tokens";
        if ($TokensTime) {
            $Columns .= ", TokensTime";
            $Values .= ", '$TokensTime'";
        }
    }
    if ($Bonus) {
        $Columns .= ", Bonus";
        $Values .= ", $Bonus";
    }
    if ($BadgeID) {
        $Columns .= ", Badge";
        $Values .= ", $BadgeID";
    }
    G::$DB->query("insert into events_reward_log (UserIDs, ByUserID, Remark$Columns) values ('" . implode(",", $UserIDs) . "', " . G::$LoggedUser['ID'] . ", '$Remark'$Values)");
}
function getUserIDs($UsernamesString) {
    $Usernames = preg_split("/[\s,]+/", $UsernamesString);
    G::$DB->query("select ID from users_main where username in ('" . implode("','", $Usernames) . "')");
    $UserIDs = G::$DB->collect('ID');
    if (count($UserIDs) != count($Usernames)) {
        return false;
    } else {
        return $UserIDs;
    }
}
function addTokens($UserIDs, $Numbers, $Time, $SendToLeechDisabled) {
    $Where = " where id in (" . implode(",", $UserIDs) . ")";
    if (!$SendToLeechDisabled) {
        $Where .= ' and can_leech = 1';
    }
    G::$DB->query("UPDATE users_main SET FLTokens = FLTokens + $Numbers$Where");
    if ($Time) {
        $SQL = "insert into tokens_typed (`EndTime`, `Type`, `UserID`) values ";
        $Values = [];
        foreach ($UserIDs as $UserID) {
            for ($i = 0; $i < $Numbers; $i++) {
                $Values[] = "('$Time', 'time', $UserID)";
            }
        }
        $SQL .= implode(',', $Values);
        G::$DB->query($SQL);
    }
    clearCache($UserIDs, 'user_info_heavy_');
}
function addInvites($UserIDs, $Numbers, $Time) {
    $Where = " where id in (" . implode(",", $UserIDs) . ")";
    G::$DB->query("UPDATE users_main SET Invites = Invites + $Numbers$Where");
    if ($Time) {
        $SQL = "insert into invites_typed (`EndTime`, `Type`, `UserID`) values ";
        $Values = [];
        foreach ($UserIDs as $UserID) {
            for ($i = 0; $i < $Numbers; $i++) {
                $Values[] = "('$Time', 'time', $UserID)";
            }
        }
        $SQL .= implode(',', $Values);
        G::$DB->query($SQL);
    }
    clearCache($UserIDs, 'user_info_heavy_');
}
function addBonus($UserIDs, $Numbers) {
    $Where = " where id in (" . implode(",", $UserIDs) . ")";
    G::$DB->query("UPDATE users_main SET BonusPoints = BonusPoints + $Numbers$Where");
    clearCache($UserIDs, 'user_stats_');
}
function addBadges($UserIDs, $BadgeID) {
    foreach ($UserIDs as $UserID) {
        Badges::gave($UserID, $BadgeID, false);
    }
}
function clearCache($UserIDs, $Label) {
    G::$DB->query("SELECT max(id) from users_main");
    list($MaxID) = G::$DB->next_record();
    for ($i = 1; $i <= $MaxID; $i++) {
        G::$Cache->delete_value($Label . $i);
    }
}
function sendRewardPM($UserIDs, $Invites, $InvitesTime, $Tokens, $TokensTime, $Bonus, $BadgeID) {
    $CHSStrs = [];
    $ENGStrs = [];
    if ($Invites) {
        $CHSStr = "$Invites 枚邀请";
        $ENGStr = "$Invites invite" . ($Invites > 1 ? "s" : "");
        if ($InvitesTime) {
            $CHSStr .= " ($InvitesTime)";
            $ENGStr .= " ($InvitesTime)";
        }
        $CHSStrs[] = $CHSStr;
        $ENGStrs[] = $ENGStr;
    }
    if ($Tokens) {
        $CHSStr = "$Tokens 枚令牌";
        $ENGStr = "$Tokens token" . ($Tokens > 1 ? "s" : "");
        if ($TokensTime) {
            $CHSStr .= " ($TokensTime)";
            $ENGStr .= " ($TokensTime)";
        }
        $CHSStrs[] = $CHSStr;
        $ENGStrs[] = $ENGStr;
    }
    if ($Bonus) {
        $CHSStrs[] = "$Bonus 积分";
        $ENGStrs[] = "$Bonus bonus";
    }
    if ($BadgeID) {
        $Badge = Badges::get_badges_by_id($BadgeID);
        $CHSStrs[] = Lang::get('badges', $Badge['Label'] . '_badge_name', 'chs');
        $ENGStrs[] = Lang::get('badges', $Badge['Label'] . '_badge_name', 'en');
    }
    if (count($CHSStrs)) {
        $PM = '你获得了 ' . implode("、", $CHSStrs) . '。';
        $PM .= "\n\n----------------------------------------------------------------------\n\n";
        $PM .= 'You\'ve got: ' . implode(", ", $ENGStrs) . '.';
        foreach ($UserIDs as $UserID) {
            $UserLang = Lang::getUserLang($UserID);
            Misc::send_pm($UserID, 0, '奖励通知 | Reward notification', $PM);
        }
    }
}
if (!check_perms('users_mod')) {
    error(403);
}
if (isset($_POST['action'])) {
    switch ($_POST['add_all']) {
        case '-1':
            error(403);
            break;
        case '0':
            $Usernames = db_string($_POST['usernames']);
            if (!$Usernames) {
                error("请输入用户名！");
            }
            if (!($UserIDs = getUserIDs($Usernames))) {
                error("用户名有误！");
            }
            break;
        case '1':
            if (isset($_POST['add_disabled'])) {
                $Where = "";
            } else {
                $Where = " where Enabled='1'";
            }
            G::$DB->query("select ID from users_main$Where");
            $UserIDs = G::$DB->collect("ID");
            break;
    }
    if (isset($_POST['tokens'])) {
        if (!check_perms('events_reward_tokens')) error(403);
        if ($_POST['tokens_permanent'] === "" || empty($_POST['tokens_numbers'])) {
            error(403);
        }
        $TokensPermanent = $_POST['tokens_permanent'] ? true : false;
        if (!$TokensPermanent) {
            $TokensTime = db_string($_POST['tokens_time']);
            if (empty($TokensTime)) {
                error(403);
            }
        } else {
            $TokensTime = false;
        }
        $TokensNumbers = intval($_POST['tokens_numbers']);
        if ($TokensNumbers <= 0) {
            error("数量小于等于零！");
        }
        $SendToLeechDisabled = isset($_POST['leechdisabled']);
    }
    if (isset($_POST['invites'])) {
        if (!check_perms('events_reward_invites')) error(403);
        if ($_POST['invites_permanent'] === "" || empty($_POST['invites_numbers'])) {
            error(403);
        }
        $InvitesPermanent = $_POST['invites_permanent'] ? true : false;
        if (!$InvitesPermanent) {
            $InvitesTime = db_string($_POST['invites_time']);
            if (empty($InvitesTime)) {
                error(403);
            }
        } else {
            $InvitesTime = false;
        }
        $InvitesNumbers = intval($_POST['invites_numbers']);
        if ($InvitesNumbers <= 0) {
            error("数量小于等于零！");
        }
    }
    if (isset($_POST['bonus'])) {
        if (!check_perms('events_reward_bonus')) error(403);
        if (empty($_POST['bonus_numbers'])) {
            error(403);
        }
        $BonusNumbers = intval($_POST['bonus_numbers']);
        if ($BonusNumbers <= 0) {
            error("数量小于等于零！");
        }
    }
    if (isset($_POST['badges'])) {
        if (!check_perms('events_reward_badges')) error(403);
        if (empty($_POST['badgeid'])) {
            error(403);
        }
        $BadgeID = intval($_POST['badgeid']);
    }
    $PM = db_string($_POST['pm']);
    eventsRewardLog($UserIDs, $InvitesNumbers, $InvitesTime, $TokensNumbers, $TokensTime, $BonusNumbers, $BadgeID, $PM);
    if (isset($_POST['tokens'])) {
        addTokens($UserIDs, $TokensNumbers, $TokensTime, $SendToLeechDisabled);
    }
    if (isset($_POST['invites'])) {
        addInvites($UserIDs, $InvitesNumbers, $InvitesTime);
    }
    if (isset($_POST['bonus'])) {
        addBonus($UserIDs, $BonusNumbers);
    }
    if (isset($_POST['badges'])) {
        addBadges($UserIDs, $BadgeID);
    }
    sendRewardPM($UserIDs, $InvitesNumbers, $InvitesTime, $TokensNumbers, $TokensTime, $BonusNumbers, $BadgeID);
    header("Location: tools.php?action=events_reward");
    exit();
}
View::show_header(Lang::get('tools', 'events_reward'));

$Badges = Badges::get_badges_by_id();
$BadgeLabels = Badges::get_badge_labels();
?>

<div class="box pad">
    <script>
        function check() {
            var str = '发放用户：'
            if ($('#add_all_1').prop('checked')) {
                if ($('#add_disabled').prop('checked')) {
                    str += '全体用户'
                } else {
                    str += '全体启用用户'
                }
            } else if ($('#add_all_0').prop('checked')) {
                str += $('#usernames').val() + ' (' + $('#usernames').val().split(/[\s,]+/).length + ')'

            } else {
                return false;
            }
            if ($('#someone_tokens_input').prop('checked')) {
                str += '\n\n'
                if ($('#tokens_permanent_1').prop('checked')) {
                    str += '发放永久令牌'
                } else if ($('#tokens_permanent_0').prop('checked')) {
                    str += '发放临时令牌 ('
                    str += $('#tokens_time').val() + ')'
                } else {
                    return false;
                }
                if ($('#leechdisabled').prop('checked')) {
                    str += ' (包括禁止下载用户)'
                } else {
                    str += ' (不包括禁止下载用户)'
                }
                str += '：' + $('#tokens_numbers').val()
            }
            if ($('#someone_invites_input').prop('checked')) {
                str += '\n\n'
                if ($('#invites_permanent_1').prop('checked')) {
                    str += '发放永久邀请'
                } else if ($('#invites_permanent_0').prop('checked')) {
                    str += '发放临时邀请 ('
                    str += $('#invites_time').val() + ')'
                } else {
                    return false;
                }
                str += '：' + $('#invites_numbers').val()
            }
            if ($('#someone_bonus_input').prop('checked')) {
                str += '\n\n'
                str += '发放积分：' + $('#bonus_numbers').val()
            }
            if ($('#someone_badges_input').prop('checked')) {
                str += '\n\n'
                str += '发放印记：' + $("#badgeid").find("option:selected").text()
            }
            str += '\n\n理由：' + $("#pm").val()
            return confirm(str);
        }
    </script>
    <form method="post" onsubmit="return check()">
        <input type="hidden" name="action" value="events_reward" />
        <div class="header">
            <h2><?= Lang::get('tools', 'events_reward') ?></h2>
        </div>
        <div class="linkbox">
            <div class="center"><a href="tools.php?action=events_reward_history" class="brackets"><?= Lang::get('tools', 'events_reward_history') ?></a></div>
        </div>
        <div id="add_to">
            <span><?= Lang::get('tools', 'add_to') ?>:</span>
            <label><input id="add_all_1" type="radio" name="add_all" value="1" onclick="$('.add_disabled').show();$('#usernames').hide()"><?= Lang::get('tools', 'add_all') ?></label>
            <label><input id="add_all_0" type="radio" name="add_all" value="0" onclick="$('.add_disabled').hide();$('#usernames').show()"><?= Lang::get('tools', 'add_part') ?></label>
            <input class="add_disabled" id="add_disabled" name="add_disabled" style="display: none;" type="checkbox"><label style="display: none;" class="add_disabled" for="add_disabled"><?= Lang::get('tools', 'add_to_disabled_users') ?></label>
            <textarea id="usernames" name="usernames" style="display: none;" placeholder="<?= Lang::get('tools', 'add_to_placeholder') ?>"></textarea>
        </div>
        <? if (check_perms('events_reward_tokens')) { ?>
            <div class="events_reward_container">
                <div class="head">
                    </label><input type="checkbox" id="someone_tokens_input" name="tokens" onclick="$('#someone_tokens').toggle()"><label for="someone_tokens_input"><?= Lang::get('tools', 'someone_tokens') ?>
                </div>
                <div class="body" id="someone_tokens" style="display: none;">
                    <div>
                        <span><?= Lang::get('tools', 'token_type') ?>:</span>
                        <label><input id="tokens_permanent_1" type="radio" name="tokens_permanent" value="1" onclick="$('#tokens_time').hide()"><?= Lang::get('tools', 'permanent') ?></label>
                        <label><input id="tokens_permanent_0" type="radio" name="tokens_permanent" value="0" onclick="$('#tokens_time').show()"><?= Lang::get('tools', 'temporary') ?></label>
                        <input id="tokens_time" type="date" name="tokens_time" style="display: none;" />
                    </div>
                    <div><span><?= Lang::get('tools', 'token_number') ?>:</span>
                        <input id="tokens_numbers" name="tokens_numbers" type="number">
                    </div>
                    <div><input id="leechdisabled" name="leechdisabled" type="checkbox"><label for="leechdisabled"><?= Lang::get('tools', 'leechdisabled') ?></label< /div>
                    </div>
                </div>
            </div>
        <? }
        if (check_perms('events_reward_invites')) { ?>
            <div class="events_reward_container">
                <div class="head">
                    <input type="checkbox" id="someone_invites_input" name="invites" onclick="$('#someone_invites').toggle()"><label for="someone_invites_input"><?= Lang::get('tools', 'someone_invites') ?></label>
                </div>
                <div class="body" id="someone_invites" style="display: none;">
                    <div>
                        <span><?= Lang::get('tools', 'invites_type') ?>:</span>
                        <label><input id="invites_permanent_1" type="radio" name="invites_permanent" value="1" onclick="$('#invites_time').hide()"><?= Lang::get('tools', 'permanent') ?></label>
                        <label><input id="invites_permanent_0" type="radio" name="invites_permanent" value="0" onclick="$('#invites_time').show()"><?= Lang::get('tools', 'temporary') ?></label>
                        <input id="invites_time" type="date" name="invites_time" style="display: none;" />
                    </div>
                    <div><span><?= Lang::get('tools', 'invites_number') ?>:</span>
                        <input id="invites_numbers" name="invites_numbers" type="number">
                    </div>
                </div>
            </div>
        <? }
        if (check_perms('events_reward_bonus')) { ?>
            <div class="events_reward_container">
                <div class="head">
                    <input type="checkbox" id="someone_bonus_input" name="bonus" onclick="$('#someone_bonus').toggle()"><label for="someone_bonus_input"><?= Lang::get('tools', 'someone_bonus') ?></label>
                </div>
                <div class="body" id="someone_bonus" style="display: none;">
                    <div><span><?= Lang::get('tools', 'bonus_number') ?>:</span>
                        <input id="bonus_numbers" name="bonus_numbers" type="number">
                    </div>
                </div>
            </div>
        <? }
        if (check_perms('events_reward_badges') && ENABLE_BADGE) { ?>
            <div class="events_reward_container">
                <div class="head">
                    <input type="checkbox" id="someone_badges_input" name="badges" onclick="$('#someone_badges').toggle()"><label for="someone_badges_input"><?= Lang::get('tools', 'badge_send') ?></label>
                </div>
                <div class="body" id="someone_badges" style="display: none;">
                    <div><span><?= Lang::get('tools', 'badge_send') ?>:</span>
                        <select id="badgeid" name='badgeid'>
                            <?
                            foreach ($Badges as $Badge) {
                                echo "<option value='" . $Badge['ID'] . "'>" . $Badge['Level'] . "-" . $BadgeLabels[$Badge['Label']]['Remark'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
        <? } ?>
        <div class="events_reward_container">
            <div class="head">
                <?= Lang::get('tools', 'events_reward_pm') ?>
            </div>
            <div class="body" class="box pad">
                <span><?= Lang::get('tools', 'reason') ?>:</span>
                <input id="pm" type="text" name="pm" placeholder="<?= Lang::get('tools', 'reason_placeholder') ?>" size="80" />
            </div>
        </div>
        <div class="center"><input type="submit" value="<?= Lang::get('tools', 'send_rewards') ?>"></div>
    </form>
</div>
<?
View::show_footer()
?>