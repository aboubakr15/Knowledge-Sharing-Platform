<?php

namespace App\Models;

class Badge {
    public int $badge_id;
    public string $name;
    public string $type;

    public function __construct(int $badge_id, string $name, string $type) {
        $this->badge_id = $badge_id;
        $this->name = $name;
        $this->type = $type;
    }
}