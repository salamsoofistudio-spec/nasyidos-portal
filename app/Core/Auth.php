<?php

declare(strict_types=1);

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/Security.php';

final class Auth
{
    /**
     * Return currently authenticated user.
     */
    public static function user(): ?array
    {
        if (
            empty($_SESSION['user_id']) ||
            !is_numeric($_SESSION['user_id'])
        ) {
            return null;
        }

        $db =
            Database::connection();

        $stmt = $db->prepare(
            'SELECT
                id,
                name,
                email,
                role,
                subscription_status,
                subscription_ends_at,
                is_active
             FROM users
             WHERE id = ?
             LIMIT 1'
        );

        $stmt->execute([
            (int) $_SESSION['user_id']
        ]);

        $user =
            $stmt->fetch();

        if (!$user) {
            return null;
        }

        if (
            (int) $user['is_active'] !== 1
        ) {
            self::logout();

            return null;
        }

        return $user;
    }


    /**
     * Attempt user login.
     */
    public static function attempt(
        string $email,
        string $password
    ): bool {

        $db =
            Database::connection();

        $email =
            strtolower(trim($email));

        $stmt = $db->prepare(
            'SELECT *
             FROM users
             WHERE email = ?
             LIMIT 1'
        );

        $stmt->execute([
            $email
        ]);

        $user =
            $stmt->fetch();

        if (!$user) {
            return false;
        }

        if (
            !password_verify(
                $password,
                $user['password_hash']
            )
        ) {
            return false;
        }

        if (
            (int) $user['is_active'] !== 1
        ) {
            return false;
        }

        Security::regenerateSession();

        $_SESSION['user_id'] =
            (int) $user['id'];

        $_SESSION['login_at'] =
            time();

        return true;
    }


    /**
     * Register a new user.
     */
    public static function register(
        string $name,
        string $email,
        string $password
    ): int {

        $name =
            trim($name);

        $email =
            strtolower(trim($email));

        if (
            mb_strlen($name) < 2 ||
            mb_strlen($name) > 100
        ) {
            throw new RuntimeException(
                'Nama tidak sah.'
            );
        }

        if (
            !filter_var(
                $email,
                FILTER_VALIDATE_EMAIL
            )
        ) {
            throw new RuntimeException(
                'Email tidak sah.'
            );
        }

        if (
            strlen($password) < 10
        ) {
            throw new RuntimeException(
                'Password mesti sekurang-kurangnya 10 aksara.'
            );
        }

        $db =
            Database::connection();

        $stmt = $db->prepare(
            'SELECT id
             FROM users
             WHERE email = ?
             LIMIT 1'
        );

        $stmt->execute([
            $email
        ]);

        if ($stmt->fetch()) {
            throw new RuntimeException(
                'Email sudah berdaftar.'
            );
        }

        $passwordHash =
            password_hash(
                $password,
                PASSWORD_DEFAULT
            );

        $stmt = $db->prepare(
            'INSERT INTO users
                (
                    name,
                    email,
                    password_hash
                )
             VALUES
                (?, ?, ?)'
        );

        $stmt->execute([
            $name,
            $email,
            $passwordHash
        ]);

        return (int)
            $db->lastInsertId();
    }


    /**
     * Logout current user.
     */
    public static function logout(): void
    {
        Security::destroySession();
    }


    /**
     * Require authenticated user.
     */
    public static function requireLogin(): array
    {
        $user =
            self::user();

        if (!$user) {

            header(
                'Location: ?page=login'
            );

            exit;
        }

        return $user;
    }


    /**
     * Check whether user has active Pro/Team access.
     */
    public static function isPro(
        array $user
    ): bool {

        if (
            !in_array(
                $user['subscription_status'],
                ['pro', 'team'],
                true
            )
        ) {
            return false;
        }

        if (
            empty(
                $user['subscription_ends_at']
            )
        ) {
            return true;
        }

        return strtotime(
            $user['subscription_ends_at']
        ) >= time();
    }


    /**
     * Require Pro/Team access.
     */
    public static function requirePro(): array
    {
        $user =
            self::requireLogin();

        if (
            !self::isPro($user)
        ) {
            header(
                'Location: ?page=pricing'
            );

            exit;
        }

        return $user;
    }


    /**
     * Require administrator access.
     */
    public static function requireAdmin(): array
    {
        $user =
            self::requireLogin();

        if (
            $user['role'] !== 'admin'
        ) {
            http_response_code(403);

            exit('Forbidden');
        }

        return $user;
    }
}