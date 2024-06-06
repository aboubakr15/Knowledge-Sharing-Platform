<?php

namespace App\Models;

class QuestionTag {
    public int $question_id;
    public int $tag_id;

    public function __construct(int $question_id, int $tag_id) {
        $this->question_id = $question_id;
        $this->tag_id = $tag_id;
    }
}