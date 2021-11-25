<?php

namespace Gazelle\Schedule\Tasks;

class NotifyNonseedingUploaders extends \Gazelle\Schedule\Task {
    public function run() {
        // Send warnings to uploaders of torrents that will be deleted this week
        $this->db->prepared_query(
            "
            SELECT
                t.ID,
                t.GroupID,
                tg.Name,
                t.Format,
                t.Encoding,
                t.UserID
            FROM torrents AS t
                INNER JOIN torrents_leech_stats AS tls ON (tls.TorrentID = t.ID)
                INNER JOIN torrents_group AS tg ON (tg.ID = t.GroupID)
                INNER JOIN users_info AS u ON (u.UserID = t.UserID)
            WHERE tls.last_action < NOW() - INTERVAL 20 DAY
                AND tls.last_action != 0
                AND u.UnseededAlerts = '1'
            ORDER BY tls.last_action ASC"
        );

        $torrentIDs = $this->db->to_array();
        $torrentAlerts = [];
        $inactivityExceptionsMade = [];

        foreach ($torrentIDs as $torrentID) {
            list($id, $groupID, $name, $format, $encoding, $userID) = $torrentID;

            if (array_key_exists($userID, $inactivityExceptionsMade) && (time() < $inactivityExceptionsMade[$userID])) {
                // don't notify exceptions
                continue;
            }

            if (!array_key_exists($userID, $torrentAlerts)) {
                $torrentAlerts[$userID] = ['Count' => 0, 'Msg' => ''];
            }

            $artistName = \Artists::display_artists(\Artists::get_artist($groupID), false, false, false);
            if ($artistName) {
                $name = "$artistName - $name";
            }

            if ($format && $encoding) {
                $name .= " [$format / $encoding]";
            }

            $torrentAlerts[$userID]['Msg'] .= "\n[url=" . SITE_URL . "/torrents.php?torrentid=$id]" . $name . "[/url]";
            $torrentAlerts[$userID]['Count']++;

            $this->processed++;
        }

        foreach ($torrentAlerts as $userID => $messageInfo) {
            \Misc::send_pm($UserID, 0, "未做种通知 | Unseeded torrent notification", "你发布的种子中有 " . $MessageInfo['Count'] . " 个很快就会因为不活跃而进入可替代状态。种子四周不做种就可以被替代。如果你仍然拥有种子内容文件，你可以在客户端确认种子处于做种状态来保证你所发布种子的安全。请通过点开种子详情，查看 “最新活动” 的时间来确认种子未做种的时长。更多信息，请见 [url=" . site_url() . "wiki.php?action=article&name=不活跃种子]本文[/url]。\n\n以下种子将因不活跃而进入可替代状态：" . $MessageInfo['Msg'] . "\n\n如果你不愿再接收此类提醒，请前往个人设置关闭。\n\n[hr]\n" . $MessageInfo['Count'] . " of your uploads will be trumpable for inactivity soon. Unseeded torrents are trumpable after 4 weeks. If you still have the files, you can seed your uploads by ensuring the torrents are in your client and that they aren't stopped. You can view the time that a torrent has been unseeded by clicking on the torrent description line and looking for the \"Last active\" time. For more information, please go [url=" . site_url() . "wiki.php?action=article&name=不活跃种子]here[/url].\n\nThe following torrent" . ($MessageInfo['Count'] > 1 ? 's' : '') . ' will be trumpable for inactivity:' . $MessageInfo['Msg'] . "\n\nIf you no longer wish to receive these notifications, please disable them in your profile settings.");

            $this->debug("Warning user $userID about ${messageInfo['Count']} torrents", $userID);
        }
    }
}
