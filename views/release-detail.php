<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Release Detail
|--------------------------------------------------------------------------
|
| Expected variables:
| $release
| $tracks
| $platforms
|
*/

function release_detail_e(mixed $value): string
{
    return Security::escape($value);
}

$title =
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

$cover =
    (string) (
        $release['cover_art_url']
        ?? ''
    );

$releaseDate =
    (string) (
        $release['release_date']
        ?? ''
    );

$platformLinksCount =
    (int) (
        $release['platform_links_count']
        ?? 0
    );

function release_detail_status(string $status): string
{
    return match ($status) {
        'planned' => 'Planned',
        'released' => 'Released',
        'archived' => 'Archived',
        default => 'Draft',
    };
}

function release_detail_type(string $type): string
{
    return match ($type) {
        'ep' => 'EP',
        'album' => 'Album',
        'compilation' => 'Compilation',
        default => 'Single',
    };
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

    <title>
        <?= release_detail_e($title) ?> · NasyidOS
    </title>

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
                Workspace / Releases / Detail
            </div>
        </div>

        <div class="release-topbar-actions">

            <a
                href="?page=releases"
                class="release-link"
            >
                My Releases
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

        <a
            href="?page=releases"
            class="release-back"
        >
            ← Back to My Releases
        </a>


        <section class="release-detail-hero">

            <div class="release-cover release-cover-large">

                <?php if ($cover !== ''): ?>

                    <img
                        src="<?= release_detail_e($cover) ?>"
                        alt="<?= release_detail_e($title) ?>"
                    >

                <?php else: ?>

                    <span>
                        <?= release_detail_e(
                            mb_strtoupper(
                                mb_substr($title, 0, 2)
                            )
                        ) ?>
                    </span>

                <?php endif; ?>

            </div>


            <div class="release-detail-heading">

                <div class="release-eyebrow">
                    RELEASE
                </div>

                <h1>
                    <?= release_detail_e($title) ?>
                </h1>

                <p class="release-detail-artist">
                    <?= release_detail_e($artistName) ?>
                </p>

                <div class="release-detail-tags">

                    <span class="release-tag">
                        <?= release_detail_e(
                            release_detail_type($type)
                        ) ?>
                    </span>

                    <span class="release-tag">
                        <?= release_detail_e(
                            release_detail_status($status)
                        ) ?>
                    </span>

                    <?php if (!empty($release['genre'])): ?>

                        <span class="release-tag">
                            <?= release_detail_e(
                                $release['genre']
                            ) ?>
                        </span>

                    <?php endif; ?>

                </div>

            </div>


            <div class="release-detail-actions">

                <a
                    href="?page=release-edit&id=<?= (int) $release['id'] ?>"
                    class="release-button primary"
                >
                    Edit Release
                </a>

            </div>

        </section>


        <section class="release-detail-grid">

            <article class="release-panel">

                <div class="release-panel-label">
                    RELEASE METADATA
                </div>

                <dl class="release-definition-list">

                    <div>
                        <dt>Release Date</dt>
                        <dd>
                            <?= $releaseDate !== ''
                                ? release_detail_e($releaseDate)
                                : 'Not set' ?>
                        </dd>
                    </div>

                    <div>
                        <dt>UPC</dt>
                        <dd>
                            <?= !empty($release['upc'])
                                ? release_detail_e($release['upc'])
                                : 'Not set' ?>
                        </dd>
                    </div>

                    <div>
                        <dt>Distributor</dt>
                        <dd>
                            <?= !empty($release['distributor'])
                                ? release_detail_e(
                                    $release['distributor']
                                )
                                : 'Not set' ?>
                        </dd>
                    </div>

                    <div>
                        <dt>Language</dt>
                        <dd>
                            <?= !empty($release['language'])
                                ? release_detail_e(
                                    $release['language']
                                )
                                : 'Not set' ?>
                        </dd>
                    </div>

                    <div>
                        <dt>Explicit</dt>
                        <dd>
                            <?= !empty($release['is_explicit'])
                                ? 'Yes'
                                : 'No' ?>
                        </dd>
                    </div>

                    <div>
                        <dt>Platform Links</dt>
                        <dd>
                            <?= $platformLinksCount ?>
                        </dd>
                    </div>

                </dl>

            </article>


            <article class="release-panel">

                <div class="release-panel-label">
                    TRACKS
                </div>

                <?php if (!$tracks): ?>

                    <p class="release-muted">
                        No tracks added yet.
                    </p>

                <?php else: ?>

                    <div class="track-list">

                        <?php foreach ($tracks as $track): ?>

                            <div class="track-row">

                                <div class="track-number">
                                    <?= (int) $track['disc_number'] ?>.
                                    <?= (int) $track['track_number'] ?>
                                </div>

                                <div class="track-name">
                                    <?= release_detail_e(
                                        $track['track_title']
                                    ) ?>
                                </div>

                                <div class="track-status">
                                    <?= release_detail_e(
                                        $track['audio_status']
                                        ?? 'missing'
                                    ) ?>
                                </div>

                            </div>

                        <?php endforeach; ?>

                    </div>

                <?php endif; ?>

            </article>


            <article class="release-panel release-panel-wide">

                <div class="release-panel-label">
                    PLATFORM CONNECTIONS
                </div>

                <?php if (!$platforms): ?>

                    <p class="release-muted">
                        No platform connections yet.
                    </p>

                <?php else: ?>

                    <div class="platform-grid">

                        <?php foreach ($platforms as $platform): ?>

                            <div class="platform-row">

                                <div>
                                    <strong>
                                        <?= release_detail_e(
                                            $platform['platform']
                                        ) ?>
                                    </strong>

                                    <span>
                                        <?= release_detail_e(
                                            $platform['platform_status']
                                        ) ?>
                                    </span>
                                </div>

                                <?php if (
                                    !empty(
                                        $platform['platform_url']
                                    )
                                ): ?>

                                    <a
                                        href="<?= release_detail_e(
                                            $platform['platform_url']
                                        ) ?>"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                    >
                                        Open
                                    </a>

                                <?php endif; ?>

                            </div>

                        <?php endforeach; ?>

                    </div>

                <?php endif; ?>

            </article>

        </section>

    </main>

</div>

</body>
</html>
