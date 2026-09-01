<?php

declare(strict_types=1);

final class AuthController
{
    /**
     * Handle user registration.
     */
    public static function register(): void
    {
        Security::verifyCsrf();

        $name =
            trim(
                (string) (
                    $_POST['name'] ?? ''
                )
            );

        $email =
            strtolower(
                trim(
                    (string) (
                        $_POST['email'] ?? ''
                    )
                )
            );

        $password =
            (string) (
                $_POST['password'] ?? ''
            );

        $errors = [];


        /*
        |--------------------------------------------------------------------------
        | Validate name
        |--------------------------------------------------------------------------
        */

        if (
            mb_strlen($name) < 2
        ) {
            $errors[] =
                'Nama mesti sekurang-kurangnya 2 aksara.';
        }

        if (
            mb_strlen($name) > 100
        ) {
            $errors[] =
                'Nama terlalu panjang.';
        }


        /*
        |--------------------------------------------------------------------------
        | Validate email
        |--------------------------------------------------------------------------
        */

        if (
            !filter_var(
                $email,
                FILTER_VALIDATE_EMAIL
            )
        ) {
            $errors[] =
                'Sila masukkan email yang sah.';
        }


        /*
        |--------------------------------------------------------------------------
        | Validate password
        |--------------------------------------------------------------------------
        */

        if (
            strlen($password) < 10
        ) {
            $errors[] =
                'Password mesti sekurang-kurangnya 10 aksara.';
        }


        /*
        |--------------------------------------------------------------------------
        | Return validation errors
        |--------------------------------------------------------------------------
        */

        if ($errors) {

            $_SESSION['register_errors'] =
                $errors;

            $_SESSION['old_register'] = [
                'name' => $name,
                'email' => $email,
            ];

            header(
                'Location: ?page=register'
            );

            exit;
        }


        /*
        |--------------------------------------------------------------------------
        | Create account
        |--------------------------------------------------------------------------
        */

        try {

            $userId =
                Auth::register(
                    $name,
                    $email,
                    $password
                );


            /*
            |--------------------------------------------------------------------------
            | Write audit log
            |--------------------------------------------------------------------------
            */

            $db =
                Database::connection();

            $stmt = $db->prepare(
                'INSERT INTO audit_logs
                (
                    user_id,
                    action,
                    meta,
                    ip_address,
                    user_agent
                )
                VALUES
                (?, ?, ?, ?, ?)'
            );

            $stmt->execute([
                $userId,
                'user_registered',
                'Account created',
                $_SERVER['REMOTE_ADDR'] ?? null,
                substr(
                    $_SERVER['HTTP_USER_AGENT'] ?? '',
                    0,
                    500
                )
            ]);


            /*
            |--------------------------------------------------------------------------
            | Registration successful
            |--------------------------------------------------------------------------
            */

            $_SESSION['success'] =
                'Akaun berjaya dibuat. Sila log masuk.';

            unset(
                $_SESSION['old_register'],
                $_SESSION['register_errors']
            );

            header(
                'Location: ?page=login'
            );

            exit;

        } catch (
            RuntimeException $e
        ) {

            $_SESSION['register_errors'] = [
                $e->getMessage()
            ];

            $_SESSION['old_register'] = [
                'name' => $name,
                'email' => $email,
            ];

            header(
                'Location: ?page=register'
            );

            exit;
        }
    }


    /**
     * Handle user login.
     */
    public static function login(): void
    {
        Security::verifyCsrf();

        $email =
            strtolower(
                trim(
                    (string) (
                        $_POST['email'] ?? ''
                    )
                )
            );

        $password =
            (string) (
                $_POST['password'] ?? ''
            );

        $errors = [];


        /*
        |--------------------------------------------------------------------------
        | Validate email
        |--------------------------------------------------------------------------
        */

        if (
            !filter_var(
                $email,
                FILTER_VALIDATE_EMAIL
            )
        ) {
            $errors[] =
                'Sila masukkan email yang sah.';
        }


        /*
        |--------------------------------------------------------------------------
        | Validate password
        |--------------------------------------------------------------------------
        */

        if (
            $password === ''
        ) {
            $errors[] =
                'Sila masukkan password.';
        }


        /*
        |--------------------------------------------------------------------------
        | Return validation errors
        |--------------------------------------------------------------------------
        */

        if ($errors) {

            $_SESSION['login_errors'] =
                $errors;

            $_SESSION['old_login'] = [
                'email' => $email,
            ];

            header(
                'Location: ?page=login'
            );

            exit;
        }


        /*
        |--------------------------------------------------------------------------
        | Authenticate user
        |--------------------------------------------------------------------------
        */

        $authenticated =
            Auth::attempt(
                $email,
                $password
            );


        if (!$authenticated) {

            $_SESSION['login_errors'] = [
                'Email atau password tidak betul.'
            ];

            $_SESSION['old_login'] = [
                'email' => $email,
            ];

            header(
                'Location: ?page=login'
            );

            exit;
        }


        /*
        |--------------------------------------------------------------------------
        | Retrieve authenticated user
        |--------------------------------------------------------------------------
        */

        $user =
            Auth::user();


        if (!$user) {

            Auth::logout();

            $_SESSION['login_errors'] = [
                'Login gagal. Sila cuba lagi.'
            ];

            header(
                'Location: ?page=login'
            );

            exit;
        }


        /*
        |--------------------------------------------------------------------------
        | Write login audit log
        |--------------------------------------------------------------------------
        */

        $db =
            Database::connection();

        $stmt = $db->prepare(
            'INSERT INTO audit_logs
            (
                user_id,
                action,
                meta,
                ip_address,
                user_agent
            )
            VALUES
            (?, ?, ?, ?, ?)'
        );

        $stmt->execute([
            $user['id'],
            'user_login',
            'Successful login',
            $_SERVER['REMOTE_ADDR'] ?? null,
            substr(
                $_SERVER['HTTP_USER_AGENT'] ?? '',
                0,
                500
            )
        ]);


        /*
        |--------------------------------------------------------------------------
        | Login successful
        |--------------------------------------------------------------------------
        */

        unset(
            $_SESSION['login_errors'],
            $_SESSION['old_login']
        );

        header(
            'Location: ?page=dashboard'
        );

        exit;
    }
}