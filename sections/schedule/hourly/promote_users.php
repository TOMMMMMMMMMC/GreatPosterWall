<?php

//------------- Promote users -------------------------------------------//
sleep(5);

foreach ($UserCriteria as $L) { // $L = Level
    $Query = "
				SELECT ID
				FROM users_main AS um
					JOIN users_info ON um.ID = users_info.UserID
                LEFT JOIN(
                    SELECT UserID,
                       SUM(Downloaded) AS Downloaded
                    FROM
                        users_freetorrents
                    GROUP BY
                        UserID
                ) AS ft 
                ON
                    um.ID = ft.UserID
                LEFT JOIN(
                    SELECT UserID,
                       SUM(Downloaded) AS Downloaded
                    FROM
                        users_freeleeches
                    GROUP BY
                        UserID
                ) AS fl
                ON
                    um.ID = fl.UserID
				WHERE PermissionID = " . $L['From'] . "
					AND Warned = '0000-00-00 00:00:00'
					AND Uploaded >= '$L[MinUpload]'
					AND um.Downloaded + IFNULL(ft.Downloaded,0) + IFNULL(fl.Downloaded, 0) >= '$L[MinDownload]'
					AND (um.Uploaded / um.Downloaded >= '$L[MinRatio]' OR (um.Uploaded / um.Downloaded IS NULL))
					AND JoinDate < now() - INTERVAL '$L[Weeks]' WEEK
					AND (
						SELECT COUNT(ID)
						FROM torrents
						WHERE UserID = um.ID
						) >= '$L[MinUploads]'
					AND Enabled = '1'";
    if (!empty($L['Extra'])) {
        $Query .= ' AND ' . $L['Extra'];
    }

    $DB->query($Query);

    $UserIDs = $DB->collect('ID');

    if (count($UserIDs) > 0) {
        $DB->query("
				UPDATE users_main
				SET PermissionID = " . $L['To'] . "
				WHERE ID IN(" . implode(',', $UserIDs) . ')');
        foreach ($UserIDs as $UserID) {
            /*$Cache->begin_transaction("user_info_$UserID");
            $Cache->update_row(false, array('PermissionID' => $L['To']));
            $Cache->commit_transaction(0);*/
            $Cache->delete_value("user_info_$UserID");
            $Cache->delete_value("user_info_heavy_$UserID");
            $Cache->delete_value("user_stats_$UserID");
            $Cache->delete_value("enabled_$UserID");
            $DB->query("
					UPDATE users_info
					SET AdminComment = CONCAT('" . sqltime() . " - Class changed to " . Users::make_class_string($L['To']) . " by System\n\n', AdminComment)
					WHERE UserID = $UserID");
            Misc::send_pm($UserID, 0, "你已升级 | You have been promoted to " . Users::make_class_string($L['To']), "恭喜你晋升到 “" . Users::make_class_string($L['To']) . "”！\n\n欲了解更多关于 " . SITE_NAME . " 用户等级的知识，请阅读 [url=" . site_url() . "wiki.php?action=article&amp;name=userclasses]本文[/url]。
			
			----------------------------------------------------------------------
			
			Congratulations on your promotion to " . Users::make_class_string($L['To']) . "!\n\nTo read more about " . SITE_NAME . "'s user classes, read [url=" . site_url() . "wiki.php?action=article&amp;name=userclasses]this wiki article[/url].");
            if ($L['Invite']) {
                $DB->query("select AwardLevel from users_main where ID=$UserID and AwardLevel < " . $L['AwardLevel']);
                $AwardLevel = $DB->collect('AwardLevel');
                if (count($AwardLevel) > 0) {
                    $DB->query("UPDATE users_main SET AwardLevel = " . $L['AwardLevel'] . ", Invites = Invites + " . $L['Invite'] . " WHERE ID = $UserID");
                }
            }
        }
    }

    // Demote users with less than the required uploads

    $Query = "
			SELECT ID
			FROM users_main
				JOIN users_info ON users_main.ID = users_info.UserID
			WHERE PermissionID = '$L[To]'
				AND ( Uploaded < '$L[MinUpload]'
					OR (
						SELECT COUNT(ID)
						FROM torrents
						WHERE UserID = users_main.ID
						) < '$L[MinUploads]'";
    if (!empty($L['Extra'])) {
        $Query .= ' OR NOT ' . $L['Extra'];
    }
    $Query .= "
					)
				AND Enabled = '1'";

    $DB->query($Query);
    $UserIDs = $DB->collect('ID');

    if (count($UserIDs) > 0) {
        $DB->query("
				UPDATE users_main
				SET PermissionID = " . $L['From'] . "
				WHERE ID IN(" . implode(',', $UserIDs) . ')');
        foreach ($UserIDs as $UserID) {
            /*$Cache->begin_transaction("user_info_$UserID");
            $Cache->update_row(false, array('PermissionID' => $L['From']));
            $Cache->commit_transaction(0);*/
            $Cache->delete_value("user_info_$UserID");
            $Cache->delete_value("user_info_heavy_$UserID");
            $Cache->delete_value("user_stats_$UserID");
            $Cache->delete_value("enabled_$UserID");
            $DB->query("
					UPDATE users_info
					SET AdminComment = CONCAT('" . sqltime() . " - Class changed to " . Users::make_class_string($L['From']) . " by System\n\n', AdminComment)
					WHERE UserID = $UserID");
            Misc::send_pm($UserID, 0, "你已降级 | You have been demoted to " . Users::make_class_string($L['From']), "你现在只满足 “" . Users::make_class_string(MEMBER) . "” 用户等级的要求。\n\n欲了解更多关于 " . SITE_NAME . " 用户等级的知识，请阅读 [url=" . site_url() . "wiki.php?action=article&amp;name=userclasses]本文[/url]。
	
			----------------------------------------------------------------------
			
			You now only meet the requirements for the \"" . Users::make_class_string(MEMBER) . "\" user class.\n\nTo read more about " . SITE_NAME . "'s user classes, read [url=" . site_url() . "wiki.php?action=article&amp;name=userclasses]this wiki article[/url].");
        }
    }
}
