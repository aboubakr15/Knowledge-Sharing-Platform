<?php

namespace App\Models;

class User {
    public int $user_id;
    public string $username;
    public string $email;
    public string $password;
    public ?string $photo;
    public string $created_at;
    public int $reputations;
    public string $role;

    public function __construct(int $user_id, string $username, string $email, string $password, ?string $photo, string $created_at, int $reputations, string $role) {
        $this->user_id = $user_id;
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
        $this->photo = $photo;
        $this->created_at = $created_at;
        $this->reputations = $reputations;
        $this->role = $role;
    }

    // Getters
    public function getUserId(): int {
        return $this->user_id;
    }

    public function getUsername(): string {
        return $this->username;
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function getPassword(): string {
        return $this->password;
    }

    public function getPhoto(): ?string {
        return $this->photo;
    }

    public function getCreatedAt(): string {
        return $this->created_at;
    }

    public function getReputations(): int {
        return $this->reputations;
    }

    public function getRole(): string {
        return $this->role;
    }

    // Setters
    public function setUsername(string $username): void {
        $this->username = $username;
    }

    public function setEmail(string $email): void {
        $this->email = $email;
    }

    public function setPassword(string $password): void {
        $this->password = $password;
    }

    public function setPhoto(?string $photo): void {
        $this->photo = $photo;
    }

    public function setCreatedAt(string $created_at): void {
        $this->created_at = $created_at;
    }

    public function setReputations(int $reputations): void {
        $this->reputations = $reputations;
    }

    public function setRole(string $role): void {
        $this->role = $role;
    }
}

