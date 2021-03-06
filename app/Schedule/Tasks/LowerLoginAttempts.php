<?php

namespace Gazelle\Schedule\Tasks;

class LowerLoginAttempts extends \Gazelle\Schedule\Task {
    public function run() {
        $this->db->prepared_query('
            UPDATE login_attempts
            SET Attempts = Attempts - 1
            WHERE Attempts > 0
        ');
        $this->processed = $this->db->affected_rows();

        $this->db->prepared_query('
            DELETE FROM login_attempts
            WHERE LastAttempt < (now() - INTERVAL 90 DAY)
        ');
        $this->processed += $this->db->affected_rows();
    }
}
