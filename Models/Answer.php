<?php

namespace App\Models;

class Answer
{
    public ?int $answer_id;
    public int $user_id;
    public int $question_id;
    public string $body;
    public string $created_at;
    public ?int $reputations;

    public function __construct(int $user_id, int $question_id, string $body, string $created_at, ?int $answer_id = null, ?int $reputations = null)
    {
        $this->answer_id = $answer_id;
        $this->user_id = $user_id;
        $this->question_id = $question_id;
        $this->body = $body;
        $this->created_at = $created_at;
        $this->reputations = $reputations == null ? 0 : $reputations;
    }

    // Getters
    public function getAnswerId(): int
    {
        return $this->answer_id;
    }

    public function getUserId(): int
    {
        return $this->user_id;
    }

    public function getQuestionId(): int
    {
        return $this->question_id;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getCreatedAt(): string
    {
        return $this->created_at;
    }

    public function getReputations(): int
    {
        return $this->reputations;
    }

    // Setters
    public function setBody(string $body): void
    {
        $this->body = $body;
    }

    public function setCreatedAt(string $created_at): void
    {
        $this->created_at = $created_at;
    }

    public function setReputations(int $reputations): void
    {
        $this->reputations = $reputations;
    }
}
