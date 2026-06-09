<?php

namespace App\Controllers;

use PDO;

class UserController
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $this->pdo = $pdo;
    }

    public function isAuthenticated(): bool
    {
        if (empty($_SESSION['session_token'])) {
            return false;
        }

        $stmt = $this->pdo->prepare("SELECT 1 FROM `users` WHERE `session_token` = ? LIMIT 1");
        $stmt->execute([$_SESSION['session_token']]);

        if (!$stmt->fetch()) {
            unset($_SESSION['session_token']);
            return false;
        }

        return true;
    }

    public function register(string $username, string $password): array
    {
        $stmt = $this->pdo->prepare("SELECT id FROM `users` WHERE `username` = ?");
        $stmt->execute([$username]);

        if ($stmt->fetch()) {
            return [
                'success' => false,
                'error' => 'Username already exists'
            ];
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $sessionToken = bin2hex(random_bytes(32));

        $stmt = $this->pdo->prepare("
            INSERT INTO `users` (`username`, `password`, `session_token`) 
            VALUES (?, ?, ?)
        ");

        $result = $stmt->execute([$username, $hashedPassword, $sessionToken]);

        if ($result) {
            $_SESSION['session_token'] = $sessionToken;

            return [
                'success' => true
            ];
        }

        return [
            'success' => false,
            'error' => 'Database error'
        ];
    }

    public function login(string $username, string $password): array
    {
        $stmt = $this->pdo->prepare("SELECT id, password FROM `users` WHERE `username` = ?");
        $stmt->execute([$username]);

        $user = $stmt->fetch();

        if (!$user) {
            return [
                'success' => false,
                'error' => 'Invalid username or password'
            ];
        }

        if (!password_verify($password, $user['password'])) {
            return [
                'success' => false,
                'error' => 'Invalid username or password'
            ];
        }

        $sessionToken = bin2hex(random_bytes(32));

        $stmt = $this->pdo->prepare("
            UPDATE `users` 
            SET `session_token` = ? 
            WHERE `id` = ?
        ");

        $result = $stmt->execute([$sessionToken, $user['id']]);

        if ($result) {
            $_SESSION['session_token'] = $sessionToken;

            return [
                'success' => true
            ];
        }

        return [
            'success' => false,
            'error' => 'Database error'
        ];
    }

    public function getUserData(): array
    {
        $sessionToken = $_SESSION['session_token'];

        $stmt = $this->pdo->prepare("
        SELECT 
            id, 
            username,
            created_at 
        FROM `users` 
        WHERE `session_token` = ? 
        LIMIT 1
    ");

        $stmt->execute([$sessionToken]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return [
                'success' => false,
                'error' => 'User not found'
            ];
        }

        return [
            'success' => true,
            'data' => [
                'id' => $user['id'],
                'username' => $user['username'],
                'created_at' => $user['created_at']
            ]
        ];
    }
}
