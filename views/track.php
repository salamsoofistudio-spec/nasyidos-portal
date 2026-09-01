<?php

declare(strict_types=1);


/*
|--------------------------------------------------------------------------
| Track Detail
|--------------------------------------------------------------------------
|
| Expected variables:
|
| $release
| $track
|
*/


/*
|--------------------------------------------------------------------------
| Release
|--------------------------------------------------------------------------
*/

$releaseId =
    (int) (
        $release['id']
        ?? $track['release_id']
        ?? 0
    );


$releaseTitle =
    trim(
        (string) (
            $release['title']
            ?? $release['release_title']
            ?? ''
        )
    );


$artistName =
    trim(
        (string) (
            $release['artist_name']
            ?? ''
        )
    );


/*
|--------------------------------------------------------------------------
| Track
|--------------------------------------------------------------------------
*/

$trackId =
    (int) (
        $track['id']
        ?? 0
    );


$trackTitle =
    trim(
        (string) (
            $track['track_title']
            ?? ''
        )
    );


$trackNumber =
    (int) (
        $track['track_number']
        ?? 0
    );


$discNumber =
    (int) (
        $track['disc_number']
        ?? 1
    );


$versionLabel =
    trim(
        (string) (
            $track['version_label']
            ?? ''
        )
    );


$isrc =
    trim(
        (string) (
            $track['isrc']
            ?? ''
        )
    );


$durationSeconds =
    (int) (
        $track['duration_seconds']
        ?? 0
    );


$language =
    trim(
        (string) (
            $track['language']
            ?? ''
        )
    );


$lyrics =
    trim(
        (string) (
            $track['lyrics']
            ?? ''
        )
    );


$lyricsStatus =
    trim(
        (string) (
            $track['lyrics_status']
            ?? ''
        )
    );


$audioStatus =
    trim(
        (string) (
            $track['audio_status']
            ?? ''
        )
    );


$isExplicit =
    !empty(
        $track['is_explicit']
    );


$featuringArtists =
    trim(
        (string) (
            $track['featuring_artists']
            ?? ''
        )
    );


$composers =
    trim(
        (string) (
            $track['composers']
            ?? ''
        )
    );


$lyricists =
    trim(
        (string) (
            $track['lyricists']
            ?? ''
        )
    );


$producers =
    trim(
        (string) (
            $track['producers']
            ?? ''
        )
    );


$notes =
    trim(
        (string) (
            $track['notes']
            ?? ''
        )
    );


/*
|--------------------------------------------------------------------------
| Track position
|--------------------------------------------------------------------------
*/

$trackPosition =
    $discNumber .
    '.' .
    $trackNumber;


/*
|--------------------------------------------------------------------------
| Duration
|--------------------------------------------------------------------------
*/

$durationLabel = '—';

if ($durationSeconds > 0) {

    $minutes =
        intdiv(
            $durationSeconds,
            60
        );

    $seconds =
        $durationSeconds % 60;

    $durationLabel =
        $minutes .
        ':' .
        str_pad(
            (string) $seconds,
            2,
            '0',
            STR_PAD_LEFT
        );
}


/*
|--------------------------------------------------------------------------
| Status labels
|--------------------------------------------------------------------------
*/

$lyricsStatusLabel =
    match ($lyricsStatus) {

        'complete' =>
            'Complete',

        'in_progress' =>
            'In Progress',

        'not_started' =>
            'Not Started',

        default =>
            $lyricsStatus !== ''
                ? ucwords(
                    str_replace(
                        '_',
                        ' ',
                        $lyricsStatus
                    )
                )
                : '—',
    };


$audioStatusLabel =
    match ($audioStatus) {

        'ready' =>
            'Ready',

        'in_progress' =>
            'In Progress',

        'not_ready' =>
            'Not Ready',

        default =>
            $audioStatus !== ''
                ? ucwords(
                    str_replace(
                        '_',
                        ' ',
                        $audioStatus
                    )
                )
                : '—',
    };


$explicitLabel =
    $isExplicit
        ? 'Yes'
        : 'No';

?>
<!doctype html>

<html lang="en">

<head>

    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>
        <?= Security::escape($trackTitle) ?>
        · NasyidOS
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


    <!--
    |--------------------------------------------------------------------------
    | TOP BAR
    |--------------------------------------------------------------------------
    -->

    <header class="release-topbar">

        <div>

            <div class="release-brand">

                <a
                    href="?page=home"
                    class="release-link"
                >
                    NasyidOS
                </a>

            </div>

            <div class="release-breadcrumb">
                Workspace / Releases / Track
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


    <!--
    |--------------------------------------------------------------------------
    | CONTENT
    |--------------------------------------------------------------------------
    -->

    <main class="release-content">


        <!--
        |--------------------------------------------------------------------------
        | BACK
        |--------------------------------------------------------------------------
        -->

        <div class="release-back">

            <a
                href="?page=release&id=<?= $releaseId ?>"
                class="release-back"
            >
                ← Back to Release
            </a>

        </div>


        <!--
        |--------------------------------------------------------------------------
        | HERO
        |--------------------------------------------------------------------------
        -->

        <section class="release-detail-hero">


            <div
                class="release-detail-heading"
                style="grid-column: 1 / -1;"
            >

                <div class="release-eyebrow">
                    Track
                </div>


                <h1>
                    <?= Security::escape($trackTitle) ?>
                </h1>


                <p class="release-detail-artist">

                    <?= Security::escape($artistName) ?>

                    <?php if (
                        $artistName !== '' &&
                        $releaseTitle !== ''
                    ): ?>

                        ·

                    <?php endif; ?>

                    <?= Security::escape($releaseTitle) ?>

                    <?php if ($versionLabel !== ''): ?>

                        ·
                        <?= Security::escape($versionLabel) ?>

                    <?php endif; ?>

                </p>


                <div class="release-detail-tags">

                    <span class="release-tag">
                        Track <?= Security::escape($trackPosition) ?>
                    </span>


                    <?php if ($versionLabel !== ''): ?>

                        <span class="release-tag">
                            <?= Security::escape($versionLabel) ?>
                        </span>

                    <?php endif; ?>


                    <?php if ($language !== ''): ?>

                        <span class="release-tag">
                            <?= Security::escape($language) ?>
                        </span>

                    <?php endif; ?>

                </div>


                <div class="release-card-actions">

                    <a
                        href="?page=track-edit&id=<?= $trackId ?>&release_id=<?= $releaseId ?>"
                        class="release-button primary"
                    >
                        Edit Track
                    </a>


                    <a
                        href="?page=release&id=<?= $releaseId ?>"
                        class="release-button secondary"
                    >
                        View Release
                    </a>

                </div>

            </div>


            <div class="release-detail-actions">

                <div class="release-stat-card">

                    <span>
                        TRACK
                    </span>

                    <strong>
                        <?= Security::escape($trackPosition) ?>
                    </strong>

                </div>

            </div>


        </section>


        <!--
        |--------------------------------------------------------------------------
        | TRACK IDENTITY
        |--------------------------------------------------------------------------
        -->

        <div class="release-detail-grid">


            <section class="release-panel">

                <div class="release-panel-label">
                    01 · TRACK IDENTITY
                </div>


                <dl class="release-definition-list">


                    <div>

                        <dt>
                            Track Number
                        </dt>

                        <dd>
                            <?= Security::escape($trackNumber) ?>
                        </dd>

                    </div>


                    <div>

                        <dt>
                            Disc Number
                        </dt>

                        <dd>
                            <?= Security::escape($discNumber) ?>
                        </dd>

                    </div>


                    <div>

                        <dt>
                            Version
                        </dt>

                        <dd>
                            <?= Security::escape(
                                $versionLabel !== ''
                                    ? $versionLabel
                                    : '—'
                            ) ?>
                        </dd>

                    </div>


                    <div>

                        <dt>
                            ISRC
                        </dt>

                        <dd>
                            <?= Security::escape(
                                $isrc !== ''
                                    ? $isrc
                                    : '—'
                            ) ?>
                        </dd>

                    </div>


                    <div>

                        <dt>
                            Duration
                        </dt>

                        <dd>
                            <?= Security::escape($durationLabel) ?>
                        </dd>

                    </div>


                    <div>

                        <dt>
                            Language
                        </dt>

                        <dd>
                            <?= Security::escape(
                                $language !== ''
                                    ? $language
                                    : '—'
                            ) ?>
                        </dd>

                    </div>


                </dl>

            </section>


            <!--
            |--------------------------------------------------------------------------
            | READINESS
            |--------------------------------------------------------------------------
            -->

            <section class="release-panel">

                <div class="release-panel-label">
                    02 · READINESS
                </div>


                <dl class="release-definition-list">


                    <div>

                        <dt>
                            Lyrics
                        </dt>

                        <dd>
                            <?= Security::escape(
                                $lyricsStatusLabel
                            ) ?>
                        </dd>

                    </div>


                    <div>

                        <dt>
                            Audio
                        </dt>

                        <dd>
                            <?= Security::escape(
                                $audioStatusLabel
                            ) ?>
                        </dd>

                    </div>


                    <div>

                        <dt>
                            Explicit
                        </dt>

                        <dd>
                            <?= Security::escape(
                                $explicitLabel
                            ) ?>
                        </dd>

                    </div>


                </dl>

            </section>


            <!--
            |--------------------------------------------------------------------------
            | CREDITS
            |--------------------------------------------------------------------------
            -->

            <section class="release-panel release-panel-wide">

                <div class="release-panel-label">
                    03 · CREDITS
                </div>


                <dl class="release-definition-list">


                    <div>

                        <dt>
                            Featuring Artists
                        </dt>

                        <dd>
                            <?= Security::escape(
                                $featuringArtists !== ''
                                    ? $featuringArtists
                                    : '—'
                            ) ?>
                        </dd>

                    </div>


                    <div>

                        <dt>
                            Composers
                        </dt>

                        <dd>
                            <?= Security::escape(
                                $composers !== ''
                                    ? $composers
                                    : '—'
                            ) ?>
                        </dd>

                    </div>


                    <div>

                        <dt>
                            Lyricists
                        </dt>

                        <dd>
                            <?= Security::escape(
                                $lyricists !== ''
                                    ? $lyricists
                                    : '—'
                            ) ?>
                        </dd>

                    </div>


                    <div>

                        <dt>
                            Producers
                        </dt>

                        <dd>
                            <?= Security::escape(
                                $producers !== ''
                                    ? $producers
                                    : '—'
                            ) ?>
                        </dd>

                    </div>


                </dl>

            </section>


            <!--
            |--------------------------------------------------------------------------
            | LYRICS
            |--------------------------------------------------------------------------
            -->

            <section class="release-panel release-panel-wide">

                <div class="release-panel-label">
                    04 · LYRICS
                </div>


                <?php if ($lyrics !== ''): ?>

                    <div class="release-muted">

                        <?= nl2br(
                            Security::escape(
                                $lyrics
                            )
                        ) ?>

                    </div>

                <?php else: ?>

                    <p class="release-muted">
                        No lyrics added yet.
                    </p>

                <?php endif; ?>

            </section>


            <!--
            |--------------------------------------------------------------------------
            | NOTES
            |--------------------------------------------------------------------------
            -->

            <?php if ($notes !== ''): ?>

                <section class="release-panel release-panel-wide">

                    <div class="release-panel-label">
                        05 · NOTES
                    </div>


                    <div class="release-muted">

                        <?= nl2br(
                            Security::escape(
                                $notes
                            )
                        ) ?>

                    </div>

                </section>

            <?php endif; ?>


        </div>


    </main>

</div>

</body>

</html>