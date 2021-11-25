<?
View::show_header(Lang::get('badges', 'badges_center'));
$BadgesByUserID = Badges::get_badges_by_userid($LoggedUser['ID']);
$Page  = max(1, isset($_GET['page']) ? intval($_GET['page']) : 1);
$Pages = Format::get_pages($Page, count($BadgesByUserID), 20);
?>
<div class="thin">
    <div class="header">
        <h2><?= Users::format_username($UserID, false, false, false) ?> &gt; <?= Lang::get('badges', 'index_badge') ?> &gt; <?= Lang::get('badges', 'badge_log') ?></h2>
    </div>
    <div class="linkbox">
        <a href="/badges.php?action=display" class="brackets"><?= Lang::get('badges', 'badge_display') ?></a>
        <a href="/badges.php" class="brackets"><?= Lang::get('badges', 'badge_achievement_progress') ?></a>
        <a href="/badges.php?action=history" class="brackets"><?= Lang::get('badges', 'badge_log') ?></a>
        <a href="/badges.php?action=store" class="brackets"><?= Lang::get('badges', 'badge_store') ?></a>
        <!-- <a href="" class="brackets">游九中心</a> -->
    </div>
    <div class="linkbox" class="page_turn"><?= $Pages ?></div>
    <div class="badge_log_container">
        <table id="badge_log_table">
            <tr class="colhead">
                <td><?= Lang::get('badges', 'badge_obtained_time') ?></td>
                <td><?= Lang::get('badges', 'badge_log_detail') ?></td>
            </tr>

            <?
            $RowA = true;
            $i = 0;
            foreach ($BadgesByUserID as $BadgeID => $BadgeInfo) {
                if ($i < ($Page - 1) * 20) {
                    $i++;
                    continue;
                } else if ($i >= $Page * 20) {
                    break;
                }
                $i++;
                $Badge = Badges::get_badges_by_id($BadgeID);
            ?>
                <tr class="row<?= $RowA ? "a" : "b" ?>">
                    <td><?= time_diff($BadgeInfo['Time']) ?></td>
                    <td><?= $Badge['Level'] <= 1 ? ("Activated " . Badges::get_text($Badge['Label'], 'badge_name')) : ("Promoted to level " . $Badge['Level'] . " " . Badges::get_text($Badge['Label'], 'badge_name')) ?></td>
                </tr>
            <?
                $RowA = !$RowA;
            }
            ?>
        </table>
    </div>
    <div class="linkbox" class="page_turn"><?= $Pages ?></div>
</div>


<?
View::show_footer();
