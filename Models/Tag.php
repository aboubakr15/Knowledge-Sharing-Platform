<?php

namespace App\Models;

class Tag
{
    public int $tag_id;

    public string $name;

    public int $questionCount = 0;

    public function __construct(int $tag_id, string $name)
    {
        $this->tag_id = $tag_id;
        $this->name = $name;
    }

    // Getters
    public function getTagId(): int
    {
        return $this->tag_id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    // Setters
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getQuestionCount()
    {
        return $this->questionCount;
    }

    public function setQuestionCount(int $cnt)
    {
        $this->questionCount = $cnt;
    }
}