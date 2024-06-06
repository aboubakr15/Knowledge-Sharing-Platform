<?php

namespace App\Models;

class UserBadge {
    public int $user_id;
    public int $badge_id;

    public function __construct(int $user_id, int $badge_id) {
        $this->user_id = $user_id;
        $this->badge_id = $badge_id;
    }
}