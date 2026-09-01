<?php

declare(strict_types=1);
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
NasyidOS · Digital Growth Intelligence
</title>

<style>

* {
    box-sizing: border-box;
}

body {
    margin: 0;

    font-family:
        Inter,
        system-ui,
        -apple-system,
        BlinkMacSystemFont,
        "Segoe UI",
        sans-serif;

    color: #111827;

    background: #f8fafc;
}

.container {
    width: min(
        1120px,
        calc(100% - 40px)
    );

    margin: auto;
}

.nav {
    height: 72px;

    display: flex;

    align-items: center;

    justify-content: space-between;
}

.logo {
    font-size: 21px;

    font-weight: 800;

    letter-spacing: -0.03em;
}

.logo span {
    color: #6d5dfc;
}

.nav-links {
    display: flex;

    gap: 10px;
}

.nav-links a {
    text-decoration: none;

    padding: 9px 14px;

    border-radius: 9px;

    color: #374151;

    font-size: 14px;

    font-weight: 650;
}

.nav-links a.primary {
    background: #111827;

    color: #ffffff;
}

.hero {
    padding:
        100px
        0
        110px;

    text-align: center;
}

.badge {
    display: inline-flex;

    padding: 7px 12px;

    border-radius: 999px;

    background: #efedff;

    color: #5b4ee5;

    font-size: 12px;

    font-weight: 750;

    margin-bottom: 20px;
}

h1 {
    max-width: 780px;

    margin:
        0
        auto
        20px;

    font-size:
        clamp(
            40px,
            7vw,
            70px
        );

    line-height: 1.02;

    letter-spacing: -0.055em;
}

.hero p {
    max-width: 650px;

    margin:
        0
        auto
        30px;

    color: #64748b;

    font-size: 18px;

    line-height: 1.7;
}

.actions {
    display: flex;

    justify-content: center;

    gap: 12px;

    flex-wrap: wrap;
}

.button {
    display: inline-block;

    text-decoration: none;

    padding:
        13px
        18px;

    border-radius: 11px;

    font-weight: 750;

    font-size: 14px;
}

.button.primary {
    background: #111827;

    color: #ffffff;
}

.button.secondary {
    background: #ffffff;

    color: #111827;

    border:
        1px solid #e5e7eb;
}

.powered {
    text-align: center;

    padding:
        25px
        0
        40px;

    color: #94a3b8;

    font-size: 12px;
}

</style>

</head>

<body>

<div class="container">

    <nav class="nav">

        <div class="logo">
            Nasyid<span>OS</span>
        </div>

        <div class="nav-links">

            <a href="?page=login">
                Login
            </a>

            <a
                href="?page=register"
                class="primary"
            >
                Get Started
            </a>

        </div>

    </nav>


    <main class="hero">

        <div class="badge">
            Digital Growth Intelligence for Nasyid
        </div>

        <h1>
            Turn your Nasyid release into a digital growth system.
        </h1>

        <p>
            NasyidOS membantu artis dan pasukan muzik
            mengoptimumkan release, content dan kehadiran
            digital merentas Spotify, YouTube, TikTok,
            Instagram dan platform utama.
        </p>


        <div class="actions">

            <a
                href="?page=register"
                class="button primary"
            >
                Start Free
            </a>

            <a
                href="?page=login"
                class="button secondary"
            >
                Sign In
            </a>

        </div>

    </main>


    <div class="powered">
        NasyidOS · Powered by Sarang Seni Studio
    </div>

</div>

</body>

</html>