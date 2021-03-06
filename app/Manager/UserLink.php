<?php

namespace Gazelle\Manager;

class UserLink extends \Gazelle\Base {

    protected $source;

    public function __construct(\Gazelle\User $source) {
        parent::__construct();
        $this->source = $source;
    }

    public function groupId(\Gazelle\User $user): ?int {
        return $this->db->scalar(
            "
            SELECT GroupID
            FROM users_dupes
            WHERE UserID = ?
            ",
            $user->id()
        );
    }

    public function link(\Gazelle\User $target, string $adminUsername, bool $updateNote): bool {
        $sourceId = $this->source->id();
        [$sourceGroupId, $comments] = $this->db->row(
            "
            SELECT u.GroupID, d.Comments
            FROM users_dupes AS u
            INNER JOIN dupe_groups AS d ON (d.ID = u.GroupID)
            WHERE u.UserID = ?
            ",
            $sourceId
        );
        $targetGroupId = $this->groupId($target);

        if ($targetGroupId) {
            if ($targetGroupId == $sourceGroupId) {
                return false;
            }
            if ($sourceGroupId) {
                $this->db->prepared_query(
                    "
                    UPDATE users_dupes SET
                        GroupID = ?
                    WHERE GroupID = ?
                    ",
                    $targetGroupId,
                    $sourceGroupId
                );
                $this->db->prepared_query(
                    "
                    UPDATE dupe_groups SET
                        Comments = concat(?, Comments)
                    WHERE ID = ?
                    ",
                    "$comments\n\n",
                    $targetGroupId
                );
                $this->db->prepared_query(
                    "
                    DELETE FROM dupe_groups WHERE ID = ?
                    ",
                    $sourceGroupId
                );
                $linkGroupId = $sourceGroupId;
            } else {
                $this->db->prepared_query(
                    "
                    INSERT INTO users_dupes
                           (UserID, GroupID)
                    VALUES (?,      ?)
                    ",
                    $sourceId,
                    $targetGroupId
                );
                $linkGroupId = $targetGroupId;
            }
        } elseif ($sourceGroupId) {
            $this->db->prepared_query(
                "
                INSERT INTO users_dupes
                       (UserID, GroupID)
                VALUES (?,      ?)
                ",
                $target->id(),
                $sourceGroupId
            );
            $linkGroupId = $sourceGroupId;
        } else {
            $this->db->prepared_query("INSERT INTO dupe_groups () VALUES ()");
            $linkGroupId = $this->db->inserted_id();
            $this->db->prepared_query(
                "
                INSERT INTO users_dupes
                       (UserID, GroupID)
                VALUES (?,      ?),
                       (?,      ?)
                ",
                $target->id(),
                $linkGroupId,
                $sourceId,
                $linkGroupId
            );
        }

        if ($updateNote) {
            $this->db->prepared_query(
                "
                UPDATE users_info AS i
                INNER JOIN users_dupes AS d USING (UserID) SET
                    i.AdminComment = concat(now(), ?, i.AdminComment)
                WHERE d.GroupID = ?
                ",
                " - Linked accounts updated: [user]" . $this->source->username() . "[/user] and [user]"
                    . $target->username() . "[/user] linked by {$adminUsername}\n\n",
                $linkGroupId
            );
        }
        return true;
    }

    function addGroupComments(string $comments, string $adminName, bool $updateNote) {
        $groupId = $this->groupId($this->source);
        $oldHash = $this->db->scalar(
            "
            SELECT sha1(Comments) AS CommentHash
            FROM dupe_groups
            WHERE ID = ?
            ",
            $groupId
        );
        $newHash = sha1($comments);
        if ($oldHash === $newHash) {
            return;
        }
        $this->db->prepared_query(
            "
            UPDATE dupe_groups SET
                Comments = ?
            WHERE ID = ?
            ",
            $comments,
            $groupId
        );
        if ($updateNote) {
            $this->db->prepared_query(
                "
                UPDATE users_info AS i SET
                    i.AdminComment = concat(now(), ?, i.AdminComment)
                WHERE i.UserID = ?
                ",
                "- Linked accounts updated: Comments updated by {$adminName}\n\n",
                $this->source->id()
            );
        }
    }

    function info() {
        $sourceId = $this->source->id();
        [$linkedGroupId, $comments] = $this->db->row(
            "
            SELECT d.ID, d.Comments
            FROM dupe_groups AS d
            INNER JOIN users_dupes AS u ON (u.GroupID = d.ID)
            WHERE u.UserID = ?
            ",
            $sourceId
        );
        $this->db->prepared_query(
            "
            SELECT um.ID as user_id,
                um.Username AS username
            FROM users_dupes AS d
            INNER JOIN users_main AS um ON (um.ID = d.UserID)
            WHERE d.GroupID = ?
                AND d.UserID != ?
            ORDER BY um.ID
            ",
            $linkedGroupId,
            $sourceId
        );
        return [$linkedGroupId, $comments, $this->db->to_array(false, MYSQLI_ASSOC, false)];
    }

    function remove(\Gazelle\User $target, string $adminName) {
        $targetId = $target->id();
        $this->db->prepared_query(
            "
            UPDATE users_info AS i
            INNER JOIN users_dupes AS d1 ON (d1.UserID = i.UserID)
            INNER JOIN users_dupes AS d2 ON (d2.GroupID = d1.GroupID) SET
                i.AdminComment = concat(now(), ?, i.AdminComment)
            WHERE d2.UserID = ?
            ",
            " - Linked accounts updated: [user]" . $target->username() . "[/user] unlinked by $adminName\n\n",
            $targetId
        );
        $this->db->prepared_query(
            "
            DELETE FROM users_dupes WHERE UserID = ?
            ",
            $targetId
        );
        $this->db->prepared_query("
            DELETE g.*
            FROM dupe_groups AS g
            LEFT JOIN users_dupes AS u ON (u.GroupID = g.ID)
            WHERE u.GroupID IS NULL
        ");
    }

    function removeGroup(int $linkGroupId) {
        $this->db->prepared_query(
            "
            DELETE FROM dupe_groups WHERE ID = ?
            ",
            $linkGroupId
        );
    }
}
