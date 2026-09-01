<?php

declare(strict_types=1);

$errors =
    $_SESSION['login_errors']
    ?? [];

$old =
    $_SESSION['old_login']
    ?? [];

$success =
    $_SESSION['success']
    ?? null;

unset(
    $_SESSION['login_errors'],
    $_SESSION['old_login'],
    $_SESSION['success']
);
?>

<!doctype html>

<html lang="ms">

<head>

<meta charset="utf-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1"
>

<title>
Login · NasyidOS
</title>

<style>

* {
    box-sizing: border-box;
}

body {
    margin: 0;

    min-height: 100vh;

    background: #f6f7fb;

    color: #111827;

    font-family:
        Inter,
        system-ui,
        -apple-system,
        BlinkMacSystemFont,
        "Segoe UI",
        sans-serif;

    display: flex;

    align-items: center;

    justify-content: center;

    padding: 24px;
}

.container {
    width: 100%;

    max-width: 440px;
}

.logo {
    text-align: center;

    font-size: 26px;

    font-weight: 800;

    margin-bottom: 6px;
}

.powered {
    text-align: center;

    color: #697386;

    font-size: 12px;

    margin-bottom: 24px;
}

.card {
    background: #ffffff;

    border: 1px solid #e5e7eb;

    border-radius: 20px;

    padding: 28px;

    box-shadow:
        0 12px 40px
        rgba(17, 24, 39, 0.06);
}

h1 {
    margin: 0 0 8px;

    font-size: 25px;
}

.subtitle {
    margin: 0 0 24px;

    color: #697386;
}

.form-group {
    margin-bottom: 17px;
}

label {
    display: block;

    font-size: 13px;

    font-weight: 700;

    margin-bottom: 7px;
}

input {
    width: 100%;

    padding: 12px 13px;

    border:
        1px solid #d9dde4;

    border-radius: 11px;

    font: inherit;

    outline: none;

    background: #ffffff;
}

input:focus {
    border-color: #6d5dfc;

    box-shadow:
        0 0 0 3px
        rgba(109, 93, 252, 0.12);
}

button {
    width: 100%;

    border: 0;

    padding: 13px;

    border-radius: 11px;

    background: #111827;

    color: #ffffff;

    font: inherit;

    font-weight: 750;

    cursor: pointer;
}

button:hover {
    opacity: 0.92;
}

.error {
    background: #fff0f0;

    border:
        1px solid #fecaca;

    color: #991b1b;

    padding: 12px 14px;

    border-radius: 11px;

    margin-bottom: 18px;

    font-size: 13px;
}

.success {
    background: #effaf3;

    border:
        1px solid #bbf7d0;

    color: #166534;

    padding: 12px 14px;

    border-radius: 11px;

    margin-bottom: 18px;

    font-size: 13px;
}

.footer {
    text-align: center;

    color: #697386;

    font-size: 13px;

    margin-top: 18px;
}

.footer a {
    color: #111827;

    font-weight: 700;
}

</style>

</head>

<body>

<div class="container">

    <div class="logo">
        NasyidOS
    </div>

    <div class="powered">
        Powered by Sarang Seni Studio
    </div>


    <div class="card">

        <h1>
            Welcome back
        </h1>

        <p class="subtitle">
            Sign in to your NasyidOS account.
        </p>


        <?php if ($success): ?>

            <div class="success">

                <?= Security::escape(
                    $success
                ) ?>

            </div>

        <?php endif; ?>


        <?php if ($errors): ?>

            <div class="error">

                <?php foreach ($errors as $error): ?>

                    <div>
                        <?= Security::escape(
                            $error
                        ) ?>
                    </div>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>


        <form
            method="POST"
            action="?page=login"
        >

            <input
                type="hidden"
                name="csrf_token"
                value="<?= Security::escape(
                    Security::csrfToken()
                ) ?>"
            >


            <div class="form-group">

                <label for="email">
                    Email
                </label>

                <input
                    id="email"
                    name="email"
                    type="email"
                    maxlength="190"
                    autocomplete="email"
                    value="<?= Security::escape(
                        $old['email'] ?? ''
                    ) ?>"
                    required
                >

            </div>


            <div class="form-group">

                <label for="password">
                    Password
                </label>

                <input
                    id="password"
                    name="password"
                    type="password"
                    autocomplete="current-password"
                    required
                >

            </div>


            <button type="submit">
                Log In
            </button>

        </form>

    </div>


    <div class="footer">

        Belum ada akaun?

        <a href="?page=register">
            Create account
        </a>

    </div>

</div>

</body>

</html>