<?php

declare(strict_types=1);

$success =
    $_SESSION['success']
    ?? null;

unset(
    $_SESSION['success']
);

$artists =
    $artists
    ?? [];
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
        My Artists · NasyidOS
    </title>

    <link
        rel="stylesheet"
        href="assets/css/app.css"
    >

    <link
        rel="stylesheet"
        href="assets/css/artists.css"
    >

</head>

<body>

<div class="container">

    <main>

        <header class="page-header">

            <div>

                <div class="page-eyebrow">
                    Workspace
                </div>

                <h1 class="page-title">
                    My Artists
                </h1>

                <p class="page-description">
                    Manage artist profiles and connect
                    their digital platforms before running
                    release and content audits.
                </p>

            </div>


            <a
                href="?page=artists-create"
                class="primary-button"
            >
                + Add Artist
            </a>

        </header>


        <?php if ($success): ?>

            <div class="alert alert-success">

                <?= Security::escape(
                    $success
                ) ?>

            </div>

        <?php endif; ?>


        <?php if (!$artists): ?>

            <section class="empty-state">

                <div class="empty-state-inner">

                    <div class="empty-icon">
                        +
                    </div>

                    <h2>
                        No artists yet
                    </h2>

                    <p>
                        Add your first artist to start
                        building a digital growth profile
                        and prepare for your first NasyidOS audit.
                    </p>

                    <a
                        href="?page=artists-create"
                        class="primary-button"
                    >
                        + Add Your First Artist
                    </a>

                </div>

            </section>

        <?php else: ?>

            <section class="artist-grid">

                <?php foreach ($artists as $artist): ?>

                    <?php

                    $displayName =
                        $artist['stage_name']
                        ?: $artist['artist_name'];

                    $initial =
                        strtoupper(
                            mb_substr(
                                $displayName,
                                0,
                                1
                            )
                        );

                    $platforms = [
                        'Spotify' =>
                            !empty(
                                $artist['spotify_url']
                            ),

                        'Apple Music' =>
                            !empty(
                                $artist['apple_music_url']
                            ),

                        'YouTube' =>
                            !empty(
                                $artist['youtube_url']
                            ),

                        'TikTok' =>
                            !empty(
                                $artist['tiktok_url']
                            ),

                        'Instagram' =>
                            !empty(
                                $artist['instagram_url']
                            ),

                        'Facebook' =>
                            !empty(
                                $artist['facebook_url']
                            ),
                    ];
                    ?>

                    <article class="artist-card">

                        <div class="artist-card-top">

                            <div class="artist-identity">

                                <div class="artist-avatar">
                                    <?= Security::escape(
                                        $initial
                                    ) ?>
                                </div>

                                <div>

                                    <h2 class="artist-name">
                                        <?= Security::escape(
                                            $displayName
                                        ) ?>
                                    </h2>

                                    <?php if (
                                        !empty(
                                            $artist['stage_name']
                                        ) &&
                                        $artist['stage_name']
                                        !==
                                        $artist['artist_name']
                                    ): ?>

                                        <div class="artist-stage">

                                            <?= Security::escape(
                                                $artist['artist_name']
                                            ) ?>

                                        </div>

                                    <?php endif; ?>

                                </div>

                            </div>


                            <div class="artist-status">
                                Active
                            </div>

                        </div>


                        <div class="artist-meta">

                            <?php if (
                                !empty(
                                    $artist['genre']
                                )
                            ): ?>

                                <span class="artist-tag">

                                    <?= Security::escape(
                                        $artist['genre']
                                    ) ?>

                                </span>

                            <?php endif; ?>


                            <?php if (
                                !empty(
                                    $artist['subgenre']
                                )
                            ): ?>

                                <span class="artist-tag">

                                    <?= Security::escape(
                                        $artist['subgenre']
                                    ) ?>

                                </span>

                            <?php endif; ?>

                        </div>


                        <div class="platform-list">

                            <?php foreach (
                                $platforms
                                as $platform => $connected
                            ): ?>

                                <span
                                    class="platform-badge <?= $connected ? 'connected' : '' ?>"
                                >

                                    <?= Security::escape(
                                        $platform
                                    ) ?>

                                    <?= $connected ? '✓' : '' ?>

                                </span>

                            <?php endforeach; ?>

                        </div>


                        <div class="artist-actions">

                            <a
    href="?page=artist&id=<?= (int) $artist['id'] ?>"
    class="secondary-button"
>
                                View Artist
                            </a>

                            <a
    href="?page=artist-edit&id=<?= (int) $artist['id'] ?>"
    class="secondary-button"
>
                                Edit
                            </a>

                        </div>

                    </article>

                <?php endforeach; ?>

            </section>

        <?php endif; ?>

    </main>

</div>

</body>

</html>