<?php

namespace App\Services;

use App\Models\User;

class AuthService
{
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function auth(string $username): bool
    {
        $_SESSION['username'] = $username;
        return true;
    }

    public function logout(): void
    {
        session_unset();
        session_destroy();
    }

    public function isLoggedIn(): bool
    {
        return isset($_SESSION['username']);
    }

    public function getCurrentUser(): ?User
    {
        if ($this->isLoggedIn()) {
            $username = $_SESSION['username'];
            $user = (new UserService())->getUserByUsername($username);
            if ($user) {
                return $user;
            } else {
                $this->logout();
                return null;
            }
        } else {
            return null;
        }
    }
}