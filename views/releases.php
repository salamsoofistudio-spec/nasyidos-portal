<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| My Releases
|--------------------------------------------------------------------------
|
| Expected variables:
| $user
| $releases
| $success
|
*/

function release_e(mixed $value): string
{
    return Security::escape($value);
}

$totalReleases = count($releases);

$draftCount = 0;
$plannedCount = 0;
$releasedCount = 0;

foreach ($releases as $release) {
    switch ((string) ($release['release_status'] ?? 'draft')) {
        case 'planned':
            $plannedCount++;
            break;

        case 'released':
            $releasedCount++;
            break;

        default:
            $draftCount++;
            break;
    }
}

function release_status_label(string $status): string
{
    return match ($status) {
        'planned' => 'Planned',
        'released' => 'Released',
        'archived' => 'Archived',
        default => 'Draft',
    };
}

function release_type_label(string $type): string
{
    return match ($type) {
        'ep' => 'EP',
        'album' => 'Album',
        'compilation' => 'Compilation',
        default => 'Single',
    };
}

function release_initials(string $name): string
{
    $name = trim($name);

    if ($name === '') {
        return 'R';
    }

    $words = preg_split('/\s+/', $name);

    if (is_array($words) && count($words) >= 2) {
        return mb_strtoupper(
            mb_substr((string) $words[0], 0, 1) .
            mb_substr((string) $words[1], 0, 1)
        );
    }

    return mb_strtoupper(
        mb_substr($name, 0, 1)
    );
}

?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>My Releases · NasyidOS</title>

    <link
        rel="stylesheet"
        href="assets/css/app.css"
    >

    <link
        rel="stylesheet"
        href="assets/css/releases.css"
    >
</head>

<body>

<div class="release-shell">

    <header class="release-topbar">

        <div>
            <div class="release-brand">
                Nasyid<span>OS</span>
            </div>

            <div class="release-breadcrumb">
                Workspace / Releases
            </div>
        </div>

        <div class="release-topbar-actions">

            <a
                href="?page=dashboard"
                class="release-link"
            >
                Dashboard
            </a>

            <a
                href="?page=artists"
                class="release-link"
            >
                My Artists
            </a>

            <a
                href="?page=logout"
                class="release-link muted"
            >
                Logout
            </a>

        </div>

    </header>


    <main class="release-content">

        <?php if ($success !== ''): ?>

            <div
                class="release-alert"
                aria-live="polite"
            >
                <?= release_e($success) ?>
            </div>

        <?php endif; ?>


        <section class="release-page-header">

            <div>

                <div class="release-eyebrow">
                    RELEASE WORKSPACE
                </div>

                <h1>
                    My Releases
                </h1>

                <p>
                    Manage release metadata, tracks,
                    platform connections and audit readiness.
                </p>

            </div>

        </section>


        <section class="release-stats">

            <article class="release-stat-card">
                <span>Total Releases</span>
                <strong><?= $totalReleases ?></strong>
            </article>

            <article class="release-stat-card">
                <span>Draft</span>
                <strong><?= $draftCount ?></strong>
            </article>

            <article class="release-stat-card">
                <span>Planned</span>
                <strong><?= $plannedCount ?></strong>
            </article>

            <article class="release-stat-card">
                <span>Released</span>
                <strong><?= $releasedCount ?></strong>
            </article>

        </section>


        <?php if (!$releases): ?>

            <section class="release-empty">

                <div class="release-empty-icon">
                    +
                </div>

                <h2>
                    No releases yet
                </h2>

                <p>
                    Your release workspace is ready.
                    Release creation will be connected
                    in the next release workflow step.
                </p>

            </section>

        <?php else: ?>

            <section class="release-list">

                <?php foreach ($releases as $release): ?>

                    <?php
                    $releaseTitle =
                        (string) (
                            $release['title']
                            ?? 'Untitled Release'
                        );

                    $artistName =
                        (string) (
                            $release['artist_name']
                            ?? 'Unknown Artist'
                        );

                    $status =
                        (string) (
                            $release['release_status']
                            ?? 'draft'
                        );

                    $type =
                        (string) (
                            $release['release_type']
                            ?? 'single'
                        );

                    $releaseDate =
                        (string) (
                            $release['release_date']
                            ?? ''
                        );

                    $cover =
                        (string) (
                            $release['cover_art_url']
                            ?? ''
                        );

                    $trackCount =
                        (int) (
                            $release['track_count']
                            ?? 0
                        );

                    $platformCount =
                        (int) (
                            $release['platform_count']
                            ?? 0
                        );

                    $livePlatformCount =
                        (int) (
                            $release['live_platform_count']
                            ?? 0
                        );
                    ?>

                    <article class="release-card">

                        <div class="release-cover">

                            <?php if ($cover !== ''): ?>

                                <img
                                    src="<?= release_e($cover) ?>"
                                    alt="<?= release_e($releaseTitle) ?>"
                                    loading="lazy"
                                >

                            <?php else: ?>

                                <span>
                                    <?= release_e(
                                        release_initials(
                                            $releaseTitle
                                        )
                                    ) ?>
                                </span>

                            <?php endif; ?>

                        </div>


                        <div class="release-main">

                            <div class="release-card-head">

                                <div>

                                    <div class="release-kicker">
                                        <?= release_e(
                                            release_type_label($type)
                                        ) ?>
                                    </div>

                                    <h2>
                                        <?= release_e($releaseTitle) ?>
                                    </h2>

                                    <p class="release-artist">
                                        <?= release_e($artistName) ?>
                                    </p>

                                </div>


                                <span
                                    class="release-status status-<?= release_e($status) ?>"
                                >
                                    <?= release_e(
                                        release_status_label($status)
                                    ) ?>
                                </span>

                            </div>


                            <div class="release-meta">

                                <span>
                                    <?= $releaseDate !== ''
                                        ? release_e($releaseDate)
                                        : 'No release date' ?>
                                </span>

                                <span>
                                    <?= $trackCount ?>
                                    <?= $trackCount === 1
                                        ? 'track'
                                        : 'tracks' ?>
                                </span>

                                <span>
                                    <?= $platformCount ?>
                                    platforms
                                </span>

                                <?php if ($livePlatformCount > 0): ?>

                                    <span class="release-live">
                                        <?= $livePlatformCount ?>
                                        live
                                    </span>

                                <?php endif; ?>

                            </div>


                            <div class="release-card-actions">

                                <a
                                    href="?page=release&id=<?= (int) $release['id'] ?>"
                                    class="release-button secondary"
                                >
                                    View Release
                                </a>

                                <a
                                    href="?page=release-edit&id=<?= (int) $release['id'] ?>"
                                    class="release-button secondary"
                                >
                                    Edit
                                </a>

                            </div>

                        </div>

                    </article>

                <?php endforeach; ?>

            </section>

        <?php endif; ?>

    </main>

</div>

</body>
</html>
