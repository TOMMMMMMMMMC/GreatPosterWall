<?php

namespace Gazelle\Donate;

class PrepaidCard extends \Gazelle\Base {

    public function donate(int $userId, string $num, string $secret, $faceNum) {
         $this->db->query("
			SELECT 1
			FROM users_main
			WHERE ID = '$UserID'
			LIMIT 1");
        if(!$this->db->has_results()) {
            return false;
        }
        $AddedBy = $userId;
        $Source = "Prepaid Card";
        $this->db->begin_transaction();
        $this->db->prepared_query(
            "INSERT INTO donations_prepaid_card (user_id, create_time, card_num, card_secret, face_value VALUES(?, ?, ?, ?)",
            $userId,
            sqltime(),
            $num,
            $secret,
            $faceNum
        );
        if ($this->db->affected_rows() != 1) {
            $this->db->rollback();
            return false;
        }
        $this->db->prepared_query(
            "UPDATE users_info
				SET Donor = '1'
				WHERE UserID = ?", $userId
        );
        $this->db->prepared_query(
            "INSERT INTO "
        )
    }
}
