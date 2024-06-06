<?php

namespace App\Models;

class Vote
{
    private ?int $vote_id;
    private int $user_id;
    private int $question_id;
    private string $vote_type;
    private int $answer_id;

    public function __construct(int $user_id, int $question_id, int $answer_id, string $vote_type, ?int $vote_id = null)
    {
        $this->vote_id = $vote_id;
        $this->user_id = $user_id;
        $this->question_id = $question_id;
        $this->vote_type = $vote_type;
        $this->answer_id = $answer_id;
    }

    // Getters
    public function getVoteId(): int
    {
        return $this->vote_id;
    }

    public function getUserId(): int
    {
        return $this->user_id;
    }

    public function getQuestionId(): int
    {
        return $this->question_id;
    }

    public function getVoteType(): string
    {
        return $this->vote_type;
    }

    public function getAnswerId(): int
    {
        return $this->answer_id;
    }

    // Setters
    public function setVoteId(int $vote_id): void
    {
        $this->vote_id = $vote_id;
    }

    public function setUserId(int $user_id): void
    {
        $this->user_id = $user_id;
    }

    public function setQuestionId(int $question_id): void
    {
        $this->question_id = $question_id;
    }

    public function setVoteType(string $vote_type): void
    {
        $this->vote_type = $vote_type;
    }

    public function setAnswerId(int $id): void
    {
        $this->answer_id = $id;
    }
}
