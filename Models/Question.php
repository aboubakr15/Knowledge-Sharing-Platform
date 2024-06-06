<?php

namespace App\Models;

class Question
{
    public int $question_id;
    public int $user_id;
    public string $title;
    public string $body;
    public string $created_at;
    public string $updated_at;
    public int $reputations;

    public function __construct(int $question_id, int $user_id, string $title, string $body, string $created_at, string $updated_at, int $reputations)
    {
        $this->question_id = $question_id;
        $this->user_id = $user_id;
        $this->title = $title;
        $this->body = $body;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
        $this->reputations = $reputations;
    }

    // Getters
    public function getQuestionId(): int
    {
        return $this->question_id;
    }

    public function getUserId(): int
    {
        return $this->user_id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getCreatedAt(): string
    {
        return $this->created_at;
    }

    public function getUpdatedAt(): string
    {
        return $this->updated_at;
    }

    public function getReputations(): int
    {
        return $this->reputations;
    }

    // Setters
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function setBody(string $body): void
    {
        $this->body = $body;
    }

    public function setCreatedAt(string $created_at): void
    {
        $this->created_at = $created_at;
    }

    public function setUpdatedAt(string $updated_at): void
    {
        $this->updated_at = $updated_at;
    }

    public function setReputations(int $reputations): void
    {
        $this->reputations = $reputations;
    }
}
