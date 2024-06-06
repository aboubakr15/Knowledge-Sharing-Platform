<?php

namespace App\Models;

use App\Services\UserService;

class Notification
{
    private int $notification_id;
    private int $user_id;
    private int $targeted_user_id;
    private string $notification_type;
    private int $source_id;
    private bool $is_read;
    private string $created_at;
    public function __construct(int $notification_id, int $user_id, int $targeted_user_id, string $notification_type, int $source_id, bool $is_read = false, string $created_at = '')
    {
        $this->notification_id = $notification_id;
        $this->user_id = $user_id;
        $this->targeted_user_id = $targeted_user_id;
        $this->notification_type = $notification_type;
        $this->source_id = $source_id;
        $this->is_read = $is_read;
        $this->created_at = $created_at ?: date('Y-m-d H:i:s');
    }

    // Getters
    public function getNotificationId(): int
    {
        return $this->notification_id;
    }

    public function getUserId(): int
    {
        return $this->user_id;
    }

    public function getNotificationType(): string
    {
        return $this->notification_type;
    }

    public function getSourceId(): int
    {
        return $this->source_id;
    }

    public function getIsRead(): bool
    {
        return $this->is_read;
    }

    public function getCreatedAt(): string
    {
        return $this->created_at;
    }

    // Setters
    public function setNotificationId(int $notification_id): void
    {
        $this->notification_id = $notification_id;
    }

    public function setUserId(int $user_id): void
    {
        $this->user_id = $user_id;
    }

    public function setNotificationType(string $notification_type): void
    {
        $this->notification_type = $notification_type;
    }

    public function setSourceId(int $source_id): void
    {
        $this->source_id = $source_id;
    }

    public function setIsRead(bool $is_read): void
    {
        $this->is_read = $is_read;
    }

    public function setCreatedAt(string $created_at): void
    {
        $this->created_at = $created_at;
    }

    public function setTargetedUserId(int $targeted_user_id): void
    {
        $this->targeted_user_id = $targeted_user_id;
    }

    public function getTargetedUserId(): int
    {
        return $this->targeted_user_id;
    }

    public function displayMessage(): string
    {
        $actorUsername = (new UserService())->getById($this->user_id)->getUsername();

        // $actorUsername = "s";
        switch ($this->notification_type) {
            case 'vote_on_question':
                return "Your question received a vote from $actorUsername on " . $this->getCreatedAt();
            case 'vote_on_answer':
                return "Your answer received a vote from $actorUsername on " . $this->getCreatedAt();
            case 'answer':
                return "$actorUsername answered your question on " . $this->getCreatedAt();
            case 'badge_earned':
                return "Congratulations! $actorUsername earned a badge on " . $this->getCreatedAt();
            default:
                return "New notification on " . $this->getCreatedAt();
        }
    }

}
