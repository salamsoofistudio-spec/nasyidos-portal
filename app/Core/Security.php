<?php

declare(strict_types=1);

final class Security
{
    /**
     * Generate or retrieve the current CSRF token.
     */
    public static function csrfToken(): string
    {
        if (
            empty($_SESSION['csrf_token']) ||
            !is_string($_SESSION['csrf_token'])
        ) {
            $_SESSION['csrf_token'] =
                bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }


    /**
     * Verify a submitted CSRF token.
     */
    public static function verifyCsrf(): void
    {
        $sessionToken =
            $_SESSION['csrf_token'] ?? '';

        $submittedToken =
            $_POST['csrf_token'] ?? '';

        if (
            !is_string($sessionToken) ||
            !is_string($submittedToken) ||
            $sessionToken === '' ||
            $submittedToken === '' ||
            !hash_equals(
                $sessionToken,
                $submittedToken
            )
        ) {
            http_response_code(419);

            exit(
                'Invalid or expired security token.'
            );
        }
    }


    /**
     * Safely escape output for HTML.
     */
    public static function escape(
        mixed $value
    ): string {
        return htmlspecialchars(
            (string) $value,
            ENT_QUOTES |
            ENT_SUBSTITUTE |
            ENT_HTML5,
            'UTF-8'
        );
    }


    /**
     * Regenerate the session ID.
     */
    public static function regenerateSession(): void
    {
        session_regenerate_id(true);
    }


    /**
     * Destroy the current session.
     */
    public static function destroySession(): void
    {
        $_SESSION = [];

        if (
            ini_get('session.use_cookies')
        ) {
            $params =
                session_get_cookie_params();

            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'] ?? '',
                (bool) $params['secure'],
                (bool) $params['httponly']
            );
        }

        session_destroy();
    }
}