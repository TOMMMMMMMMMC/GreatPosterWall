<?
$LastRead = Forums::get_last_read($Forums);
View::show_header(Lang::get('forums', 'forums'));
?>
<div class="thin">
    <h2><?= Lang::get('forums', 'forums') ?></h2>
    <div class="forum_list">
        <?

        $Row = 'a';
        $LastCategoryID = 0;
        $OpenTable = false;
        foreach ($Forums as $Forum) {
            list($ForumID, $CategoryID, $ForumName, $ForumDescription, $MinRead, $MinWrite, $MinCreate, $NumTopics, $NumPosts, $LastPostID, $LastAuthorID, $LastTopicID, $LastTime, $SpecificRules, $LastTopic, $Locked, $Notice, $Sticky) = array_values($Forum);
            if (!Forums::check_forumperm($ForumID)) {
                continue;
            }
            if ($ForumID == DONOR_FORUM) {
                $ForumDescription = Donations::get_forum_description();
            }
            $Tooltip = $ForumID == DONOR_FORUM ? 'tooltip_gold' : 'tooltip';
            $Row = $Row === 'a' ? 'b' : 'a';
            $ForumDescription = display_str($ForumDescription);

            if ($CategoryID != $LastCategoryID) {
                $Row = 'b';
                $LastCategoryID = $CategoryID;
                if ($OpenTable) { ?>
                    </table>
                <?      } ?>
                <h3><?= $ForumCats[$CategoryID] ?></h3>
                <table class="forum_index m_table">
                    <tr class="colhead">
                        <td style="width: 2%;"></td>
                        <td class="m_th_left" style="width: 20%;"><?= Lang::get('forums', 'forum') ?></td>
                        <td><?= Lang::get('forums', 'last_post') ?></td>
                        <td class="m_th_right" style="width: 7%;"><?= Lang::get('forums', 'topics') ?></td>
                        <td class="m_th_right" style="width: 7%;"><?= Lang::get('forums', 'posts') ?></td>
                    </tr>
                <?
                $OpenTable = true;
            }

            $Read = Forums::is_unread($Locked, $Notice, $Sticky, $LastPostID, $LastRead, $LastTopicID, $LastTime) ? 'unread' : 'read';
            /* Removed per request, as distracting
    if ($Locked) {
        $Read .= '_locked';
    }
    if ($Notice) {
        $Read .= '_notice';
    }
    if ($Sticky) {
        $Read .= '_sticky';
    }
*/
                ?>
                <tr class="row<?= $Row ?>">
                    <td class="td_read <?= $Read ?> <?= $Tooltip ?>" title="<?= ucfirst($Read) ?>"></td>
                    <td class="td_forum">
                        <h4 class="min_padding">
                            <a class="<?= $Tooltip ?>" href="forums.php?action=viewforum&amp;forumid=<?= $ForumID ?>" title="<?= display_str($ForumDescription) ?>"><?= display_str($ForumName) ?></a>
                        </h4>
                    </td>
                    <? if ($NumPosts == 0) { ?>
                        <td class="td_latest">
                            <?= Lang::get('forums', 'there_are_no_topics') ?><?= (($MinCreate <= $LoggedUser['Class']) ? '<a href="forums.php?action=new&amp;forumid=' . $ForumID . '">' . Lang::get('forums', 'create_one') . '</a>' : '') ?>
                        </td>
                        <td class="td_topic_count number_column m_td_right">0</td>
                        <td class="td_post_count number_column m_td_right">0</td>
                    <? } else { ?>
                        <td class="td_latest">
                            <span style="float: left;" class="last_topic">
                                <a href="forums.php?action=viewthread&amp;threadid=<?= $LastTopicID ?>" class="tooltip" data-title-plain="<?= display_str($LastTopic) ?>" <?= ((strlen($LastTopic) > 50) ? "title='" . display_str($LastTopic) . "'" : "") ?>><?= display_str(Format::cut_string($LastTopic, 50, 1)) ?></a>
                            </span>
                            <? if (!empty($LastRead[$LastTopicID])) { ?>
                                <span style="float: left;" class="<?= $Tooltip ?> last_read" title="<?= Lang::get('forums', 'jump_to_last_read') ?>">
                                    <a href="forums.php?action=viewthread&amp;threadid=<?= $LastTopicID ?>&amp;page=<?= $LastRead[$LastTopicID]['Page'] ?>#post<?= $LastRead[$LastTopicID]['PostID'] ?>"></a>
                                </span>
                            <? } ?>
                            <span style="float: right;" class="last_poster"><?= Lang::get('forums', 'by') ?> <?= Users::format_username($LastAuthorID, false, false, false) ?> <?= time_diff($LastTime, 1) ?></span>
                        </td>
                        <td class="td_topic_count number_column m_td_right"><?= number_format($NumTopics) ?></td>
                        <td class="td_post_count number_column m_td_right"><?= number_format($NumPosts) ?></td>
                    <? } ?>
                </tr>
            <? } ?>
                </table>
    </div>
    <div class="linkbox"><a href="forums.php?action=catchup&amp;forumid=all&amp;auth=<?= $LoggedUser['AuthKey'] ?>" class="brackets"><?= Lang::get('forums', 'catch_up') ?></a></div>
</div>

<? View::show_footer(); ?>