<?php

if (isset($_GET['search'])) {
    $_GET['search'] = trim($_GET['search']);
}

if (!empty($_GET['search'])) {
    if (preg_match('/^' . IP_REGEX . '$/', $_GET['search'])) {
        $_GET['ip'] = $_GET['search'];
    } elseif (preg_match('/^' . EMAIL_REGEX . '$/i', $_GET['search'])) {
        $_GET['email'] = $_GET['search'];
    } elseif (preg_match(USERNAME_REGEX, $_GET['search'])) {
        $DB->query("
			SELECT ID
			FROM users_main
			WHERE Username = '" . db_string($_GET['search']) . "'");
        if (list($ID) = $DB->next_record()) {
            header("Location: user.php?id=$ID");
            die();
        }
        $_GET['username'] = $_GET['search'];
    } else {
        $_GET['comment'] = $_GET['search'];
    }
}

foreach (array('ip', 'email', 'username', 'comment') as $field) {
    if (isset($_GET[$field])) {
        $_GET[$field] = trim($_GET[$field]);
    }
}

define('USERS_PER_PAGE', 30);

function wrap($String, $ForceMatch = '', $IPSearch = false) {
    if (!$ForceMatch) {
        global $Match;
    } else {
        $Match = $ForceMatch;
    }
    if ($Match == ' REGEXP ') {
        if (strpos($String, '\'') !== false || preg_match('/^.*\\\\$/i', $String)) {
            error(Lang::get('user', 'regex_contains_illegal_characters'));
        }
    } else {
        $String = db_string($String);
    }
    if ($Match == ' LIKE ') {
        // Fuzzy search
        // Stick in wildcards at beginning and end of string unless string starts or ends with |
        if (($String[0] != '|') && !$IPSearch) {
            $String = "%$String";
        } elseif ($String[0] == '|') {
            $String = substr($String, 1, strlen($String));
        }

        if (substr($String, -1, 1) != '|') {
            $String = "$String%";
        } else {
            $String = substr($String, 0, -1);
        }
    }
    $String = "'$String'";
    return $String;
}

function date_compare($Field, $Operand, $Date1, $Date2 = '') {
    $Date1 = db_string($Date1);
    $Date2 = db_string($Date2);
    $Return = array();

    switch ($Operand) {
        case 'on':
            $Return[] = " $Field >= '$Date1 00:00:00' ";
            $Return[] = " $Field <= '$Date1 23:59:59' ";
            break;
        case 'before':
            $Return[] = " $Field < '$Date1 00:00:00' ";
            break;
        case 'after':
            $Return[] = " $Field > '$Date1 23:59:59' ";
            break;
        case 'between':
            $Return[] = " $Field >= '$Date1 00:00:00' ";
            $Return[] = " $Field <= '$Date2 00:00:00' ";
            break;
    }

    return $Return;
}


function num_compare($Field, $Operand, $Num1, $Num2 = '') {

    if ($Num1 != 0) {
        $Num1 = db_string($Num1);
    }
    if ($Num2 != 0) {
        $Num2 = db_string($Num2);
    }

    $Return = array();

    switch ($Operand) {
        case 'equal':
            $Return[] = " $Field = '$Num1' ";
            break;
        case 'above':
            $Return[] = " $Field > '$Num1' ";
            break;
        case 'below':
            $Return[] = " $Field < '$Num1' ";
            break;
        case 'between':
            $Return[] = " $Field > '$Num1' ";
            $Return[] = " $Field < '$Num2' ";
            break;
        default:
            print_r($Return);
            die();
    }
    return $Return;
}

// Arrays, regexes, and all that fun stuff we can use for validation, form generation, etc

$DateChoices = array('inarray' => array('on', 'before', 'after', 'between'));
$SingleDateChoices = array('inarray' => array('on', 'before', 'after'));
$NumberChoices = array('inarray' => array('equal', 'above', 'below', 'between', 'buffer'));
$YesNo = array('inarray' => array('any', 'yes', 'no'));
$DisabledField = array('inarray' => array("DisableAnyone", "DisablePosting", "DisableAvatar", "DisableForums", "DisableIRC", "DisablePM", "DisableLeech", "DisableRequests", "DisableUpload", "DisablePoints", "DisableTagging", "DisableWiki", "DisableInvites", "DisableCheckAll", "DisableCheckSelf"));
$OrderVals = array('inarray' => array('Username', 'Ratio', 'IP', 'Email', 'Joined', 'Last Seen', 'Uploaded', 'Downloaded', 'Invites', 'Snatches'));
$WayVals = array('inarray' => array('Ascending', 'Descending'));

$email_history_checked = true;
$ip_history_checked = true;
$disabled_ip_checked = true;

if (count($_GET)) {
    if (!empty($_GET['email_history']) || !empty($_GET['disabled_id']) || !empty($_GET['ip_history'])) {
        if (empty($_GET['email_history'])) {
            $email_history_checked = false;
        }
        if (empty($_GET['disabled_ip'])) {
            $disabled_ip_checked = false;
        }
        if (empty($_GET['ip_history'])) {
            $ip_history_checked = false;
        }
    }
    $DateRegex = array('regex' => '/\d{4}-\d{2}-\d{2}/');

    $ClassIDs = array();
    $SecClassIDs = array();
    foreach ($Classes as $ClassID => $Value) {
        if ($Value['Secondary']) {
            $SecClassIDs[] = $ClassID;
        } else {
            $ClassIDs[] = $ClassID;
        }
    }

    $Val->SetFields('comment', '0', 'string', 'Comment is too long.', array('maxlength' => 512));
    $Val->SetFields('disabled_invites', '0', 'inarray', 'Invalid disabled_invites field', $YesNo);


    $Val->SetFields('joined', '0', 'inarray', 'Invalid joined field', $DateChoices);
    $Val->SetFields('join1', '0', 'regex', 'Invalid join1 field', $DateRegex);
    $Val->SetFields('join2', '0', 'regex', 'Invalid join2 field', $DateRegex);

    $Val->SetFields('lastactive', '0', 'inarray', 'Invalid lastactive field', $DateChoices);
    $Val->SetFields('lastactive1', '0', 'regex', 'Invalid lastactive1 field', $DateRegex);
    $Val->SetFields('lastactive2', '0', 'regex', 'Invalid lastactive2 field', $DateRegex);

    $Val->SetFields('ratio', '0', 'inarray', 'Invalid ratio field', $NumberChoices);
    $Val->SetFields('uploaded', '0', 'inarray', 'Invalid uploaded field', $NumberChoices);
    $Val->SetFields('downloaded', '0', 'inarray', 'Invalid downloaded field', $NumberChoices);
    //$Val->SetFields('snatched', '0', 'inarray', 'Invalid snatched field', $NumberChoices);

    $Val->SetFields('matchtype', '0', 'inarray', 'Invalid matchtype field', array('inarray' => array('strict', 'fuzzy', 'regex')));

    $Val->SetFields('lockedaccount', '0', 'inarray', 'Invalid locked account field', array('inarray' => array('any', 'locked', 'unlocked')));

    $Val->SetFields('enabled', '0', 'inarray', 'Invalid enabled field', array('inarray' => array('', 0, 1, 2)));
    $Val->SetFields('class', '0', 'inarray', 'Invalid class', array('inarray' => $ClassIDs));
    $Val->SetFields('secclass', '0', 'inarray', 'Invalid class', array('inarray' => $SecClassIDs));
    $Val->SetFields('donor', '0', 'inarray', 'Invalid donor field', $YesNo);
    $Val->SetFields('warned', '0', 'inarray', 'Invalid warned field', $YesNo);
    $Val->SetFields('disabled', '0', 'inarray', 'Invalid disabled field', $DisabledField);

    $Val->SetFields('order', '0', 'inarray', 'Invalid ordering', $OrderVals);
    $Val->SetFields('way', '0', 'inarray', 'Invalid way', $WayVals);

    $Val->SetFields('passkey', '0', 'string', 'Invalid passkey', array('maxlength' => 32));
    $Val->SetFields('avatar', '0', 'string', 'Avatar URL too long', array('maxlength' => 512));
    $Val->SetFields('stylesheet', '0', 'inarray', 'Invalid stylesheet', array_unique(array_keys($Stylesheets)));
    $Val->SetFields('cc', '0', 'inarray', 'Invalid Country Code', array('maxlength' => 2));

    $Err = $Val->ValidateForm($_GET);

    if (!$Err) {
        // Passed validation. Let's rock.
        $RunQuery = false; // if we should run the search

        if (isset($_GET['matchtype']) && $_GET['matchtype'] == 'strict') {
            $Match = ' = ';
        } elseif (isset($_GET['matchtype']) && $_GET['matchtype'] == 'regex') {
            $Match = ' REGEXP ';
        } else {
            $Match = ' LIKE ';
        }

        $OrderTable = array(
            'Username' => 'um1.Username',
            'Joined' => 'ui1.JoinDate',
            'Email' => 'um1.Email',
            'IP' => 'um1.IP',
            'Last Seen' => 'um1.LastAccess',
            'Uploaded' => 'um1.Uploaded',
            'Downloaded' => 'um1.Downloaded',
            'Ratio' => '(um1.Uploaded / um1.Downloaded)',
            'Invites' => 'um1.Invites',
            'Snatches' => 'Snatches'
        );

        $WayTable = array('Ascending' => 'ASC', 'Descending' => 'DESC');

        $Where = array();
        $Having = array();
        $Join = array();
        $Group = array();
        $Distinct = '';
        $Order = '';


        $SQL = '
				SQL_CALC_FOUND_ROWS
				um1.ID,
				um1.Username,
				um1.Uploaded,
				um1.Downloaded,';
        if ($_GET['snatched'] == 'off') {
            $SQL .= "'X' AS Snatches,";
        } else {
            $SQL .= "
				(
					SELECT COUNT(xs.uid)
					FROM xbt_snatched AS xs
					WHERE xs.uid = um1.ID
				) AS Snatches,";
        }
        if ($_GET['invitees'] == 'off') {
            $SQL .= "'X' AS Invitees,";
        } else {
            $SQL .= "
			(
				SELECT COUNT(ui2.UserID)
				FROM users_info AS ui2
				WHERE um1.ID = ui2.Inviter
  			) AS Invitees,";
        }
        $SQL .= '
				um1.PermissionID,
				um1.Email,
				um1.Enabled,
				um1.IP,
				um1.Invites,
				ui1.DisableInvites,
				ui1.Warned,
				ui1.Donor,
				ui1.JoinDate,
				um1.LastAccess
			FROM users_main AS um1
				JOIN users_info AS ui1 ON ui1.UserID = um1.ID ';


        if (!empty($_GET['username'])) {
            $Where[] = 'um1.Username' . $Match . wrap($_GET['username']);
        }

        if (!empty($_GET['email'])) {
            if (isset($_GET['email_history'])) {
                $Distinct = 'DISTINCT ';
                $Join['he'] = ' JOIN users_history_emails AS he ON he.UserID = um1.ID ';
                $Where[] = ' he.Email ' . $Match . wrap($_GET['email']);
            } else {
                $Where[] = 'um1.Email' . $Match . wrap($_GET['email']);
            }
        }

        if (!empty($_GET['email_cnt']) && is_number($_GET['email_cnt'])) {
            $Query = "
				SELECT UserID
				FROM users_history_emails
				GROUP BY UserID
				HAVING COUNT(DISTINCT Email) ";
            if ($_GET['emails_opt'] === 'equal') {
                $operator = '=';
            }
            if ($_GET['emails_opt'] === 'above') {
                $operator = '>';
            }
            if ($_GET['emails_opt'] === 'below') {
                $operator = '<';
            }
            $Query .= $operator . ' ' . $_GET['email_cnt'];
            $DB->query($Query);
            $Users = implode(',', $DB->collect('UserID'));
            if (!empty($Users)) {
                $Where[] = "um1.ID IN ($Users)";
            }
        }


        if (!empty($_GET['ip'])) {
            if (isset($_GET['ip_history'])) {
                $Distinct = 'DISTINCT ';
                $Join['hi'] = ' JOIN users_history_ips AS hi ON hi.UserID = um1.ID ';
                $Where[] = ' hi.IP ' . $Match . wrap($_GET['ip'], '', true);
            } else {
                $Where[] = 'um1.IP' . $Match . wrap($_GET['ip'], '', true);
            }
        }

        if ($_GET['lockedaccount'] != '' && $_GET['lockedaccount'] != 'any') {
            $Join['la'] = '';

            if ($_GET['lockedaccount'] == 'unlocked') {
                $Join['la'] .= ' LEFT';
                $Where[] = ' la.UserID IS NULL';
            }

            $Join['la'] .= ' JOIN locked_accounts AS la ON la.UserID = um1.ID ';
        }



        if (!empty($_GET['cc'])) {
            if ($_GET['cc_op'] == 'equal') {
                $Where[] = "um1.ipcc = '" . db_string($_GET['cc']) . "'";
            } else {
                $Where[] = "um1.ipcc != '" . db_string($_GET['cc']) . "'";
            }
        }

        if (!empty($_GET['tracker_ip'])) {
            $Distinct = 'DISTINCT ';
            $Join['xfu'] = ' JOIN xbt_files_users AS xfu ON um1.ID = xfu.uid ';
            $Where[] = ' xfu.ip ' . $Match . wrap($_GET['tracker_ip'], '', true);
        }

        //      if (!empty($_GET['tracker_ip'])) {
        //              $Distinct = 'DISTINCT ';
        //              $Join['xs'] = ' JOIN xbt_snatched AS xs ON um1.ID = xs.uid ';
        //              $Where[] = ' xs.IP '.$Match.wrap($_GET['ip']);
        //      }

        if (!empty($_GET['comment'])) {
            $Where[] = 'ui1.AdminComment' . $Match . wrap($_GET['comment']);
        }

        if (!empty($_GET['lastfm'])) {
            $Distinct = 'DISTINCT ';
            $Join['lastfm'] = ' JOIN lastfm_users AS lfm ON lfm.ID = um1.ID ';
            $Where[] = ' lfm.Username' . $Match . wrap($_GET['lastfm']);
        }


        if (strlen($_GET['invites1'])) {
            $Invites1 = round($_GET['invites1']);
            $Invites2 = round($_GET['invites2']);
            $Where[] = implode(' AND ', num_compare('Invites', $_GET['invites'], $Invites1, $Invites2));
        }

        if (strlen($_GET['invitees1']) && $_GET['invitees'] != 'off') {
            $Invitees1 = round($_GET['invitees1']);
            $Invitees2 = round($_GET['invitees2']);
            $Having[] = implode(' AND ', num_compare('Invitees', $_GET['invitees'], $Invitees1, $Invitees2));
        }

        if ($_GET['disabled_invites'] == 'yes') {
            $Where[] = 'ui1.DisableInvites = \'1\'';
        } elseif ($_GET['disabled_invites'] == 'no') {
            $Where[] = 'ui1.DisableInvites = \'0\'';
        }

        if ($_GET['disabled']) {
            $DisabledSQL = array("DisablePosting", "DisableAvatar", "DisableForums", "DisableIRC", "DisablePM", "DisableRequests", "DisableUpload", "DisablePoints", "DisableTagging", "DisableWiki", "DisableInvites", "DisableCheckAll", "DisableCheckSelf");
            if ($_GET['disabled'] == "DisableLeech") {
                $Where[] = 'um1.can_leech = \'0\'';
            } else if ($_GET['disabled'] == "DisableAnyone") {
                $sql = "um1.can_leech = '0'";
                foreach ($DisabledSQL as $d) {
                    $sql .= " or ui1.$d = '1'";
                }
                $Where[] = "($sql)";
            } else {
                $Where[] = 'ui1.' . $_GET['disabled'] . ' = \'1\'';
            }
        }

        if ($_GET['join1']) {
            $Where[] = implode(' AND ', date_compare('ui1.JoinDate', $_GET['joined'], $_GET['join1'], $_GET['join2']));
        }

        if ($_GET['lastactive1']) {
            $Where[] = implode(' AND ', date_compare('um1.LastAccess', $_GET['lastactive'], $_GET['lastactive1'], $_GET['lastactive2']));
        }

        if ($_GET['ratio1']) {
            $Decimals = strlen(array_pop(explode('.', $_GET['ratio1'])));
            if (!$Decimals) {
                $Decimals = 0;
            }
            $Where[] = implode(' AND ', num_compare("ROUND(Uploaded/Downloaded,$Decimals)", $_GET['ratio'], $_GET['ratio1'], $_GET['ratio2']));
        }

        if (strlen($_GET['uploaded1'])) {
            $Upload1 = round($_GET['uploaded1']);
            $Upload2 = round($_GET['uploaded2']);
            if ($_GET['uploaded'] != 'buffer') {
                $Where[] = implode(' AND ', num_compare('ROUND(Uploaded / 1024 / 1024 / 1024)', $_GET['uploaded'], $Upload1, $Upload2));
            } else {
                $Where[] = implode(' AND ', num_compare('ROUND((Uploaded / 1024 / 1024 / 1024) - (Downloaded / 1024 / 1024 / 1023))', 'between', $Upload1 * 0.9, $Upload1 * 1.1));
            }
        }

        if (strlen($_GET['downloaded1'])) {
            $Download1 = round($_GET['downloaded1']);
            $Download2 = round($_GET['downloaded2']);
            $Where[] = implode(' AND ', num_compare('ROUND(Downloaded / 1024 / 1024 / 1024)', $_GET['downloaded'], $Download1, $Download2));
        }

        if (strlen($_GET['snatched1'])) {
            $Snatched1 = round($_GET['snatched1']);
            $Snatched2 = round($_GET['snatched2']);
            $Having[] = implode(' AND ', num_compare('Snatches', $_GET['snatched'], $Snatched1, $Snatched2));
        }

        if ($_GET['enabled'] != '') {
            $Where[] = 'um1.Enabled = ' . wrap($_GET['enabled'], '=');
        }

        if ($_GET['class'] != '') {
            $Where[] = 'um1.PermissionID = ' . wrap($_GET['class'], '=');
        }

        if ($_GET['secclass'] != '') {
            $Join['ul'] = ' JOIN users_levels AS ul ON um1.ID = ul.UserID ';
            $Where[] = 'ul.PermissionID = ' . wrap($_GET['secclass'], '=');
        }

        if ($_GET['donor'] == 'yes') {
            $Where[] = 'ui1.Donor = \'1\'';
        } elseif ($_GET['donor'] == 'no') {
            $Where[] = 'ui1.Donor = \'0\'';
        }

        if ($_GET['warned'] == 'yes') {
            $Where[] = 'ui1.Warned != \'0000-00-00 00:00:00\'';
        } elseif ($_GET['warned'] == 'no') {
            $Where[] = 'ui1.Warned = \'0000-00-00 00:00:00\'';
        }

        if ($_GET['disabled_ip']) {
            $Distinct = 'DISTINCT ';
            if ($_GET['ip_history']) {
                if (!isset($Join['hi'])) {
                    $Join['hi'] = ' JOIN users_history_ips AS hi ON hi.UserID = um1.ID ';
                }
                $Join['hi2'] = ' JOIN users_history_ips AS hi2 ON hi2.IP = hi.IP ';
                $Join['um2'] = ' JOIN users_main AS um2 ON um2.ID = hi2.UserID AND um2.Enabled = \'2\' ';
            } else {
                $Join['um2'] = ' JOIN users_main AS um2 ON um2.IP = um1.IP AND um2.Enabled = \'2\' ';
            }
        }

        if (!empty($_GET['passkey'])) {
            $Where[] = 'um1.torrent_pass' . $Match . wrap($_GET['passkey']);
        }

        if (!empty($_GET['avatar'])) {
            $Where[] = 'ui1.Avatar' . $Match . wrap($_GET['avatar']);
        }

        if ($_GET['stylesheet'] != '') {
            $Where[] = 'ui1.StyleID = ' . wrap($_GET['stylesheet'], '=');
        }

        if ($OrderTable[$_GET['order']] && $WayTable[$_GET['way']]) {
            $Order = ' ORDER BY ' . $OrderTable[$_GET['order']] . ' ' . $WayTable[$_GET['way']] . ' ';
        }

        //---------- Finish generating the search string

        $SQL = 'SELECT ' . $Distinct . $SQL;
        $SQL .= implode(' ', $Join);

        if (count($Where)) {
            $SQL .= ' WHERE ' . implode(' AND ', $Where);
        }

        if (count($Group)) {
            $SQL .= " GROUP BY " . implode(' ,', $Group);
        }

        if (count($Having)) {
            $SQL .= ' HAVING ' . implode(' AND ', $Having);
        }

        $SQL .= $Order;

        if (count($Where) > 0 || count($Join) > 0 || count($Having) > 0) {
            $RunQuery = true;
        }

        list($Page, $Limit) = Format::page_limit(USERS_PER_PAGE);
        $SQL .= " LIMIT $Limit";
    } else {
        error($Err);
    }
}
View::show_header(Lang::get('user', 'user_search'));
?>

<div class="thin">
    <form class="search_form" name="users" action="user.php" method="get">
        <input type="hidden" name="action" value="search" />
        <table class="layout">
            <tr>
                <td class="label nobr"><?= Lang::get('user', 'username') ?>:</td>
                <td width="24%">
                    <input type="text" name="username" size="20" value="<?= display_str($_GET['username']) ?>" />
                </td>
                <td class="label nobr"><?= Lang::get('user', 'joined') ?>:</td>
                <td width="24%">
                    <select name="joined">
                        <option value="on" <? if ($_GET['joined'] === 'on') {
                                                echo ' selected="selected"';
                                            } ?>><?= Lang::get('user', 'on') ?></option>
                        <option value="before" <? if ($_GET['joined'] === 'before') {
                                                    echo ' selected="selected"';
                                                } ?>><?= Lang::get('user', 'before') ?></option>
                        <option value="after" <? if ($_GET['joined'] === 'after') {
                                                    echo ' selected="selected"';
                                                } ?>><?= Lang::get('user', 'after') ?></option>
                        <option value="between" <? if ($_GET['joined'] === 'between') {
                                                    echo ' selected="selected"';
                                                } ?>><?= Lang::get('user', 'between') ?></option>
                    </select>
                    <input type="text" name="join1" size="10" value="<?= display_str($_GET['join1']) ?>" placeholder="YYYY-MM-DD" />
                    <input type="text" name="join2" size="10" value="<?= display_str($_GET['join2']) ?>" placeholder="YYYY-MM-DD" />
                </td>
                <td class="label nobr"><?= Lang::get('user', 'enabled') ?>:</td>
                <td>
                    <select name="enabled">
                        <option value="" <? if ($_GET['enabled'] === '') {
                                                echo ' selected="selected"';
                                            } ?>><?= Lang::get('user', 'do_not_care') ?></option>
                        <option value="0" <? if ($_GET['enabled'] === '0') {
                                                echo ' selected="selected"';
                                            } ?>><?= Lang::get('user', 'unconfirmed') ?></option>
                        <option value="1" <? if ($_GET['enabled'] === '1') {
                                                echo ' selected="selected"';
                                            } ?>><?= Lang::get('user', 'enabled') ?></option>
                        <option value="2" <? if ($_GET['enabled'] === '2') {
                                                echo ' selected="selected"';
                                            } ?>><?= Lang::get('user', 'disabled') ?></option>
                    </select>
                </td>
            </tr>

            <tr>
                <td class="label nobr"><?= Lang::get('user', 'email_address') ?>:</td>
                <td>
                    <input type="text" name="email" size="20" value="<?= display_str($_GET['email']) ?>" />
                </td>
                <td class="label nobr"><?= Lang::get('user', 'last_active') ?>:</td>
                <td width="30%">
                    <select name="lastactive">
                        <option value="on" <? if ($_GET['lastactive'] === 'on') {
                                                echo ' selected="selected"';
                                            } ?>><?= Lang::get('user', 'on') ?></option>
                        <option value="before" <? if ($_GET['lastactive'] === 'before') {
                                                    echo ' selected="selected"';
                                                } ?>><?= Lang::get('user', 'before') ?></option>
                        <option value="after" <? if ($_GET['lastactive'] === 'after') {
                                                    echo ' selected="selected"';
                                                } ?>><?= Lang::get('user', 'after') ?></option>
                        <option value="between" <? if ($_GET['lastactive'] === 'between') {
                                                    echo ' selected="selected"';
                                                } ?>><?= Lang::get('user', 'between') ?></option>
                    </select>
                    <input type="text" name="lastactive1" size="10" value="<?= display_str($_GET['lastactive1']) ?>" placeholder="YYYY-MM-DD" />
                    <input type="text" name="lastactive2" size="10" value="<?= display_str($_GET['lastactive2']) ?>" placeholder="YYYY-MM-DD" />
                </td>
                <td class="label nobr"><?= Lang::get('user', 'primary_class') ?>:</td>
                <td>
                    <select name="class">
                        <option value="" <? if ($_GET['class'] === '') {
                                                echo ' selected="selected"';
                                            } ?>><?= Lang::get('user', 'do_not_care') ?></option>
                        <? foreach ($ClassLevels as $Class) {
                            if ($Class['Secondary']) {
                                continue;
                            }
                        ?>
                            <option value="<?= $Class['ID'] ?>" <? if ($_GET['class'] === $Class['ID']) {
                                                                    echo ' selected="selected"';
                                                                } ?>><?= Format::cut_string($Class['Name'], 10, 1, 1) . ' (' . $Class['Level'] . ')' ?></option>
                        <?  } ?>
                    </select>
                </td>
            </tr>

            <tr>
                <td class="label tooltip nobr" title="<?= Lang::get('user', 'to_fuzzy_search_for_a_block_of_addresses_title') ?>"><?= Lang::get('user', 'ip_address') ?>:</td>
                <td>
                    <input type="text" name="ip" size="20" value="<?= display_str($_GET['ip']) ?>" />
                </td>
                <td class="label nobr"><?= Lang::get('user', 'locked_account') ?>:</td>
                <td>
                    <select name="lockedaccount">
                        <option value="any" <? if ($_GET['lockedaccount'] == 'any') {
                                                echo ' selected="selected"';
                                            } ?>><?= Lang::get('user', 'do_not_care') ?></option>
                        <option value="locked" <? if ($_GET['lockedaccount'] == 'locked') {
                                                    echo ' selected="selected"';
                                                } ?>><?= Lang::get('user', 'locked') ?></option>
                        <option value="unlocked" <? if ($_GET['lockedaccount'] == 'unlocked') {
                                                        echo ' selected="selected"';
                                                    } ?>><?= Lang::get('user', 'ulocked') ?></option>
                    </select>
                </td>
                <td class="label nobr"><?= Lang::get('user', 'secondary_class') ?>:</td>
                <td>
                    <select name="secclass">
                        <option value="" <? if ($_GET['secclass'] === '') {
                                                echo ' selected="selected"';
                                            } ?>><?= Lang::get('user', 'do_not_care') ?></option>
                        <? $Secondaries = array();
                        // Neither level nor ID is particularly useful when searching secondary classes, so let's do some
                        // kung-fu to sort them alphabetically.
                        $fnc = function ($Class1, $Class2) {
                            return strcmp($Class1['Name'], $Class2['Name']);
                        };
                        foreach ($ClassLevels as $Class) {
                            if (!$Class['Secondary']) {
                                continue;
                            }
                            $Secondaries[] = $Class;
                        }
                        usort($Secondaries, $fnc);
                        foreach ($Secondaries as $Class) {
                        ?>
                            <option value="<?= $Class['ID'] ?>" <? if ($_GET['secclass'] === $Class['ID']) {
                                                                    echo ' selected="selected"';
                                                                } ?>><?= Format::cut_string($Class['Name'], 20, 1, 1) ?></option>
                        <?  } ?>
                    </select>
                </td>
            </tr>

            <tr>
                <td class="label nobr">Extra:</td>
                <td>
                    <ul class="options_list nobullet">
                        <li title="<?= Lang::get('user', 'disabled_accounts_linked_by_ip_title') ?>">
                            <input type="checkbox" name="disabled_ip" id="disabled_ip" />
                            <label for="disabled_ip"><?= Lang::get('user', 'disabled_accounts_linked_by_ip') ?></label>
                        </li>
                        <li>
                            <input type="checkbox" name="ip_history" id="ip_history" <? if ($ip_history_checked) {
                                                                                            echo ' checked="checked"';
                                                                                        } ?> />
                            <label title="<?= Lang::get('user', 'disabled_accounts_linked_by_ip_must_also_be_checked') ?>" for="ip_history"><?= Lang::get('user', 'ip_history') ?></label>
                        </li>
                        <li>
                            <input type="checkbox" name="email_history" id="email_history" <? if ($email_history_checked) {
                                                                                                echo ' checked="checked"';
                                                                                            } ?> />
                            <label title="<?= Lang::get('user', 'also_search_the_email_addresses_the_member_used_in_the_past') ?>" for="email_history"><?= Lang::get('user', 'email_history') ?></label>
                        </li>
                    </ul>
                </td>
                <td class="label nobr"><?= Lang::get('user', 'ratio') ?>:</td>
                <td width="30%">
                    <select name="ratio">
                        <option value="equal" <? if ($_GET['ratio'] === 'equal') {
                                                    echo ' selected="selected"';
                                                } ?>><?= Lang::get('user', 'equal') ?></option>
                        <option value="above" <? if ($_GET['ratio'] === 'above') {
                                                    echo ' selected="selected"';
                                                } ?>><?= Lang::get('user', 'above') ?></option>
                        <option value="below" <? if ($_GET['ratio'] === 'below') {
                                                    echo ' selected="selected"';
                                                } ?>><?= Lang::get('user', 'below') ?></option>
                        <option value="between" <? if ($_GET['ratio'] === 'between') {
                                                    echo ' selected="selected"';
                                                } ?>><?= Lang::get('user', 'between') ?></option>
                    </select>
                    <input type="text" name="ratio1" size="6" value="<?= display_str($_GET['ratio1']) ?>" />
                    <input type="text" name="ratio2" size="6" value="<?= display_str($_GET['ratio2']) ?>" />
                </td>
                <td class="label nobr"><?= Lang::get('user', 'donor') ?>:</td>
                <td>
                    <select name="donor">
                        <option value="" <? if ($_GET['donor'] === '') {
                                                echo ' selected="selected"';
                                            } ?>><?= Lang::get('user', 'do_not_care') ?></option>
                        <option value="yes" <? if ($_GET['donor'] === 'yes') {
                                                echo ' selected="selected"';
                                            } ?>><?= Lang::get('user', 'yes') ?></option>
                        <option value="no" <? if ($_GET['donor'] === 'no') {
                                                echo ' selected="selected"';
                                            } ?>><?= Lang::get('user', 'no') ?></option>
                    </select>
                </td>
            </tr>

            <tr>
                <td class="label nobr"><?= Lang::get('user', 'disabled_invites') ?>:</td>
                <td>
                    <select name="disabled_invites">
                        <option value="" <? if ($_GET['disabled_invites'] === '') {
                                                echo ' selected="selected"';
                                            } ?>><?= Lang::get('user', 'do_not_care') ?></option>
                        <option value="yes" <? if ($_GET['disabled_invites'] === 'yes') {
                                                echo ' selected="selected"';
                                            } ?>><?= Lang::get('user', 'yes') ?></option>
                        <option value="no" <? if ($_GET['disabled_invites'] === 'no') {
                                                echo ' selected="selected"';
                                            } ?>><?= Lang::get('user', 'no') ?></option>
                    </select>
                </td>
                <td class="label tooltip nobr" title="<?= Lang::get('user', 'units_are_in_gibibytes') ?>"><?= Lang::get('user', 'uploaded') ?>:</td>
                <td width="30%">
                    <select name="uploaded">
                        <option value="equal" <? if ($_GET['uploaded'] === 'equal') {
                                                    echo ' selected="selected"';
                                                } ?>><?= Lang::get('user', 'equal') ?></option>
                        <option value="above" <? if ($_GET['uploaded'] === 'above') {
                                                    echo ' selected="selected"';
                                                } ?>><?= Lang::get('user', 'above') ?></option>
                        <option value="below" <? if ($_GET['uploaded'] === 'below') {
                                                    echo ' selected="selected"';
                                                } ?>><?= Lang::get('user', 'below') ?></option>
                        <option value="between" <? if ($_GET['uploaded'] === 'between') {
                                                    echo ' selected="selected"';
                                                } ?>><?= Lang::get('user', 'between') ?></option>
                        <option value="buffer" <? if ($_GET['uploaded'] === 'buffer') {
                                                    echo ' selected="selected"';
                                                } ?>><?= Lang::get('user', 'buffer') ?></option>
                    </select>
                    <input type="text" name="uploaded1" size="6" value="<?= display_str($_GET['uploaded1']) ?>" />
                    <input type="text" name="uploaded2" size="6" value="<?= display_str($_GET['uploaded2']) ?>" />
                </td>
                <td class="label nobr"><?= Lang::get('user', 'disabled_privilege') ?>:</td>
                <td>
                    <select name="disabled">
                        <option value="" <? if ($_GET['disabled'] === '') {
                                                echo ' selected="selected"';
                                            } ?>><?= Lang::get('user', 'do_not_care') ?></option>
                        <option value="DisableAnyone" <? if ($_GET['disabled'] === 'DisableAnyone') {
                                                            echo ' selected="selected"';
                                                        } ?>><?= Lang::get('user', 'anyone') ?></option>
                        <option value="DisablePosting" <? if ($_GET['disabled'] === 'DisablePosting') {
                                                            echo ' selected="selected"';
                                                        } ?>><?= Lang::get('user', 'posting') ?></option>
                        <option value="DisableAvatar" <? if ($_GET['disabled'] === 'DisableAvatar') {
                                                            echo ' selected="selected"';
                                                        } ?>><?= Lang::get('user', 'avatar') ?></option>
                        <option value="DisableForums" <? if ($_GET['disabled'] === 'DisableForums') {
                                                            echo ' selected="selected"';
                                                        } ?>><?= Lang::get('user', 'forums') ?></option>
                        <option value="DisableIRC" <? if ($_GET['disabled'] === 'DisableIRC') {
                                                        echo ' selected="selected"';
                                                    } ?>><?= Lang::get('user', 'irc') ?></option>
                        <option value="DisablePM" <? if ($_GET['disabled'] === 'DisablePM') {
                                                        echo ' selected="selected"';
                                                    } ?>><?= Lang::get('user', 'pm') ?></option>
                        <option value="DisableLeech" <? if ($_GET['disabled'] === 'DisableLeech') {
                                                            echo ' selected="selected"';
                                                        } ?>><?= Lang::get('user', 'leech') ?></option>
                        <option value="DisableRequests" <? if ($_GET['disabled'] === 'DisableRequests') {
                                                            echo ' selected="selected"';
                                                        } ?>><?= Lang::get('global', 'requests') ?></option>
                        <option value="DisableUpload" <? if ($_GET['disabled'] === 'DisableUpload') {
                                                            echo ' selected="selected"';
                                                        } ?>><?= Lang::get('user', 'torrent_upload') ?></option>
                        <option value="DisablePoints" <? if ($_GET['disabled'] === 'DisablePoints') {
                                                            echo ' selected="selected"';
                                                        } ?>><?= Lang::get('user', 'bonus_points') ?></option>
                        <option value="DisableTagging" <? if ($_GET['disabled'] === 'DisableTagging') {
                                                            echo ' selected="selected"';
                                                        } ?>><?= Lang::get('user', 'tagging') ?></option>
                        <option value="DisableWiki" <? if ($_GET['disabled'] === 'DisableWiki') {
                                                        echo ' selected="selected"';
                                                    } ?>><?= Lang::get('user', 'wiki') ?></option>
                        <option value="DisableInvites" <? if ($_GET['disabled'] === 'DisableInvites') {
                                                            echo ' selected="selected"';
                                                        } ?>><?= Lang::get('user', 'invites') ?></option>
                        <option value="DisableCheckAll" <? if ($_GET['disabled'] === 'DisableCheckAll') {
                                                            echo ' selected="selected"';
                                                        } ?>><?= Lang::get('user', 'check_all_torrents') ?></option>
                        <option value="DisableCheckSelf" <? if ($_GET['disabled'] === 'DisableCheckSelf') {
                                                                echo ' selected="selected"';
                                                            } ?>><?= Lang::get('user', 'check_self_torrents') ?></option>
                    </select>
                </td>
            </tr>

            <tr>
                <td class="label nobr"><?= Lang::get('user', 'number_of_invites') ?>:</td>
                <td>
                    <select name="invites">
                        <option value="equal" <? if ($_GET['invites'] === 'equal') {
                                                    echo ' selected="selected"';
                                                } ?>><?= Lang::get('user', 'equal') ?></option>
                        <option value="above" <? if ($_GET['invites'] === 'above') {
                                                    echo ' selected="selected"';
                                                } ?>><?= Lang::get('user', 'above') ?></option>
                        <option value="below" <? if ($_GET['invites'] === 'below') {
                                                    echo ' selected="selected"';
                                                } ?>><?= Lang::get('user', 'below') ?></option>
                        <option value="between" <? if ($_GET['invites'] === 'between') {
                                                    echo ' selected="selected"';
                                                } ?>><?= Lang::get('user', 'between') ?></option>
                    </select>
                    <input type="text" name="invites1" size="6" value="<?= display_str($_GET['invites1']) ?>" />
                    <input type="text" name="invites2" size="6" value="<?= display_str($_GET['invites2']) ?>" />
                </td>
                <td class="label tooltip nobr" title="<?= Lang::get('user', 'units_are_in_gibibytes') ?>"><?= Lang::get('user', 'downloaded') ?>:</td>
                <td width="30%">
                    <select name="downloaded">
                        <option value="equal" <? if ($_GET['downloaded'] === 'equal') {
                                                    echo ' selected="selected"';
                                                } ?>><?= Lang::get('user', 'equal') ?></option>
                        <option value="above" <? if ($_GET['downloaded'] === 'above') {
                                                    echo ' selected="selected"';
                                                } ?>><?= Lang::get('user', 'above') ?></option>
                        <option value="below" <? if ($_GET['downloaded'] === 'below') {
                                                    echo ' selected="selected"';
                                                } ?>><?= Lang::get('user', 'below') ?></option>
                        <option value="between" <? if ($_GET['downloaded'] === 'between') {
                                                    echo ' selected="selected"';
                                                } ?>><?= Lang::get('user', 'between') ?></option>
                    </select>
                    <input type="text" name="downloaded1" size="6" value="<?= display_str($_GET['downloaded1']) ?>" />
                    <input type="text" name="downloaded2" size="6" value="<?= display_str($_GET['downloaded2']) ?>" />
                </td>
                <td class="label nobr"><?= Lang::get('user', 'warned') ?>:</td>
                <td>
                    <select name="warned">
                        <option value="" <? if ($_GET['warned'] === '') {
                                                echo ' selected="selected"';
                                            } ?>><?= Lang::get('user', 'do_not_care') ?></option>
                        <option value="yes" <? if ($_GET['warned'] === 'yes') {
                                                echo ' selected="selected"';
                                            } ?>><?= Lang::get('user', 'yes') ?></option>
                        <option value="no" <? if ($_GET['warned'] === 'no') {
                                                echo ' selected="selected"';
                                            } ?>><?= Lang::get('user', 'no') ?></option>
                    </select>
                </td>
            </tr>

            <tr>
                <td width="30%" class="label nobr"><?= Lang::get('user', 'number_of_invitees') ?>:</td>
                <td>
                    <select name="invitees">
                        <option value="equal" <?= isset($_GET['invitees']) && $_GET['invitees'] == 'equal' ? 'selected' : '' ?>><?= Lang::get('user', 'equal') ?></option>
                        <option value="above" <?= isset($_GET['invitees']) && $_GET['invitees'] == 'above' ? 'selected' : '' ?>><?= Lang::get('user', 'above') ?></option>
                        <option value="below" <?= isset($_GET['invitees']) && $_GET['invitees'] == 'below' ? 'selected' : '' ?>><?= Lang::get('user', 'below') ?></option>
                        <option value="between" <?= isset($_GET['invitees']) && $_GET['invitees'] == 'between' ? 'selected' : '' ?>><?= Lang::get('user', 'between') ?></option>
                        <option value="off" <?= !isset($_GET['invitees']) || $_GET['invitees'] == 'off' ? 'selected' : '' ?>><?= Lang::get('user', 'off') ?></option>
                    </select>
                    <input type="text" name="invitees1" size="6" value="<?= display_str($_GET['invitees1']) ?>" />
                    <input type="text" name="invitees2" size="6" value="<?= display_str($_GET['invitees2']) ?>" />
                </td>
                <td class="label nobr"><?= Lang::get('global', 'snatched') ?>:</td>
                <td width="30%">
                    <select name="snatched">
                        <option value="equal" <? if (isset($_GET['snatched']) && $_GET['snatched'] === 'equal') {
                                                    echo ' selected="selected"';
                                                } ?>><?= Lang::get('user', 'equal') ?></option>
                        <option value="above" <? if (isset($_GET['snatched']) && $_GET['snatched'] === 'above') {
                                                    echo ' selected="selected"';
                                                } ?>><?= Lang::get('user', 'above') ?></option>
                        <option value="below" <? if (isset($_GET['snatched']) && $_GET['snatched'] === 'below') {
                                                    echo ' selected="selected"';
                                                } ?>><?= Lang::get('user', 'below') ?></option>
                        <option value="between" <? if (isset($_GET['snatched']) && $_GET['snatched'] === 'between') {
                                                    echo ' selected="selected"';
                                                } ?>><?= Lang::get('user', 'between') ?></option>
                        <option value="off" <? if (!isset($_GET['snatched']) || $_GET['snatched'] === 'off') {
                                                echo ' selected="selected"';
                                            } ?>><?= Lang::get('user', 'off') ?></option>
                    </select>
                    <input type="text" name="snatched1" size="6" value="<?= display_str($_GET['snatched1']) ?>" />
                    <input type="text" name="snatched2" size="6" value="<?= display_str($_GET['snatched2']) ?>" />
                </td>
                <? if (check_perms('users_mod')) { ?>
                    <td class="label nobr"><?= Lang::get('user', 'staff_notes') ?>:</td>
                    <td>
                        <input type="text" name="comment" size="20" value="<?= display_str($_GET['comment']) ?>" />
                    </td>
                <?  } else { ?>
                    <td class="label nobr"></td>
                    <td>
                    </td>
                <?  } ?>
            </tr>

            <tr>
                <td class="label nobr"><?= Lang::get('user', 'passkey') ?>:</td>
                <td>
                    <input type="text" name="passkey" size="20" value="<?= display_str($_GET['passkey']) ?>" />
                </td>
                <td class="label tooltip nobr" title="<?= Lang::get('user', 'supports_partial_url_matching') ?>"><?= Lang::get('user', 'avatar_url') ?>:</td>
                <td>
                    <input type="text" name="avatar" size="20" value="<?= display_str($_GET['avatar']) ?>" />
                </td>
                <td class="label nobr"><?= Lang::get('user', 'last_fm_username') ?>:</td>
                <td>
                    <input type="text" name="lastfm" size="20" value="<?= display_str($_GET['lastfm']) ?>" />
                </td>
            </tr>

            <tr>
                <td class="label nobr"><?= Lang::get('user', 'tracker_ip') ?>:</td>
                <td>
                    <input type="text" name="tracker_ip" size="20" value="<?= display_str($_GET['tracker_ip']) ?>" />
                </td>
                <td class="label nobr"><?= Lang::get('user', 'stylesheet') ?>:</td>
                <td>
                    <select name="stylesheet" id="stylesheet">
                        <option value=""><?= Lang::get('user', 'do_not_care') ?></option>
                        <? foreach ($Stylesheets as $Style) { ?>
                            <option value="<?= $Style['ID'] ?>" <? Format::selected('stylesheet', $Style['ID']) ?>><?= $Style['ProperName'] ?></option>
                        <?                  } ?>
                    </select>
                </td>
                <td class="label tooltip nobr" title="<?= Lang::get('user', 'country_code_title') ?>"><?= Lang::get('user', 'country_code') ?>:</td>
                <td width="30%">
                    <select name="cc_op">
                        <option value="equal" <? if ($_GET['cc_op'] === 'equal') {
                                                    echo ' selected="selected"';
                                                } ?>><?= Lang::get('user', 'equals') ?></option>
                        <option value="not_equal" <? if ($_GET['cc_op'] === 'not_equal') {
                                                        echo ' selected="selected"';
                                                    } ?>><?= Lang::get('user', 'not_equal') ?></option>
                    </select>
                    <input type="text" name="cc" size="2" value="<?= display_str($_GET['cc']) ?>" />
                </td>
            </tr>

            <tr>
                <td class="label nobr"><?= Lang::get('user', 'search_type') ?>:</td>
                <td>
                    <ul class="options_list nobullet">
                        <li>
                            <input type="radio" name="matchtype" id="strict_match_type" value="strict" <? if ($_GET['matchtype'] == 'strict' || !$_GET['matchtype']) {
                                                                                                            echo ' checked="checked"';
                                                                                                        } ?> />
                            <label class="tooltip" title="<?= Lang::get('user', 'search_type_strict_title') ?>" for="strict_match_type"><?= Lang::get('user', 'search_type_strict') ?></label>
                        </li>
                        <li>
                            <input type="radio" name="matchtype" id="fuzzy_match_type" value="fuzzy" <? if ($_GET['matchtype'] == 'fuzzy' || !$_GET['matchtype']) {
                                                                                                            echo ' checked="checked"';
                                                                                                        } ?> />
                            <label class="tooltip" title="<?= Lang::get('user', 'search_type_fuzzy_title') ?>" for="fuzzy_match_type"><?= Lang::get('user', 'search_type_fuzzy') ?></label>
                        </li>
                        <li>
                            <input type="radio" name="matchtype" id="regex_match_type" value="regex" <? if ($_GET['matchtype'] == 'regex') {
                                                                                                            echo ' checked="checked"';
                                                                                                        } ?> />
                            <label class="tooltip" title="<?= Lang::get('user', 'search_type_regex_title') ?>" for="regex_match_type"><?= Lang::get('user', 'search_type_regex') ?></label>
                        </li>
                    </ul>
                </td>
                <td class="label nobr"><?= Lang::get('user', 'order') ?>:</td>
                <td class="nobr">
                    <select name="order">
                        <?
                        foreach (array_shift($OrderVals) as $Cur) { ?>
                            <option value="<?= $Cur ?>" <? if (isset($_GET['order']) && $_GET['order'] == $Cur || (!isset($_GET['order']) && $Cur == 'Joined')) {
                                                            echo ' selected="selected"';
                                                        } ?>><?= $Cur ?></option>
                        <?                      } ?>
                    </select>
                    <select name="way">
                        <? foreach (array_shift($WayVals) as $Cur) { ?>
                            <option value="<?= $Cur ?>" <? if (isset($_GET['way']) && $_GET['way'] == $Cur || (!isset($_GET['way']) && $Cur == 'Descending')) {
                                                            echo ' selected="selected"';
                                                        } ?>><?= $Cur ?></option>
                        <?                      } ?>
                    </select>
                </td>
                <td class="label nobr"><?= Lang::get('user', 'number_of_emails') ?>:</td>
                <td>
                    <select name="emails_opt">
                        <option value="equal" <? if ($_GET['emails_opt'] === 'equal') {
                                                    echo ' selected="selected"';
                                                } ?>><?= Lang::get('user', 'equal') ?></option>
                        <option value="above" <? if ($_GET['emails_opt'] === 'above') {
                                                    echo ' selected="selected"';
                                                } ?>><?= Lang::get('user', 'above') ?></option>
                        <option value="below" <? if ($_GET['emails_opt'] === 'below') {
                                                    echo ' selected="selected"';
                                                } ?>><?= Lang::get('user', 'below') ?></option>
                    </select>
                    <input type="text" name="email_cnt" size="6" value="<?= display_str($_GET['email_cnt']) ?>" />
                </td>
            </tr>
            <tr>
                <td colspan="6" class="center">
                    <input type="submit" value="Search users" />
                </td>
            </tr>
        </table>
    </form>
</div>
<?
if ($RunQuery) {
    $Results = $DB->query($SQL);
    $DB->query('SELECT FOUND_ROWS()');
    list($NumResults) = $DB->next_record();
    $DB->set_query_id($Results);
} else {
    $DB->query('SET @nothing = 0');
    $NumResults = 0;
}
?>
<div class="linkbox">
    <?
    $Pages = Format::get_pages($Page, $NumResults, USERS_PER_PAGE, 11);
    echo $Pages;
    ?>
</div>
<div class="box pad center" id="user_search_results_box">
    <h2><?= number_format($NumResults) ?><?= Lang::get('user', 'space_results') ?></h2>
    <table width="100%">
        <tr class="colhead">
            <td><?= Lang::get('user', 'username') ?></td>
            <td><?= Lang::get('user', 'ratio') ?></td>
            <td><?= Lang::get('user', 'ip_address') ?></td>
            <td><?= Lang::get('user', 'email') ?></td>
            <td><?= Lang::get('user', 'joined') ?></td>
            <td><?= Lang::get('user', 'last_seen') ?></td>
            <td><?= Lang::get('user', 'uploaded') ?></td>
            <td><?= Lang::get('user', 'downloaded') ?></td>
            <td><?= Lang::get('user', 'downloads') ?></td>
            <td><?= Lang::get('global', 'snatched') ?></td>
            <td><?= Lang::get('user', 'invites') ?></td>
            <? if (isset($_GET['invitees']) && $_GET['invitees'] != 'off') { ?>
                <td><?= Lang::get('user', 'invitees') ?></td>
            <?      } ?>
        </tr>
        <?
        while (list($UserID, $Username, $Uploaded, $Downloaded, $Snatched, $Invitees, $Class, $Email, $Enabled, $IP, $Invites, $DisableInvites, $Warned, $Donor, $JoinDate, $LastAccess) = $DB->next_record()) { ?>
            <tr>
                <td><?= Users::format_username($UserID, true, true, true, true) ?></td>
                <td><?= Format::get_ratio_html($Uploaded, $Downloaded) ?></td>
                <td><?= display_str($IP) ?> (<?= Tools::get_country_code_by_ajax($IP) ?>)</td>
                <td><?= display_str($Email) ?></td>
                <td><?= time_diff($JoinDate) ?></td>
                <td><?= time_diff($LastAccess) ?></td>
                <td><?= Format::get_size($Uploaded) ?></td>
                <td><?= Format::get_size($Downloaded) ?></td>
                <? $DB->query("
				SELECT COUNT(ud.UserID)
				FROM users_downloads AS ud
					JOIN torrents AS t ON t.ID = ud.TorrentID
				WHERE ud.UserID = $UserID");
                list($Downloads) = $DB->next_record();
                $DB->set_query_id($Results);
                ?>
                <td><?= number_format((int)$Downloads) ?></td>
                <td><?= (is_numeric($Snatched) ? number_format($Snatched) : display_str($Snatched)) ?></td>
                <td><? if ($DisableInvites) {
                        echo 'X';
                    } else {
                        echo number_format($Invites);
                    } ?></td>
                <? if (isset($_GET['invitees']) && $_GET['invitees'] != 'off') { ?>
                    <td><?= number_format($Invitees) ?></td>
                <?      } ?>
            </tr>
        <?
        }
        ?>
    </table>
</div>
<div class="linkbox">
    <?= $Pages ?>
</div>
<?
View::show_footer();
?>