<?php

namespace Gazelle\Schedule\Tasks;

class RatioWatch extends \Gazelle\Schedule\Task {
    public function run() {
        // Take users off ratio watch and enable leeching
        $userQuery = $this->db->prepared_query("
            SELECT
                um.ID,
                um.torrent_pass
            FROM users_info AS i
            INNER JOIN users_main AS um ON (um.ID = i.UserID)
            INNER JOIN users_leech_stats AS uls ON (uls.UserID = um.ID)
            WHERE uls.Downloaded > 0
                AND uls.Uploaded / uls.Downloaded >= um.RequiredRatio
                AND i.RatioWatchEnds IS NOT NULL
                AND um.Enabled = '1'
        ");

        $offRatioWatch = $this->db->collect('ID');
        if (count($offRatioWatch) > 0) {
            $this->db->prepared_query("
                UPDATE users_info AS ui
                INNER JOIN users_main AS um ON (um.ID = ui.UserID) SET
                    ui.RatioWatchEnds     = NULL,
                    ui.RatioWatchDownload = '0',
                    um.can_leech          = '1',
                    ui.AdminComment       = concat(now(), ' - Taken off ratio watch by adequate ratio.\n\n', ui.AdminComment)
                WHERE ui.UserID IN (" . placeholders($offRatioWatch) . ")
            ", ...$offRatioWatch);

            foreach ($offRatioWatch as $userID) {
                $this->cache->delete_value("user_info_heavy_$userID");
                \Misc::send_pm($userID, 0, '你已脱离分享率监控名单 | You have been taken off Ratio Watch', "恭喜！你又可以愉快地白嫖了。\n为确保你不会再次 “入狱”，我们恳请你认真阅读并掌握 [url=" . SITE_URL . "/rules.php?p=ratio]分享率规则[/url]。\n\n[hr]\nCongratulations! Feel free to begin downloading again.\nTo ensure that you do not get put on ratio watch again, please read the rules located [url=" . SITE_URL . "/rules.php?p=ratio]here[/url].\n");

                $this->processed++;
                $this->debug("Taking $userID off ratio watch", $userID);
            }

            $this->db->set_query_id($userQuery);
            $passkeys = $this->db->collect('torrent_pass');
            foreach ($passkeys as $passkey) {
                \Tracker::update_tracker('update_user', ['passkey' => $passkey, 'can_leech' => '1']);
            }
        }

        // Put users on ratio watch if they don't meet the standards
        $this->db->prepared_query("
            SELECT um.ID
            FROM users_info AS i
            INNER JOIN users_main AS um ON (um.ID = i.UserID)
            INNER JOIN users_leech_stats AS uls ON (uls.UserID = um.ID)
            WHERE uls.Downloaded > 0
                AND uls.Uploaded / uls.Downloaded < um.RequiredRatio
                AND i.RatioWatchEnds IS NULL
                AND um.Enabled = '1'
                AND um.can_leech = '1'
        ");

        $onRatioWatch = $this->db->collect('ID');
        if (count($onRatioWatch) > 0) {
            $this->db->prepared_query("
                UPDATE users_info AS i
                INNER JOIN users_main AS um ON (um.ID = i.UserID)
                INNER JOIN users_leech_stats AS uls ON (uls.UserID = um.ID) SET
                    i.RatioWatchEnds     = now() + INTERVAL 2 WEEK,
                    i.RatioWatchTimes    = i.RatioWatchTimes + 1,
                    i.RatioWatchDownload = uls.Downloaded
                WHERE um.ID IN (" . placeholders($onRatioWatch) . ")
            ", ...$onRatioWatch);

            foreach ($onRatioWatch as $userID) {
                $this->cache->delete_value("user_info_heavy_$userID");
                \Misc::send_pm($userID, 0, '你已进入分享率监控名单 | You have been put on Ratio Watch', "目前，你的分享率低于 [url=" . SITE_URL . "/rules.php?p=ratio]分享率规则[/url] 所定义的合格分享率。\n更多关于分享率监控的信息请点击上方链接查看。\n\n[br]\nThis happens when your ratio falls below the requirements outlined in the rules located [url=" . SITE_URL . "/rules.php?p=ratio]here[/url].\nFor information about ratio watch, click the link above.");

                $this->processed++;
                $this->debug("Putting $userID on ratio watch", $userID);
            }
        }
    }
}
