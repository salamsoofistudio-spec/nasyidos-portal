<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Dashboard View
|--------------------------------------------------------------------------
|
| $user is provided by index.php
|
*/

$name =
    $user['name']
    ?? 'Artist';

$plan =
    strtoupper(
        $user['subscription_status']
        ?? 'free'
    );

$isPro =
    Auth::isPro($user);
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
Dashboard · NasyidOS
</title>

<style>

* {
    box-sizing: border-box;
}

body {
    margin: 0;

    background: #f7f8fc;

    color: #111827;

    font-family:
        Inter,
        system-ui,
        -apple-system,
        BlinkMacSystemFont,
        "Segoe UI",
        sans-serif;
}


/*
|--------------------------------------------------------------------------
| Layout
|--------------------------------------------------------------------------
*/

.layout {
    min-height: 100vh;

    display: flex;
}


/*
|--------------------------------------------------------------------------
| Sidebar
|--------------------------------------------------------------------------
*/

.sidebar {
    width: 245px;

    background: #ffffff;

    border-right:
        1px solid #e8eaf0;

    padding: 22px 16px;

    flex-shrink: 0;
}

.logo {
    padding:
        5px
        12px
        28px;

    font-size: 21px;

    font-weight: 850;

    letter-spacing: -0.04em;
}

.logo span {
    color: #6d5dfc;
}

.section-label {
    padding:
        0
        12px
        8px;

    color: #9aa1af;

    font-size: 10px;

    font-weight: 800;

    text-transform: uppercase;

    letter-spacing: 0.08em;
}

.menu {
    display: flex;

    flex-direction: column;

    gap: 4px;

    margin-bottom: 25px;
}

.menu a {
    text-decoration: none;

    color: #667085;

    padding:
        10px
        12px;

    border-radius: 9px;

    font-size: 13px;

    font-weight: 650;
}

.menu a:hover {
    background: #f4f3ff;

    color: #5b4ee5;
}

.menu a.active {
    background: #efedff;

    color: #5b4ee5;
}


/*
|--------------------------------------------------------------------------
| Upgrade card
|--------------------------------------------------------------------------
*/

.upgrade {
    margin-top: auto;

    padding: 15px;

    border:
        1px solid #e8eaf0;

    border-radius: 13px;

    background: #fafaff;
}

.upgrade strong {
    display: block;

    font-size: 13px;

    margin-bottom: 5px;
}

.upgrade p {
    margin: 0 0 12px;

    font-size: 11px;

    line-height: 1.5;

    color: #7b8494;
}

.upgrade a {
    display: block;

    text-align: center;

    text-decoration: none;

    background: #111827;

    color: #ffffff;

    padding: 9px;

    border-radius: 8px;

    font-size: 11px;

    font-weight: 750;
}


/*
|--------------------------------------------------------------------------
| Main
|--------------------------------------------------------------------------
*/

.main {
    flex: 1;

    min-width: 0;
}


/*
|--------------------------------------------------------------------------
| Topbar
|--------------------------------------------------------------------------
*/

.topbar {
    height: 70px;

    background: #ffffff;

    border-bottom:
        1px solid #e8eaf0;

    display: flex;

    align-items: center;

    justify-content: space-between;

    padding:
        0
        28px;
}

.topbar-right {
    display: flex;

    align-items: center;

    gap: 15px;
}

.plan {
    font-size: 10px;

    font-weight: 800;

    padding:
        5px
        8px;

    border-radius: 6px;

    background: #f1f3f6;

    color: #687183;
}

.plan.pro {
    background: #efedff;

    color: #5b4ee5;
}

.user {
    font-size: 13px;

    font-weight: 700;
}


/*
|--------------------------------------------------------------------------
| Content
|--------------------------------------------------------------------------
*/

.content {
    padding: 32px;

    max-width: 1400px;
}

.eyebrow {
    color: #7b8494;

    font-size: 12px;

    margin-bottom: 7px;
}

h1 {
    margin: 0;

    font-size: 28px;

    letter-spacing: -0.035em;
}

.subtitle {
    margin:
        8px
        0
        28px;

    color: #7b8494;

    font-size: 14px;
}


/*
|--------------------------------------------------------------------------
| Cards
|--------------------------------------------------------------------------
*/

.grid {
    display: grid;

    grid-template-columns:
        repeat(
            3,
            minmax(
                0,
                1fr
            )
        );

    gap: 15px;

    margin-bottom: 20px;
}

.card {
    background: #ffffff;

    border:
        1px solid #e8eaf0;

    border-radius: 14px;

    padding: 20px;
}

.card-label {
    color: #7b8494;

    font-size: 11px;

    font-weight: 700;

    margin-bottom: 9px;
}

.score {
    font-size: 30px;

    font-weight: 800;

    letter-spacing: -0.04em;
}

.score span {
    color: #a1a8b5;

    font-size: 14px;
}

.card-note {
    margin-top: 8px;

    color: #89919f;

    font-size: 11px;
}


/*
|--------------------------------------------------------------------------
| Readiness
|--------------------------------------------------------------------------
*/

.readiness {
    display: grid;

    grid-template-columns:
        1.4fr
        1fr;

    gap: 15px;
}

.readiness-card {
    background: #ffffff;

    border:
        1px solid #e8eaf0;

    border-radius: 14px;

    padding: 22px;
}

.readiness-title {
    font-size: 15px;

    font-weight: 800;

    margin-bottom: 5px;
}

.readiness-description {
    color: #7b8494;

    font-size: 12px;

    margin-bottom: 22px;
}

.progress {
    height: 8px;

    background: #edf0f4;

    border-radius: 999px;

    overflow: hidden;
}

.progress-bar {
    width: 0%;

    height: 100%;

    background: #6d5dfc;
}

.actions-list {
    margin: 0;

    padding: 0;

    list-style: none;
}

.actions-list li {
    padding:
        11px
        0;

    border-bottom:
        1px solid #f0f1f4;

    font-size: 12px;

    color: #5f6878;
}

.actions-list li:last-child {
    border-bottom: 0;
}


/*
|--------------------------------------------------------------------------
| Responsive
|--------------------------------------------------------------------------
*/

@media (
    max-width: 850px
) {

    .sidebar {
        display: none;
    }

    .content {
        padding: 22px;
    }

    .grid {
        grid-template-columns: 1fr;
    }

    .readiness {
        grid-template-columns: 1fr;
    }

}

</style>

</head>

<body>

<div class="layout">


    <!-- SIDEBAR -->

    <aside class="sidebar">

        <div class="logo">
            Nasyid<span>OS</span>
        </div>


        <div class="section-label">
            Workspace
        </div>

        <nav class="menu">

            <a
                href="?page=dashboard"
                class="active"
            >
                Overview
            </a>

            <a href="#">
                My Artists
            </a>

            <a href="#">
                Releases
            </a>

            <a href="#">
                Release Lab
            </a>

            <a href="#">
                Content Engine
            </a>

        </nav>


        <div class="section-label">
            Intelligence
        </div>

        <nav class="menu">

            <a href="#">
                Analytics
            </a>

            <a href="#">
                Recommendations
            </a>

        </nav>


        <div class="upgrade">

            <strong>
                Unlock NasyidOS Pro
            </strong>

            <p>
                Advanced release intelligence,
                platform optimization and
                content strategy.
            </p>

            <a href="#">
                Upgrade
            </a>

        </div>

    </aside>


    <!-- MAIN -->

    <main class="main">


        <!-- TOPBAR -->

        <header class="topbar">

            <div>
                Dashboard
            </div>


            <div class="topbar-right">

                <div
                    class="plan <?= $isPro ? 'pro' : '' ?>"
                >
                    <?= Security::escape($plan) ?>
                </div>

                <div class="user">
                    <?= Security::escape($name) ?>
                </div>

                <a
                    href="?page=logout"
                    style="
                        text-decoration:none;
                        color:#667085;
                        font-size:13px;
                    "
                >
                    Logout
                </a>

            </div>

        </header>


        <!-- CONTENT -->

        <section class="content">

            <div class="eyebrow">
                Overview
            </div>

            <h1>
                Good to see you, <?= Security::escape($name) ?>.
            </h1>

            <p class="subtitle">
                Here's your current digital growth overview.
            </p>


            <!-- PLATFORM SCORE CARDS -->

            <div class="grid">


                <div class="card">

                    <div class="card-label">
                        Spotify
                    </div>

                    <div class="score">
                        —
                        <span>/100</span>
                    </div>

                    <div class="card-note">
                        Release optimization score
                    </div>

                </div>


                <div class="card">

                    <div class="card-label">
                        YouTube
                    </div>

                    <div class="score">
                        —
                        <span>/100</span>
                    </div>

                    <div class="card-note">
                        Channel & content score
                    </div>

                </div>


                <div class="card">

                    <div class="card-label">
                        TikTok
                    </div>

                    <div class="score">
                        —
                        <span>/100</span>
                    </div>

                    <div class="card-note">
                        Short-form readiness
                    </div>

                </div>

            </div>


            <!-- LOWER AREA -->

            <div class="readiness">


                <div class="readiness-card">

                    <div class="readiness-title">
                        Release Readiness
                    </div>

                    <div class="readiness-description">
                        Your release optimization score
                        will appear here after your first
                        release audit.
                    </div>


                    <div class="progress">

                        <div class="progress-bar"></div>

                    </div>

                </div>


                <div class="readiness-card">

                    <div class="readiness-title">
                        Recommended Actions
                    </div>

                    <div class="readiness-description">
                        Your next highest-impact actions.
                    </div>


                    <ul class="actions-list">

                        <li>
                            Add your first release
                        </li>

                        <li>
                            Complete artist profile
                        </li>

                        <li>
                            Run your first platform audit
                        </li>

                    </ul>

                </div>


            </div>

        </section>

    </main>

</div>

</body>

</html>