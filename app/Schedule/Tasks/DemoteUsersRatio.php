<?php

namespace Gazelle\Schedule\Tasks;

class DemoteUsersRatio extends \Gazelle\Schedule\Task {
    public function run() {
        foreach (\Gazelle\User::demotionCriteria() as $criteria) {
            $this->demote($criteria['To'], $criteria['Ratio'], $criteria['Upload'], $criteria['From']);
        }
    }

    private function demote(int $newClass, float $ratio, int $upload, array $demoteClasses) {
        $classString = \Users::make_class_string($newClass);
        $placeholders = placeholders($demoteClasses);
        $query = $this->db->prepared_query(
            "
            SELECT ID
            FROM users_main um
            INNER JOIN users_leech_stats AS uls ON (uls.UserID = um.ID)
            LEFT JOIN
            (
                SELECT rv.UserID, sum(Bounty) AS Bounty
                FROM requests_votes rv
                INNER JOIN requests r ON (r.ID = rv.RequestID)
                WHERE r.UserID != r.FillerID
                GROUP BY rv.UserID
            ) b ON (b.UserID = um.ID)
            WHERE um.PermissionID IN ($placeholders)
                AND (
                    (uls.Downloaded > 0 AND uls.Uploaded / uls.Downloaded < ?)
                    OR (uls.Uploaded + ifnull(b.Bounty, 0)) < ?
                )
            ",
            ...array_merge($demoteClasses, [$ratio, $upload])
        );

        $this->db->prepared_query(
            "
            UPDATE users_info AS ui
            INNER JOIN users_main AS um ON (um.ID = ui.UserID)
            INNER JOIN users_leech_stats AS uls ON (uls.UserID = um.ID)
            LEFT JOIN
            (
                SELECT rv.UserID, sum(Bounty) AS Bounty
                FROM requests_votes rv
                INNER JOIN requests r ON (r.ID = rv.RequestID)
                WHERE r.UserID != r.FillerID
                GROUP BY rv.UserID
            ) b ON (b.UserID = um.ID)
            SET
                um.PermissionID = ?,
                ui.AdminComment = concat(now(), ' - Class changed to ', ?, ' by System\n\n', ui.AdminComment)
            WHERE um.PermissionID IN ($placeholders)
                AND (
                    (uls.Downloaded > 0 AND uls.Uploaded / uls.Downloaded < ?)
                    OR (uls.Uploaded + ifnull(b.Bounty, 0)) < ?
                )
            ",
            ...array_merge([$newClass, $classString], $demoteClasses, [$ratio, $upload])
        );

        $this->db->set_query_id($query);
        $demotions = 0;
        while (list($userID) = $this->db->next_record()) {
            $demotions++;
            $this->debug("Demoting $userID to $classString for insufficient ratio", $userID);

            $this->cache->delete_value("user_info_$userID");
            $this->cache->delete_value("user_info_heavy_$userID");
            \Misc::send_pm($userID, 0, "You have been demoted to $classString", "You now only meet the requirements for the \"$classString\" user class.\n\nTo read more about " . SITE_NAME . "'s user classes, read [url=" . SITE_URL . "/wiki.php?action=article&amp;name=userclasses]this wiki article[/url].");
        }

        if ($demotions > 0) {
            $this->processed += $demotions;
            $this->info("Demoted $demotions users to $classString for insufficient ratio", $newClass);
        }
    }
}
