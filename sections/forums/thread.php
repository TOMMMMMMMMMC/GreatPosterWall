<?php
//TODO: Normalize thread_*_info don't need to waste all that ram on things that are already in other caches
/**********|| Page to show individual threads || ********************************\

Things to expect in $_GET:
    ThreadID: ID of the forum curently being browsed
    page:   The page the user's on.
    page = 1 is the same as no page

 ********************************************************************************/

//---------- Things to sort out before it can start printing/generating content

// Enable TOC
Text::$TOC = true;

// Check for lame SQL injection attempts
if (!isset($_GET['threadid']) || !is_number($_GET['threadid'])) {
    if (isset($_GET['topicid']) && is_number($_GET['topicid'])) {
        $ThreadID = $_GET['topicid'];
    } elseif (isset($_GET['postid']) && is_number($_GET['postid'])) {
        $DB->query("
			SELECT TopicID
			FROM forums_posts
			WHERE ID = $_GET[postid]");
        list($ThreadID) = $DB->next_record();
        if ($ThreadID) {
            header("Location: forums.php?action=viewthread&threadid=$ThreadID&postid=$_GET[postid]#post$_GET[postid]");
            die();
        } else {
            error(404);
        }
    } else {
        error(404);
    }
} else {
    $ThreadID = $_GET['threadid'];
}

if (isset($LoggedUser['PostsPerPage'])) {
    $PerPage = $LoggedUser['PostsPerPage'];
} else {
    $PerPage = POSTS_PER_PAGE;
}

//---------- Get some data to start processing

// Thread information, constant across all pages
$ThreadInfo = Forums::get_thread_info($ThreadID, true, true);
if ($ThreadInfo === null) {
    error(404);
}
$ForumID = $ThreadInfo['ForumID'];

$IsDonorForum = $ForumID == DONOR_FORUM ? true : false;

// Make sure they're allowed to look at the page
if (!Forums::check_forumperm($ForumID)) {
    error(403);
}
//Escape strings for later display
$ThreadTitle = display_str($ThreadInfo['Title']);
$ForumName = display_str($Forums[$ForumID]['Name']);

//Post links utilize the catalogue & key params to prevent issues with custom posts per page
if ($ThreadInfo['Posts'] > $PerPage) {
    if (isset($_GET['post']) && is_number($_GET['post'])) {
        $PostNum = $_GET['post'];
    } elseif (isset($_GET['postid']) && is_number($_GET['postid']) && $_GET['postid'] != $ThreadInfo['StickyPostID']) {
        $SQL = "
			SELECT COUNT(ID)
			FROM forums_posts
			WHERE TopicID = $ThreadID
				AND ID <= $_GET[postid]";
        if ($ThreadInfo['StickyPostID'] < $_GET['postid']) {
            $SQL .= " AND ID != $ThreadInfo[StickyPostID]";
        }
        $DB->query($SQL);
        list($PostNum) = $DB->next_record();
    } else {
        $PostNum = 1;
    }
} else {
    $PostNum = 1;
}
list($Page, $Limit) = Format::page_limit($PerPage, min($ThreadInfo['Posts'], $PostNum));
if (($Page - 1) * $PerPage > $ThreadInfo['Posts']) {
    $Page = ceil($ThreadInfo['Posts'] / $PerPage);
}
list($CatalogueID, $CatalogueLimit) = Format::catalogue_limit($Page, $PerPage, THREAD_CATALOGUE);

// Cache catalogue from which the page is selected, allows block caches and future ability to specify posts per page
if (!$Catalogue = $Cache->get_value("thread_{$ThreadID}_catalogue_$CatalogueID")) {
    $DB->query("
		SELECT
			p.ID,
			p.AuthorID,
			p.AddedTime,
			p.Body,
			p.EditedUserID,
			p.EditedTime,
			ed.Username
		FROM forums_posts AS p
			LEFT JOIN users_main AS ed ON ed.ID = p.EditedUserID
		WHERE p.TopicID = '$ThreadID'
			AND p.ID != '" . $ThreadInfo['StickyPostID'] . "'
		LIMIT $CatalogueLimit");
    $Catalogue = $DB->to_array(false, MYSQLI_ASSOC);
    if (!$ThreadInfo['IsLocked'] || $ThreadInfo['IsNotice'] || $ThreadInfo['IsSticky']) {
        $Cache->cache_value("thread_{$ThreadID}_catalogue_$CatalogueID", $Catalogue, 0);
    }
}
$Thread = Format::catalogue_select($Catalogue, $Page, $PerPage, THREAD_CATALOGUE);
$LastPost = end($Thread);
$LastPost = $LastPost['ID'];
$FirstPost = reset($Thread);
$FirstPost = $FirstPost['ID'];
if ($ThreadInfo['Posts'] <= $PerPage * $Page && $ThreadInfo['StickyPostID'] > $LastPost) {
    $LastPost = $ThreadInfo['StickyPostID'];
}

//Handle last read

if (!$ThreadInfo['IsLocked'] || $ThreadInfo['IsNotice'] || $ThreadInfo['IsSticky']) {

    $DB->query("
		SELECT PostID
		FROM forums_last_read_topics
		WHERE UserID = '$LoggedUser[ID]'
			AND TopicID = '$ThreadID'");
    list($LastRead) = $DB->next_record();
    if ($LastRead < $LastPost) {
        $DB->query("
			INSERT INTO forums_last_read_topics
				(UserID, TopicID, PostID)
			VALUES
				('$LoggedUser[ID]', '$ThreadID', '" . db_string($LastPost) . "')
			ON DUPLICATE KEY UPDATE
				PostID = '$LastPost'");
    }
}

//Handle subscriptions
$UserSubscriptions = Subscriptions::get_subscriptions();

if (empty($UserSubscriptions)) {
    $UserSubscriptions = array();
}

if (in_array($ThreadID, $UserSubscriptions)) {
    $Cache->delete_value('subscriptions_user_new_' . $LoggedUser['ID']);
}


$QuoteNotificationsCount = $Cache->get_value('notify_quoted_' . $LoggedUser['ID']);
if ($QuoteNotificationsCount === false || $QuoteNotificationsCount > 0) {
    $DB->query("
		UPDATE users_notify_quoted
		SET UnRead = false
		WHERE UserID = '$LoggedUser[ID]'
			AND Page = 'forums'
			AND PageID = '$ThreadID'
			AND PostID >= '$FirstPost'
			AND PostID <= '$LastPost'");
    $Cache->delete_value('notify_quoted_' . $LoggedUser['ID']);
}

// Start printing
View::show_header($ThreadInfo['Title'] . ' &lt; ' . $Forums[$ForumID]['Name'] . ' &lt; Forums', 'comments,subscriptions,bbcode,thumb', $IsDonorForum ? 'donor' : '');
?>
<div class="thin">
    <h2>
        <a href="forums.php"><?= Lang::get('forums', 'forums') ?></a> &gt;
        <a href="forums.php?action=viewforum&amp;forumid=<?= $ThreadInfo['ForumID'] ?>"><?= $ForumName ?></a> &gt;
        <?= $ThreadTitle ?>
    </h2>
    <div class="linkbox">
        <div class="center">
            <a href="reports.php?action=report&amp;type=thread&amp;id=<?= $ThreadID ?>" class="brackets"><?= Lang::get('forums', 'report') ?></a>
            <a href="#" onclick="Subscribe(<?= $ThreadID ?>);return false;" id="subscribelink<?= $ThreadID ?>" class="brackets"><?= (in_array($ThreadID, $UserSubscriptions) ? Lang::get('global', 'unsubscribe') :  Lang::get('global', 'subscribe')) ?></a>
            <a href="#" onclick="$('#searchthread').gtoggle(); this.innerHTML = (this.innerHTML == 'Search this thread' ? 'Hide search' : 'Search this thread'); return false;" class="brackets"><?= Lang::get('forums', 'search') ?></a>
            <? if (check_perms('site_debug')) { ?> <a href="tools.php?action=service_stats" class="brackets"><?= Lang::get('forums', 'service_stats') ?></a> <? } ?>
        </div>
        <div id="searchthread" class="hidden center">
            <div style="display: inline-block;">
                <h3><?= Lang::get('forums', 'search_this_thread') ?>:</h3>
                <form class="search_form" name="forum_thread" action="forums.php" method="get">
                    <input type="hidden" name="action" value="search" />
                    <input type="hidden" name="threadid" value="<?= $ThreadID ?>" />
                    <table cellpadding="6" cellspacing="1" border="0" class="layout border">
                        <tr>
                            <td><strong><?= Lang::get('forums', 'search_for') ?></strong></td>
                            <td><input type="search" id="searchbox" name="search" size="70" /></td>
                        </tr>
                        <tr>
                            <td><strong><?= Lang::get('forums', 'post_by') ?></strong></td>
                            <td><input type="search" id="username" name="user" placeholder="Username" size="70" /></td>
                        </tr>
                        <tr>
                            <td colspan="2" style="text-align: center;">
                                <input type="submit" name="submit" value="<?= Lang::get('forums', 'search') ?>" />
                            </td>
                        </tr>
                    </table>
                </form>
                <br />
            </div>
        </div>
        <?
        $Pages = Format::get_pages($Page, $ThreadInfo['Posts'], $PerPage, 9);
        echo $Pages;
        ?>
    </div>
    <?
    if ($ThreadInfo['NoPoll'] == 0) {
        if (!list($Question, $Answers, $Votes, $Featured, $Closed, $MaxCount) = $Cache->get_value("polls_$ThreadID")) {
            $DB->query("
			SELECT Question, Answers, Featured, Closed, MaxCount
			FROM forums_polls
			WHERE TopicID = '$ThreadID'");
            list($Question, $Answers, $Featured, $Closed, $MaxCount) = $DB->next_record(MYSQLI_NUM, array(1));
            $Answers = unserialize($Answers);
            $DB->query("
			SELECT Vote, COUNT(UserID)
			FROM forums_polls_votes
			WHERE TopicID = '$ThreadID'
			GROUP BY Vote");
            $VoteArray = $DB->to_array(false, MYSQLI_NUM);

            $Votes = array();
            foreach ($VoteArray as $VoteSet) {
                list($Key, $Value) = $VoteSet;
                $Votes[$Key] = $Value;
            }

            foreach (array_keys($Answers) as $i) {
                if (!isset($Votes[$i])) {
                    $Votes[$i] = 0;
                }
            }
            $Cache->cache_value("polls_$ThreadID", array($Question, $Answers, $Votes, $Featured, $Closed, $MaxCount), 0);
        }

        if (!empty($Votes)) {
            $TotalVotes = array_sum($Votes);
            $MaxVotes = max($Votes);
            $DB->query("SELECT count(distinct `UserID`) FROM `forums_polls_votes` WHERE `TopicID`='$ThreadID' and vote!=0");
            list($PeopleCount) = $DB->next_record();
        } else {
            $TotalVotes = 0;
            $MaxVotes = 0;
            $PeopleCount = 0;
        }

        $RevealVoters = in_array($ForumID, $ForumsRevealVoters);
        //Polls lose the you voted arrow thingy
        $DB->query("
		SELECT Vote
		FROM forums_polls_votes
		WHERE UserID = '" . $LoggedUser['ID'] . "'
            AND TopicID = '$ThreadID'");
        $UserResponses = $DB->to_array();
        $BlankVote = false;
        foreach ($UserResponses as $UserResponse) {
            $UserResponse = $UserResponse[0];
            if (!empty($UserResponse) && $UserResponse != 0) {
                $Answers[$UserResponse] = '&raquo; ' . $Answers[$UserResponse];
            } else {
                if (!empty($UserResponse) && $RevealVoters) {
                    $Answers[$UserResponse] = '&raquo; ' . $Answers[$UserResponse];
                }
            }
            if (!$UserResponse) {
                $BlankVote = true;
            }
        }

    ?>
        <div class="box thin clear threadpoll">
            <div class="head colhead_dark"><strong><?= Lang::get('forums', 'poll') ?><? if ($Closed) {
                                                                                            echo ' [' . Lang::get('forums', 'closed') . ']';
                                                                                        } ?><? if ($Featured && $Featured !== '0000-00-00 00:00:00') {
                                                                                            echo ' [' . Lang::get('forums', 'featured') . ']';
                                                                                        } ?></strong> <a href="#" onclick="$('#threadpoll').gtoggle(); log_hit(); return false;" class="brackets"><?= Lang::get('forums', 'view') ?></a></div>
            <div class="pad<? if (/*$LastRead !== null || */$ThreadInfo['IsLocked']) {
                                echo ' hidden';
                            } ?>" id="threadpoll">
                <p><strong><?= display_str($Question) . " (" . Lang::get("forums", 'limited1') . " $MaxCount " . Lang::get('forums', 'limited2') . ")" ?></strong></p>
                <? if ($UserResponse !== null || $Closed || !Forums::check_forumperm($ForumID)) { ?>
                    <ul class="poll nobullet">
                        <?
                        if (!$RevealVoters) {
                            foreach ($Answers as $i => $Answer) {
                                if (!empty($Votes[$i]) && $TotalVotes > 0) {
                                    $Ratio = $Votes[$i] / $MaxVotes;
                                    $Percent = $Votes[$i] / $TotalVotes;
                                } else {
                                    $Ratio = 0;
                                    $Percent = 0;
                                }
                        ?>
                                <li><?= display_str($Answer) ?> (<?= $Votes[$i] . ", " . number_format($Percent * 100, 2) ?>%)</li>
                                <li class="graph">
                                    <span class="left_poll"></span>
                                    <span class="center_poll" style="width: <?= round($Ratio * 750) ?>px;"></span>
                                    <span class="right_poll"></span>
                                </li>
                            <?            }
                            if ($Votes[0] > 0) {
                            ?>
                                <li><?= ($BlankVote ? '&raquo; ' : '') ?>(<?= Lang::get('forums', 'blank') ?>) (<?= $Votes[0] . ", " . number_format((float) ($Votes[0] / $TotalVotes * 100), 2) ?>%)</li>
                                <li class="graph">
                                    <span class="left_poll"></span>
                                    <span class="center_poll" style="width: <?= round(($Votes[0] / $MaxVotes) * 750) ?>px;"></span>
                                    <span class="right_poll"></span>
                                </li>
                            <?            } ?>
                    </ul>
                    <br />
                    <strong><?= Lang::get('forums', 'votes') ?>:</strong> <?= number_format($TotalVotes) ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong><?= Lang::get('forums', 'voters') ?>:</strong> <?= $PeopleCount ?><br /><br />
                <?
                        } else {
                            //Staff forum, output voters, not percentages
                            include(SERVER_ROOT . '/sections/staff/functions.php');
                            $Staff = get_staff();

                            $StaffNames = array();
                            foreach ($Staff as $Staffer) {
                                $StaffNames[] = $Staffer['Username'];
                            }

                            $DB->query("
				SELECT
					fpv.Vote AS Vote,
					GROUP_CONCAT(um.Username SEPARATOR ', ')
				FROM users_main AS um
					LEFT JOIN forums_polls_votes AS fpv ON um.ID = fpv.UserID
				WHERE TopicID = $ThreadID
				GROUP BY fpv.Vote");

                            $StaffVotesTmp = $DB->to_array();
                            $StaffCount = count($StaffNames);

                            $StaffVotes = array();
                            foreach ($StaffVotesTmp as $StaffVote) {
                                list($Vote, $Names) = $StaffVote;
                                $StaffVotes[$Vote] = $Names;
                                $Names = explode(', ', $Names);
                                $StaffNames = array_diff($StaffNames, $Names);
                            }
                ?> <ul style="list-style: none;" id="poll_options">
                        <?
                            foreach ($Answers as $i => $Answer) {
                        ?>
                            <li>
                                <a href="forums.php?action=change_vote&amp;threadid=<?= $ThreadID ?>&amp;auth=<?= $LoggedUser['AuthKey'] ?>&amp;vote=<?= (int) $i ?>"><?= display_str($Answer == '' ? 'Blank' : $Answer) ?></a>
                                - <?= $StaffVotes[$i] ?>&nbsp;(<?= number_format(((float) $Votes[$i] / $TotalVotes) * 100, 2) ?>%)
                                <a href="forums.php?action=delete_poll_option&amp;threadid=<?= $ThreadID ?>&amp;auth=<?= $LoggedUser['AuthKey'] ?>&amp;vote=<?= (int) $i ?>" onclick="return confirm('<?= Lang::get('forums', 'delete_poll_option_title') ?>');" class="brackets tooltip" title="<?= Lang::get('forums', 'delete_poll_option') ?>">X</a>
                            </li>
                        <?            } ?>
                        <li>
                            <a href="forums.php?action=change_vote&amp;threadid=<?= $ThreadID ?>&amp;auth=<?= $LoggedUser['AuthKey'] ?>&amp;vote=0"><?= ($UserResponse == '0' ? '&raquo; ' : '') ?><?= Lang::get('forums', 'blank') ?></a> - <?= $StaffVotes[0] ?>&nbsp;(<?= number_format(((float) $Votes[0] / $TotalVotes) * 100, 2) ?>%)
                        </li>
                    </ul>
                    <?
                            if ($ForumID == STAFF_FORUM) {
                    ?>
                        <br />
                        <strong><?= Lang::get('forums', 'votes') ?>:</strong> <?= number_format($StaffCount - count($StaffNames)) ?> / <?= $StaffCount ?> current staff, <?= number_format($TotalVotes) ?> <?= Lang::get('forums', 'missing_votes') ?>
                        <br />
                        <strong><?= Lang::get('forums', 'missing_votes') ?>:</strong> <?= implode(", ", $StaffNames);
                                                                                        echo "\n"; ?>
                        <br /><br />
                    <?
                            }
                    ?>
                    <a href="#" onclick="AddPollOption(<?= $ThreadID ?>); return false;" class="brackets">+</a>
                <?
                        }
                    } else {
                        //User has not voted
                ?>
                <div id="poll_container">
                    <form class="vote_form" name="poll" id="poll" action="">
                        <input type="hidden" name="action" value="poll" />
                        <input type="hidden" name="auth" value="<?= $LoggedUser['AuthKey'] ?>" />
                        <input type="hidden" name="large" value="1" />
                        <input type="hidden" name="topicid" value="<?= $ThreadID ?>" />
                        <ul style="list-style: none;" id="poll_options">
                            <? foreach ($Answers as $i => $Answer) { ?>
                                <li>
                                    <input class="poll_answer" type="checkbox" name="vote[]" id="answer_<?= $i ?>" value="<?= $i ?>" onclick="PollCount(<?= $MaxCount ?>)" />
                                    <label for="answer_<?= $i ?>"><?= display_str($Answer) ?></label>
                                </li>
                            <?        } ?>
                            <li>
                                <br />
                                <input type="checkbox" name="vote[]" id="answer_0" value="0" onclick="PollCount(0)" /> <label for="answer_0"><?= Lang::get('forums', 'blank_show_results') ?></label><br />
                            </li>
                        </ul>
                        <? if ($ForumID == STAFF_FORUM) { ?>
                            <a href="#" onclick="AddPollOption(<?= $ThreadID ?>); return false;" class="brackets">+</a>
                            <br />
                            <br />
                        <?        } ?>
                        <input type="button" onclick="ajax.post('index.php','poll',function(response) { $('#poll_container').raw().innerHTML = response});" value="Vote" />
                    </form>
                </div>
                <?    }
                    if (check_perms('forums_polls_moderate') && !$RevealVoters) {
                        if (!$Featured || $Featured == '0000-00-00 00:00:00') {
                ?>
                    <form class="manage_form" name="poll" action="forums.php" method="post">
                        <input type="hidden" name="action" value="poll_mod" />
                        <input type="hidden" name="auth" value="<?= $LoggedUser['AuthKey'] ?>" />
                        <input type="hidden" name="topicid" value="<?= $ThreadID ?>" />
                        <input type="hidden" name="feature" value="1" />
                        <input type="submit" onclick="return confirm('<?= Lang::get('forums', 'submit_poll_title') ?>');" value="Feature" />
                    </form>
                <?        } ?>
                <form class="manage_form" name="poll" action="forums.php" method="post">
                    <input type="hidden" name="action" value="poll_mod" />
                    <input type="hidden" name="auth" value="<?= $LoggedUser['AuthKey'] ?>" />
                    <input type="hidden" name="topicid" value="<?= $ThreadID ?>" />
                    <input type="hidden" name="close" value="1" />
                    <input type="submit" value="<?= (!$Closed ? 'Close' : 'Open') ?>" />
                </form>
            <?    } ?>
            </div>
        </div>
    <?
    } //End Polls

    //Sqeeze in stickypost
    if ($ThreadInfo['StickyPostID']) {
        if ($ThreadInfo['StickyPostID'] != $Thread[0]['ID']) {
            array_unshift($Thread, $ThreadInfo['StickyPost']);
        }
        if ($ThreadInfo['StickyPostID'] != $Thread[count($Thread) - 1]['ID']) {
            $Thread[] = $ThreadInfo['StickyPost'];
        }
    }
    ?>
    <script>
        function FZ(event) {
            var id = event.data.id,
                flag = event.data.flag
            if (id == 0) {
                $.get("forums.php", {
                        action: "ajax_get_jf",
                        r: flag,
                        j: 0,
                        c: com
                    },
                    function(data) {
                        var obj = eval("(" + data + ")");
                        if (obj.ret == "success") {
                            alert('成功');
                            window.location.reload();
                        } else {
                            alert('失败');
                        }
                    });
            } else {
                var select = $("#select_" + id).val(),
                    com = $("#input_comment_" + id).val()
                <?
                if (check_perms('admin_send_bonus') || isset($LoggedUser['ExtraClasses']['31'])) {
                ?>
                    var stm = $("#sys_" + id).prop('checked');
                <?
                }
                ?>
                if (select == 0) select = $("#input_" + id).val()
                $.get("forums.php", {
                        action: "ajax_get_jf",
                        r: flag,
                        j: select,
                        c: com,
                        <?
                        if (check_perms('admin_send_bonus') || isset($LoggedUser['ExtraClasses']['31'])) {
                        ?>
                            s: stm
                        <?
                        }
                        ?>
                    },
                    function(data) {
                        var obj = eval("(" + data + ")");
                        if (obj.ret == "success") {
                            alert('成功');
                            window.location.reload();
                        } else {
                            alert('失败' + obj.msg);
                        }
                    });
            }

        }

        function select_jf(event) {
            var id = event.data.id,
                select = $("#select_" + id).val()
            if (select == 0) $("#input_" + id).show()
            else $("#input_" + id).hide()
        }
        <?
        if (check_perms('admin_send_bonus') || isset($LoggedUser['ExtraClasses']['31'])) {
        ?>

            function isSys(event) {
                var id = event.data.id;
                $(".psn_" + id + ",.sysn_" + id).toggle()
                if ($("#sys_" + id).prop('checked')) {
                    $("#button_" + id).text("系统奖励")
                    $(".sysn_" + id + ":first").attr("selected", true);
                } else {
                    $("#button_" + id).text("个人奖励")
                    $(".psn_" + id + ":first").attr("selected", true);
                }
            }
        <? } ?>
    </script>
    <?
    $DB->query("
    select p.id, 
        count(t.fromuserid) count, 
        (
            select count(1) 
                from thumb where
                itemid = p.id
                and fromuserid=" . $LoggedUser['ID'] . "
                and type = 'post'
        ) 'on'
    from forums_posts as p 
    left join thumb as t 
        on p.id = t.itemid 
        and t.type = 'post' 
    WHERE p.TopicID = $ThreadID
    group by p.id");
    $ThumbCounts = $DB->to_array('id');
    foreach ($Thread as $Key => $Post) {
        list($PostID, $AuthorID, $AddedTime, $Body, $EditedUserID, $EditedTime, $EditedUsername) = array_values($Post);
        list($AuthorID, $Username, $PermissionID, $Paranoia, $Artist, $Donor, $Found, $Warned, $Avatar, $Enabled, $UserTitle) = array_values(Users::user_info($AuthorID));

    ?>
        <?
        //var_dump(check_perms('forum_moderator'));
        if ($ThreadInfo['hiddenreplies'] == 1 && $Key != 0 && !check_perms('forums_see_hidden') && $ThreadInfo['OP'] != $LoggedUser['ID'] && $AuthorID != $LoggedUser['ID']) { //check_perms('forum_moderator')
            $Body = '回帖仅楼主可见。';
        }
        ?>
        <div class="table_container border mgb">
            <table class="forum_post wrap_overflow box vertical_margin<?
                                                                        if (((!$ThreadInfo['IsLocked'] || $ThreadInfo['IsNotice'] || $ThreadInfo['IsSticky'])
                                                                                && $PostID > $LastRead
                                                                                && strtotime($AddedTime) > $LoggedUser['CatchupTime']) || (isset($RequestKey) && $Key == $RequestKey)
                                                                        ) {
                                                                            echo ' forum_unread';
                                                                        }
                                                                        if (!Users::has_avatars_enabled()) {
                                                                            echo ' noavatar';
                                                                        }
                                                                        if ($ThreadInfo['OP'] == $AuthorID) {
                                                                            echo ' important_user';
                                                                        }
                                                                        if ($PostID == $ThreadInfo['StickyPostID']) {
                                                                            echo ' sticky_post';
                                                                        } ?>" id="post<?= $PostID ?>">
                <colgroup>
                    <? if (Users::has_avatars_enabled()) { ?>
                        <col class="col_avatar" />
                    <?     } ?>
                    <col class="col_post_body" />
                </colgroup>
                <tr class="colhead_dark">
                    <td colspan="<?= Users::has_avatars_enabled() ? 2 : 1 ?>">
                        <div style="float: left;"><a class="post_id" href="forums.php?action=viewthread&amp;threadid=<?= $ThreadID ?>&amp;postid=<?= $PostID ?>#post<?= $PostID ?>">#<?= $PostID ?></a>
                            <?= Users::format_username($AuthorID, true, true, true, true, true, $IsDonorForum, false, true);
                            echo "\n"; ?>
                            <?= time_diff($AddedTime, 2);
                            echo "\n"; ?>
                            - <a href="#quickpost" id="quote_<?= $PostID ?>" onclick="Quote('<?= $PostID ?>', '<?= $Username ?>', true);" class="brackets"><?= Lang::get('forums', 'quote') ?></a>
                            <? if ((!$ThreadInfo['IsLocked'] && Forums::check_forumperm($ForumID, 'Write') && $AuthorID == $LoggedUser['ID']) || check_perms('site_moderate_forums') || ($AuthorID == $LoggedUser['ID'] && isset($LoggedUser['ExtraClasses']['31']))) { ?>
                                - <a href="#post<?= $PostID ?>" onclick="Edit_Form('<?= $PostID ?>', '<?= $Key ?>');" class="brackets"><?= Lang::get('global', 'edit') ?></a>
                            <?
                            }
                            if (check_perms('site_admin_forums') && $ThreadInfo['Posts'] > 1) { ?>
                                - <a href="#post<?= $PostID ?>" onclick="Delete('<?= $PostID ?>');" class="brackets"><?= Lang::get('global', 'delete') ?></a>
                            <?
                            }
                            if ($PostID == $ThreadInfo['StickyPostID']) { ?>
                                <strong><span class="sticky_post_label" class="brackets"><?= Lang::get('forums', 'sticky') ?></span></strong>
                                <? if (check_perms('site_moderate_forums')) { ?>
                                    - <a href="forums.php?action=sticky_post&amp;threadid=<?= $ThreadID ?>&amp;postid=<?= $PostID ?>&amp;remove=true&amp;auth=<?= $LoggedUser['AuthKey'] ?>" title="<?= Lang::get('forums', 'unsticky_title') ?>" class="brackets tooltip">X</a>
                                <?
                                }
                            } else {
                                if (check_perms('site_moderate_forums')) {
                                ?>
                                    - <a href="forums.php?action=sticky_post&amp;threadid=<?= $ThreadID ?>&amp;postid=<?= $PostID ?>&amp;auth=<?= $LoggedUser['AuthKey'] ?>" title="<?= Lang::get('forums', 'sticky_title') ?>" class="brackets tooltip">&#x21d5;</a>
                            <?
                                }
                            }
                            ?>
                        </div>
                        <div id="bar<?= $PostID ?>" style="float: right;">
                            <span id="thumb<?= $PostID ?>" <?= $ThumbCounts[$PostID]['on'] ? 'style="display: none;"' : '' ?>><?=
                                                                                                                                $LoggedUser['ID'] == $AuthorID ? "<i title=\"" . Lang::get('forums', 'cant_like_yourself') . "\" class=\"far fa-thumbs-up\"></i>" : "<a href=\"javascript:void(0);\" onclick=\"thumb($PostID, $AuthorID, 'post')\"><i class=\"far fa-thumbs-up\"></i></a>"
                                                                                                                                ?>
                            </span>
                            <span id="unthumb<?= $PostID ?>" <?= !$ThumbCounts[$PostID]['on'] ? 'style="display: none;"' : '' ?>><a href="javascript:void(0);" onclick="unthumb(<?= $PostID ?>, <?= $AuthorID ?>, 'post')"><i class="fas fa-thumbs-up"></i></a></span>
                            <span id="thumbcnt<?= $PostID ?>"><?= $ThumbCounts[$PostID]['count'] ? $ThumbCounts[$PostID]['count'] : Lang::get('forums', 'like') ?></span>
                            - <a href="reports.php?action=report&amp;type=post&amp;id=<?= $PostID ?>" class="brackets"><?= Lang::get('forums', 'report') ?></a>
                            <?
                            if (check_perms('users_warn') && $AuthorID != $LoggedUser['ID']) {
                                $AuthorInfo = Users::user_info($AuthorID);
                                if ($LoggedUser['Class'] >= $AuthorInfo['Class']) {
                            ?>
                                    <form class="manage_form hidden" name="user" id="warn<?= $PostID ?>" action="" method="post">
                                        <input type="hidden" name="action" value="warn" />
                                        <input type="hidden" name="postid" value="<?= $PostID ?>" />
                                        <input type="hidden" name="userid" value="<?= $AuthorID ?>" />
                                        <input type="hidden" name="key" value="<?= $Key ?>" />
                                    </form>
                                    - <a href="#" onclick="$('#warn<?= $PostID ?>').raw().submit(); return false;" class="brackets"><?= Lang::get('forums', 'warn') ?></a>
                            <?        }
                            }
                            ?>
                            &nbsp;
                            <a href="#">&uarr;</a>
                        </div>
                    </td>
                </tr>
                <tr>
                    <? if (Users::has_avatars_enabled()) { ?>
                        <td class="avatar" valign="top">
                            <?= Users::show_avatar($Avatar, $AuthorID, $Username, $HeavyInfo['DisableAvatars'], 150, true);
                            ?>
                        </td>
                    <?    }
                    G::$DB->query("
                        SELECT `ID`, `TopicID`, `AuthorID`, `LogTime`, `Sentuid`, `Sentjf`, `Comment`, `Sys`
                        FROM `forums_posts_jf_log`
                        WHERE TopicID = $ThreadID and PostID=$PostID
                        ORDER BY ID ASC");
                    $JF_log = G::$DB->to_array();
                    ?>

                    <td class="body" valign="top" <? if (!Users::has_avatars_enabled()) {
                                                        echo ' colspan="2"';
                                                    } ?>>
                        <div id="content<?= $PostID ?>">
                            <div class="content_text"><?= Text::full_format($Body) ?></div>
                            <?
                            if (($ThreadInfo['hiddenreplies'] != 1 || check_perms('forums_see_hidden') || $ThreadInfo['OP'] == $LoggedUser['ID'] && $AuthorID != $LoggedUser['ID']) && $JF_log) {
                            ?>
                                <h3 class="reward_head"><span class="reward_head"><i><?= Lang::get('forums', 'bonus_giving') ?></i></span></h3>
                                <dl id="ratelog_26927997" class="rate">
                                    <dd style="margin:0">
                                        <div id="post_rate_26927997"></div>
                                        <table class="ratl">
                                            <thead>
                                                <tr>
                                                    <th><?= Lang::get('forums', 'bonus_giver') ?></th>
                                                    <th><?= Lang::get('forums', 'bonus') ?></th>
                                                    <th><?= Lang::get('forums', 'comments') ?></th>
                                                </tr>
                                                </th>
                                            <tbody>
                                                <?
                                                foreach ($JF_log as $k => $val) {
                                                    if (is_array($val)) {
                                                        if ($val['Sys']) {
                                                ?>
                                                            <script>
                                                                $(document).ready(function() {
                                                                    $('#delete_link_<?= $val['ID'] ?>').bind('click', {
                                                                        id: 0,
                                                                        flag: '<?= bin2hex(openssl_encrypt($LoggedUser['ID'] . '|' . $ThreadID . '|' . $AuthorID . '|' . $PostID . '|' . $val['ID'], 'AES-128-CBC', 'hfjs05@^eIU$AfJW', OPENSSL_RAW_DATA, '0000000000000000')); ?>'
                                                                    }, FZ)
                                                                })
                                                            </script>
                                                        <? } ?>
                                                        <tr <? if ($k > 2) echo "class=\"can_hide_$PostID\" style=\"display: none;\""; ?>>
                                                            <td><?
                                                                if (check_perms('admin_send_bonus') && $val['Sys']) {
                                                                    echo ("<a id=\"delete_link_" . $val['ID'] . "\" href=\"javascript:void(0)\">×</a> ");
                                                                }
                                                                echo Users::format_username($val['Sentuid'], false, false, false, false, false, $IsDonorForum); ?></td>
                                                            <td><?= $val['Sentjf'] == 0 ? "" : $val['Sentjf'] ?></td>
                                                            <td title="<?= $val['Comment'] ?>"><?= $val['Comment'] ?></td>
                                                        </tr>
                                                    <? }
                                                }
                                                if (count($JF_log) > 3) { ?>
                                                    <tr>
                                                        <td><a id="show_link_<?= $PostID ?>" href="javascript:$('.can_hide_<?= $PostID ?>').show();$('#show_link_<?= $PostID ?>').hide()">...</a></td>
                                                        <td></td>
                                                        <td></td>
                                                    </tr>
                                                <? } ?>

                                            </tbody>
                                        </table>
                                    </dd>
                                </dl>
                            <? } ?>
                            <div class="edited_and_bonus">
                                <? if ($ThreadInfo['hiddenreplies'] != 1) { ?>
                                    <hr />
                                <? } elseif ($ThreadInfo['hiddenreplies'] == 1 && $AuthorID == $LoggedUser['ID'] || check_perms('forums_see_hidden') || $ThreadInfo['OP'] == $LoggedUser['ID']) { ?>
                                    <hr />
                                <? } ?>
                                <? if ((!$ThreadInfo['hiddenreplies'] || check_perms('forums_see_hidden')) && $EditedUserID) { ?>
                                    <div class="last_edited">
                                        <? if (check_perms('site_admin_forums')) { ?>
                                            <a href="#content<?= $PostID ?>" onclick="LoadEdit('forums', <?= $PostID ?>, 1); return false;">&laquo;</a>
                                        <?        } ?>
                                        <?= Lang::get('forums', 'last_edit_by_before') ?><?= Users::format_username($EditedUserID, false, false, false, false, false, $IsDonorForum) ?><?= Lang::get('forums', 'last_edit_by_after') ?> <?= time_diff($EditedTime, 2, true, true) ?>
                                    </div>
                                <?    }
                                if ($ThreadInfo['hiddenreplies'] != 1 || check_perms('forums_see_hidden') || $ThreadInfo['OP'] == $LoggedUser['ID']) {
                                    //if (check_perms('admin_send_bonus')) {
                                ?>
                                    <script>
                                        $(document).ready(function() {
                                            $('#select_<?= $PostID ?>').bind('change', {
                                                id: <?= $PostID ?>
                                            }, select_jf)
                                            $('#button_<?= $PostID ?>').bind('click', {
                                                id: <?= $PostID ?>,
                                                flag: '<?= bin2hex(openssl_encrypt($LoggedUser['ID'] . '|' . $ThreadID . '|' . $AuthorID . '|' . $PostID, 'AES-128-CBC', 'hfjs05@^eIU$AfJW', OPENSSL_RAW_DATA, '0000000000000000')); ?>'
                                            }, FZ)
                                            <?
                                            if (check_perms('admin_send_bonus') || isset($LoggedUser['ExtraClasses']['31'])) {
                                            ?>
                                                $('#sys_<?= $PostID ?>').bind('click', {
                                                    id: <?= $PostID ?>,
                                                }, isSys)
                                            <? } ?>
                                        })
                                    </script>
                                    <div class="bonus_as_reward">
                                        <span><?= Lang::get('forums', 'bonus_reward') ?></span>
                                        <select id="select_<?= $PostID ?>">
                                            <?
                                            if (check_perms('admin_send_bonus')) {
                                            ?>
                                                <option value="10" class="psn_<?= $PostID ?>">10</option>
                                                <option value="50" class="psn_<?= $PostID ?>">50</option>
                                                <option value="200" class="psn_<?= $PostID ?>">200</option>
                                                <option value="500" class="psn_<?= $PostID ?>">500</option>
                                                <option value="100" class="sysn_<?= $PostID ?>" style="display:none;">100</option>
                                                <option value="500" class="sysn_<?= $PostID ?>" style="display:none;">500</option>
                                                <option value="1000" class="sysn_<?= $PostID ?>" style="display:none;">1000</option>
                                                <option value="3000" class="sysn_<?= $PostID ?>" style="display:none;">3000</option>
                                                <option value="0" class="sysn_<?= $PostID ?>" style="display:none;"><?= Lang::get('forums', 'customize') ?></option>
                                            <?
                                            } else if (isset($LoggedUser['ExtraClasses']['31'])) {
                                            ?>
                                                <option value="0" class="sysn_<?= $PostID ?>">TC</option>
                                                <option value="10" class="psn_<?= $PostID ?>" style="display:none;">10</option>
                                                <option value="50" class="psn_<?= $PostID ?>" style="display:none;">50</option>
                                                <option value="200" class="psn_<?= $PostID ?>" style="display:none;">200</option>
                                                <option value="500" class="psn_<?= $PostID ?>" style="display:none;">500</option>
                                            <?
                                            } else {
                                            ?>
                                                <option value="10">10</option>
                                                <option value="50">50</option>
                                                <option value="200">200</option>
                                                <option value="500">500</option>
                                            <?
                                            }
                                            ?>
                                        </select>
                                        <input id="input_<?= $PostID ?>" type="number" max="5000" min="1" style="display: none;">
                                        <?
                                        if (check_perms('admin_send_bonus')) {
                                        ?>
                                            <span>
                                                <input id="sys_<?= $PostID ?>" type="checkbox" name="system">
                                                <label for="sys_<?= $PostID ?>"><?= Lang::get('forums', 'as_system') ?></label>
                                            </span>
                                        <?
                                        } else if (isset($LoggedUser['ExtraClasses']['31'])) {
                                        ?>
                                            <span>
                                                <input id="sys_<?= $PostID ?>" type="checkbox" name="system" checked="checked">
                                                <label for="sys_<?= $PostID ?>"><?= Lang::get('forums', 'only_tc') ?></label>
                                            </span>
                                        <?
                                        }
                                        ?>
                                        <input id="input_comment_<?= $PostID ?>" maxlength=20 type="text" placeholder="<?= Lang::get('forums', 'comment_optional') ?>">
                                        <button id="button_<?= $PostID ?>"><?= check_perms('admin_send_bonus') ? Lang::get('forums', 'personal_reward') : Lang::get('forums', 'confirm') ?></button>
                                    </div>
                                    <? //} 
                                    ?>

                            </div> <? } ?>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    <? } ?>

    <div class="breadcrumbs">
        <a href="forums.php"><?= Lang::get('forums', 'forums') ?></a> &gt;
        <a href="forums.php?action=viewforum&amp;forumid=<?= $ThreadInfo['ForumID'] ?>"><?= $ForumName ?></a> &gt;
        <?= $ThreadTitle ?>
    </div>
    <div class="linkbox">
        <?= $Pages ?>
    </div>
    <?
    if (!$ThreadInfo['IsLocked'] || check_perms('site_moderate_forums')) {
        if (Forums::check_forumperm($ForumID, 'Write') && !$LoggedUser['DisablePosting']) {
            View::parse('generic/reply/quickreply.php', array(
                'InputTitle' => Lang::get('forums', 'post_reply'),
                'InputName' => 'thread',
                'InputID' => $ThreadID,
                'ForumID' => $ForumID,
                'TextareaCols' => 90
            ));
        }
    }
    if (check_perms('site_moderate_forums')) {
        G::$DB->query("
			SELECT ID, AuthorID, AddedTime, Body
			FROM forums_topic_notes
			WHERE TopicID = $ThreadID
			ORDER BY ID ASC");
        $Notes = G::$DB->to_array();
    ?>
        <br />
        <h3 id="thread_notes"><?= Lang::get('forums', 'thread_notes') ?></h3> <a href="#" onclick="$('#thread_notes_table').gtoggle(); return false;" class="brackets"><?= Lang::get('global', 'toggle') ?></a>
        <form action="forums.php" method="post">
            <input type="hidden" name="action" value="take_topic_notes" />
            <input type="hidden" name="auth" value="<?= $LoggedUser['AuthKey'] ?>" />
            <input type="hidden" name="topicid" value="<?= $ThreadID ?>" />
            <table cellpadding="6" cellspacing="1" border="0" width="100%" class="layout border hidden" id="thread_notes_table">
                <?
                foreach ($Notes as $Note) {
                ?>
                    <tr>
                        <td><?= Users::format_username($Note['AuthorID']) ?> (<?= time_diff($Note['AddedTime'], 2, true, true) ?>)</td>
                        <td><?= Text::full_format($Note['Body']) ?></td>
                    </tr>
                <?
                }
                ?>
                <tr>
                    <td colspan="2" class="center">
                        <div class="field_div textarea_wrap"><textarea id="topic_notes" name="body" cols="90" rows="3" onkeyup="resize('threadnotes');" style=" margin: 0px; width: 735px;"></textarea></div>
                        <input type="submit" value="Save" />
                    </td>
                </tr>
            </table>
        </form>
        <br />
        <h3><?= Lang::get('forums', 'edit_thread') ?></h3>
        <form class="edit_form" name="forum_thread" action="forums.php" method="post">
            <div>
                <input type="hidden" name="action" value="mod_thread" />
                <input type="hidden" name="auth" value="<?= $LoggedUser['AuthKey'] ?>" />
                <input type="hidden" name="threadid" value="<?= $ThreadID ?>" />
                <input type="hidden" name="page" value="<?= $Page ?>" />
            </div>
            <table cellpadding="6" cellspacing="1" border="0" width="100%" class="layout border">
                <tr>
                    <td class="label"><label for="sticky_thread_checkbox"><?= Lang::get('forums', 'sticky') ?></label></td>
                    <td>
                        <input type="checkbox" id="sticky_thread_checkbox" name="sticky" <? if ($ThreadInfo['IsSticky']) {
                                                                                                echo ' checked="checked"';
                                                                                            } ?> tabindex="2" />
                    </td>
                </tr>
                <tr>
                    <td class="label"><label for="hiddenreplies_thread_checkbox"><?= Lang::get('forums', 'only_starter') ?></label></td>
                    <td>
                        <input type="checkbox" id="hiddenreplies_thread_checkbox" name="hiddenreplies" <? if ($ThreadInfo['hiddenreplies']) {
                                                                                                            echo ' checked="checked"';
                                                                                                        } ?> tabindex="2" />
                    </td>
                </tr>
                <tr id="ranking_row">
                    <td class="label"><label for="thread_ranking_textbox"><?= Lang::get('forums', 'ranking') ?></label></td>
                    <td>
                        <input type="text" id="thread_ranking_textbox" name="ranking" value="<?= $ThreadInfo['Ranking'] ?>" tabindex="2" />
                    </td>
                </tr>
                <tr>
                    <td class="label"><label for="locked_thread_checkbox"><?= Lang::get('forums', 'locked') ?></label></td>
                    <td>
                        <input type="checkbox" id="locked_thread_checkbox" name="locked" <? if ($ThreadInfo['IsLocked']) {
                                                                                                echo ' checked="checked"';
                                                                                            } ?> tabindex="2" />
                    </td>
                </tr>
                <tr>
                    <td class="label"><label for="auto_locked_thread_checkbox"><?= Lang::get('forums', 'auto_locked') ?></label></td>
                    <td>
                        <select name="autolocked" id="auto_locked_thread_checkbox" tabindex="2">
                            <option value="0" <? if ($ThreadInfo['AutoLocked'] == '0') {
                                                    echo ' selected="selected"';
                                                } ?>><?= Lang::get('forums', 'the_same_as_thread') ?></option>
                            <option value="1" <? if ($ThreadInfo['AutoLocked'] == '1') {
                                                    echo ' selected="selected"';
                                                } ?>><?= Lang::get('forums', 'auto_locked') ?></option>
                            <option value="2" <? if ($ThreadInfo['AutoLocked'] == '2') {
                                                    echo ' selected="selected"';
                                                } ?>><?= Lang::get('forums', 'dont_auto_locked') ?></option>
                        </select>
                    </td>
                </tr>
                <? if (check_perms('site_debug')) { ?>
                    <td class="label"><label for="locked_thread_checkbox"><?= Lang::get('forums', 'index_refresh') ?></label></td>
                    <td>
                        <input type="checkbox" id="locked_thread_checkbox" name="notice" <? if ($ThreadInfo['IsNotice']) {
                                                                                                echo ' checked="checked"';
                                                                                            } ?> tabindex="2" />
                    </td>
                    </tr>
                <? } ?>
                <tr>
                    <td class="label"><label for="thread_title_textbox"><?= Lang::get('forums', 'title') ?></label></td>
                    <td>
                        <input type="text" id="thread_title_textbox" name="title" style="width: 75%;" value="<?= display_str($ThreadInfo['Title']) ?>" tabindex="2" />
                    </td>
                </tr>
                <tr>
                    <td class="label"><label for="move_thread_selector"><?= Lang::get('forums', 'move_thread') ?></label></td>
                    <td>
                        <select name="forumid" id="move_thread_selector" tabindex="2">
                            <?
                            $OpenGroup = false;
                            $LastCategoryID = -1;

                            foreach ($Forums as $Forum) {
                                if ($Forum['MinClassRead'] > $LoggedUser['Class']) {
                                    continue;
                                }

                                if ($Forum['CategoryID'] != $LastCategoryID) {
                                    $LastCategoryID = $Forum['CategoryID'];
                                    if ($OpenGroup) { ?>
                                        </optgroup>
                                    <?            } ?>
                                    <optgroup label="<?= $ForumCats[$Forum['CategoryID']] ?>">
                                    <? $OpenGroup = true;
                                }
                                    ?>
                                    <option value="<?= $Forum['ID'] ?>" <? if ($ThreadInfo['ForumID'] == $Forum['ID']) {
                                                                            echo ' selected="selected"';
                                                                        } ?>><?= display_str($Forum['Name']) ?></option>
                                <?    } ?>
                                    </optgroup>
                        </select>
                    </td>
                </tr>
                <? if (check_perms('site_admin_forums')) { ?>
                    <tr>
                        <td class="label"><label for="delete_thread_checkbox"><?= Lang::get('forums', 'delete_thread') ?></label></td>
                        <td>
                            <input type="checkbox" id="delete_thread_checkbox" name="delete" tabindex="2" />
                        </td>
                    </tr>
                <?    } ?>
                <tr>
                    <td colspan="2" class="center">
                        <input type="submit" value="<?= Lang::get('forums', 'edit_thread') ?>" tabindex="2" />
                        <span style="float: right;">
                            <input type="submit" name="trash" value="Trash" tabindex="2" />
                        </span>
                    </td>
                </tr>

            </table>
        </form>
    <?
    } // If user is moderator
    ?>
</div>
<? View::show_footer();
