<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Artist Detail View
|--------------------------------------------------------------------------
|
| Expected variables:
|
| $artist
|
| These variables are supplied by ArtistController::show()
|
*/


/*
|--------------------------------------------------------------------------
| Safe output helper
|--------------------------------------------------------------------------
*/

function artist_e(?string $value): string
{
    return htmlspecialchars(
        $value ?? '',
        ENT_QUOTES,
        'UTF-8'
    );
}


/*
|--------------------------------------------------------------------------
| Artist data
|--------------------------------------------------------------------------
*/

$artistName =
    trim(
        (string) (
            $artist['artist_name']
            ?? ''
        )
    );

$stageName =
    trim(
        (string) (
            $artist['stage_name']
            ?? ''
        )
    );

$bio =
    trim(
        (string) (
            $artist['bio']
            ?? ''
        )
    );

$genre =
    trim(
        (string) (
            $artist['genre']
            ?? 'Nasyid'
        )
    );

$subgenre =
    trim(
        (string) (
            $artist['subgenre']
            ?? ''
        )
    );

$profileImage =
    trim(
        (string) (
            $artist['profile_image']
            ?? ''
        )
    );


/*
|--------------------------------------------------------------------------
| Platform URLs
|--------------------------------------------------------------------------
*/

$platforms = [

    'spotify' => [
        'label' => 'Spotify',
        'url' =>
            trim(
                (string) (
                    $artist['spotify_url']
                    ?? ''
                )
            ),
    ],

    'apple_music' => [
        'label' => 'Apple Music',
        'url' =>
            trim(
                (string) (
                    $artist['apple_music_url']
                    ?? ''
                )
            ),
    ],

    'youtube' => [
        'label' => 'YouTube',
        'url' =>
            trim(
                (string) (
                    $artist['youtube_url']
                    ?? ''
                )
            ),
    ],

    'tiktok' => [
        'label' => 'TikTok',
        'url' =>
            trim(
                (string) (
                    $artist['tiktok_url']
                    ?? ''
                )
            ),
    ],

    'instagram' => [
        'label' => 'Instagram',
        'url' =>
            trim(
                (string) (
                    $artist['instagram_url']
                    ?? ''
                )
            ),
    ],

    'facebook' => [
        'label' => 'Facebook',
        'url' =>
            trim(
                (string) (
                    $artist['facebook_url']
                    ?? ''
                )
            ),
    ],

];


/*
|--------------------------------------------------------------------------
| Platform statistics
|--------------------------------------------------------------------------
*/

$connectedPlatforms = 0;

foreach ($platforms as $platform) {

    if (
        !empty(
            $platform['url']
        )
    ) {

        $connectedPlatforms++;
    }
}

$totalPlatforms =
    count($platforms);

$missingPlatforms =
    $totalPlatforms -
    $connectedPlatforms;


/*
|--------------------------------------------------------------------------
| Initial readiness score
|--------------------------------------------------------------------------
|
| IMPORTANT:
| This is NOT the final NasyidOS intelligence score.
|
| It is only a basic profile completeness indicator.
|
| The real audit engine will be introduced later.
|
*/

$profileChecks = 0;
$profileTotal = 6;

if ($artistName !== '') {
    $profileChecks++;
}

if ($stageName !== '') {
    $profileChecks++;
}

if ($bio !== '') {
    $profileChecks++;
}

if ($genre !== '') {
    $profileChecks++;
}

if ($subgenre !== '') {
    $profileChecks++;
}

if ($connectedPlatforms > 0) {
    $profileChecks++;
}

$readinessScore =
    (int) round(
        (
            $profileChecks /
            $profileTotal
        ) *
        100
    );


/*
|--------------------------------------------------------------------------
| Score label
|--------------------------------------------------------------------------
*/

if ($readinessScore >= 80) {

    $readinessLabel =
        'Strong';

} elseif ($readinessScore >= 60) {

    $readinessLabel =
        'Good';

} elseif ($readinessScore >= 40) {

    $readinessLabel =
        'Needs Work';

} else {

    $readinessLabel =
        'Getting Started';
}


/*
|--------------------------------------------------------------------------
| Initials
|--------------------------------------------------------------------------
*/

$initials = 'N';

if ($artistName !== '') {

    $words =
        preg_split(
            '/\s+/',
            $artistName
        );

    if (
        is_array($words) &&
        count($words) >= 2
    ) {

        $initials =
            mb_strtoupper(
                mb_substr(
                    (string) $words[0],
                    0,
                    1
                ) .
                mb_substr(
                    (string) $words[1],
                    0,
                    1
                )
            );

    } else {

        $initials =
            mb_strtoupper(
                mb_substr(
                    $artistName,
                    0,
                    1
                )
            );
    }
}

?>
<!DOCTYPE html>
<html
    lang="en"
>
<head>

    <meta
        charset="UTF-8"
    >

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        <?= artist_e($artistName) ?> —
        NasyidOS
    </title>

    <meta
        name="description"
        content="NasyidOS artist digital presence profile."
    >

    <link
        rel="stylesheet"
        href="/assets/css/artist-detail.css"
    >

</head>

<body>

<main class="artist-page">

    <!-- =====================================================
         TOP NAV
         ===================================================== -->

    <header class="artist-topbar">

        <a
            href="?page=artists"
            class="back-link"
        >
            <span
                class="back-icon"
                aria-hidden="true"
            >
                ←
            </span>

            <span>
                My Artists
            </span>
        </a>


        <div class="topbar-actions">

            <a
                href="?page=artist-edit&id=<?= (int) $artist['id'] ?>"
                class="button button-dark"
            >
                Edit Artist
            </a>

        </div>

    </header>


    <!-- =====================================================
         HERO
         ===================================================== -->

    <section class="artist-hero">

        <div class="artist-hero-main">

            <div class="artist-avatar">

                <?php if ($profileImage !== ''): ?>

                    <img
                        src="<?= artist_e($profileImage) ?>"
                        alt="<?= artist_e($artistName) ?>"
                    >

                <?php else: ?>

                    <span>
                        <?= artist_e($initials) ?>
                    </span>

                <?php endif; ?>

            </div>


            <div class="artist-heading">

                <div class="eyebrow">
                    ARTIST PROFILE
                </div>

                <h1>
                    <?= artist_e($artistName) ?>
                </h1>


                <?php if ($stageName !== ''): ?>

                    <p class="stage-name">
                        <?= artist_e($stageName) ?>
                    </p>

                <?php endif; ?>


                <div class="artist-tags">

                    <?php if ($genre !== ''): ?>

                        <span class="tag">
                            <?= artist_e($genre) ?>
                        </span>

                    <?php endif; ?>


                    <?php if ($subgenre !== ''): ?>

                        <span class="tag">
                            <?= artist_e($subgenre) ?>
                        </span>

                    <?php endif; ?>

                </div>

            </div>

        </div>


        <div class="hero-status">

            <span class="status-dot"></span>

            <span>
                ACTIVE
            </span>

        </div>

    </section>


    <!-- =====================================================
         OVERVIEW
         ===================================================== -->

    <section class="overview-grid">

        <article class="score-card">

            <div class="card-label">
                DIGITAL PRESENCE
            </div>

            <div class="score-row">

                <div class="score-number">
                    <?= $readinessScore ?>
                </div>

                <div class="score-denominator">
                    /100
                </div>

            </div>

            <div class="score-label">
                <?= artist_e($readinessLabel) ?>
            </div>

            <div class="score-bar">

                <span
    class="
        score-fill
        <?php
        if ($readinessScore >= 80) {
            echo 'score-high';
        } elseif ($readinessScore >= 60) {
            echo 'score-medium';
        } elseif ($readinessScore >= 40) {
            echo 'score-low';
        } else {
            echo 'score-start';
        }
        ?>
    "
></span>

            </div>

            <p class="score-note">
                Initial profile completeness indicator.
                Full NasyidOS audit scoring will be introduced
                in the audit engine.
            </p>

        </article>


        <article class="metric-card">

            <div class="metric-icon">
                ✓
            </div>

            <div>

                <div class="card-label">
                    CONNECTED PLATFORMS
                </div>

                <div class="metric-number">
                    <?= $connectedPlatforms ?>
                    <span>
                        / <?= $totalPlatforms ?>
                    </span>
                </div>

                <p>
                    Digital profiles connected
                </p>

            </div>

        </article>


        <article class="metric-card">

            <div class="metric-icon metric-icon-muted">
                !
            </div>

            <div>

                <div class="card-label">
                    ACTION ITEMS
                </div>

                <div class="metric-number">
                    <?= $missingPlatforms ?>
                </div>

                <p>
                    Platform profiles missing
                </p>

            </div>

        </article>

    </section>


    <!-- =====================================================
         MAIN CONTENT
         ===================================================== -->

    <section class="content-grid">

        <!-- =================================================
             LEFT COLUMN
             ================================================= -->

        <div class="content-main">


            <!-- =============================================
                 ABOUT
                 ============================================= -->

            <article class="panel">

                <div class="panel-header">

                    <div>

                        <div class="panel-kicker">
                            PROFILE
                        </div>

                        <h2>
                            About the Artist
                        </h2>

                    </div>

                </div>


                <?php if ($bio !== ''): ?>

                    <div class="bio">
                        <?= nl2br(
                            artist_e($bio)
                        ) ?>
                    </div>

                <?php else: ?>

                    <div class="empty-content">

                        <div class="empty-icon">
                            +
                        </div>

                        <h3>
                            Bio belum ditambah
                        </h3>

                        <p>
                            Add a clear artist bio to strengthen
                            the artist's digital identity.
                        </p>

                        <a
                            href="?page=artist-edit&id=<?= (int) $artist['id'] ?>"
                            class="button button-light"
                        >
                            Add Bio
                        </a>

                    </div>

                <?php endif; ?>

            </article>


            <!-- =============================================
                 PLATFORM PRESENCE
                 ============================================= -->

            <article class="panel">

                <div class="panel-header">

                    <div>

                        <div class="panel-kicker">
                            DIGITAL ECOSYSTEM
                        </div>

                        <h2>
                            Platform Presence
                        </h2>

                        <p>
                            Connected digital profiles for this artist.
                        </p>

                    </div>

                </div>


                <div class="platform-list">

                    <?php foreach (
                        $platforms as $key => $platform
                    ): ?>

                        <?php
                        $isConnected =
                            !empty(
                                $platform['url']
                            );
                        ?>

                        <div
                            class="platform-item <?= $isConnected
                                ? 'is-connected'
                                : 'is-missing' ?>"
                        >

                            <div class="platform-icon">
                                <?= artist_e(
                                    mb_strtoupper(
                                        mb_substr(
                                            $platform['label'],
                                            0,
                                            1
                                        )
                                    )
                                ) ?>
                            </div>


                            <div class="platform-info">

                                <strong>
                                    <?= artist_e(
                                        $platform['label']
                                    ) ?>
                                </strong>

                                <?php if ($isConnected): ?>

                                    <span class="platform-state">
                                        Connected
                                    </span>

                                <?php else: ?>

                                    <span class="platform-state">
                                        Not connected
                                    </span>

                                <?php endif; ?>

                            </div>


                            <div class="platform-action">

                                <?php if ($isConnected): ?>

                                    <a
                                        href="<?= artist_e(
                                            $platform['url']
                                        ) ?>"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="text-button"
                                    >
                                        Open
                                    </a>

                                <?php else: ?>

                                    <a
                                        href="?page=artist-edit&id=<?= (int) $artist['id'] ?>"
                                        class="text-button"
                                    >
                                        Add
                                    </a>

                                <?php endif; ?>

                            </div>

                        </div>

                    <?php endforeach; ?>

                </div>

            </article>


            <!-- =============================================
                 NEXT PHASE
                 ============================================= -->

            <article class="panel audit-preview-panel">

                <div class="panel-header">

                    <div>

                        <div class="panel-kicker">
                            NasyidOS INTELLIGENCE
                        </div>

                        <h2>
                            Digital Audit
                        </h2>

                        <p>
                            The next layer will analyse how ready
                            this artist is for digital music discovery.
                        </p>

                    </div>

                    <span class="coming-badge">
                        V1.0
                    </span>

                </div>


                <div class="audit-preview-grid">

                    <div class="audit-preview-item">

                        <span class="preview-number">
                            01
                        </span>

                        <strong>
                            Spotify
                        </strong>

                        <span>
                            Artist profile & discovery signals
                        </span>

                    </div>


                    <div class="audit-preview-item">

                        <span class="preview-number">
                            02
                        </span>

                        <strong>
                            YouTube
                        </strong>

                        <span>
                            Channel & content optimisation
                        </span>

                    </div>


                    <div class="audit-preview-item">

                        <span class="preview-number">
                            03
                        </span>

                        <strong>
                            Short-form
                        </strong>

                        <span>
                            TikTok, Reels & Shorts readiness
                        </span>

                    </div>


                    <div class="audit-preview-item">

                        <span class="preview-number">
                            04
                        </span>

                        <strong>
                            Release
                        </strong>

                        <span>
                            Release campaign optimisation
                        </span>

                    </div>

                </div>

            </article>


        </div>


        <!-- =================================================
             RIGHT COLUMN
             ================================================= -->

        <aside class="content-sidebar">


            <!-- =============================================
                 PROFILE CHECK
                 ============================================= -->

            <article class="sidebar-card">

                <div class="card-label">
                    PROFILE CHECK
                </div>

                <h3>
                    Profile readiness
                </h3>

                <p class="sidebar-description">
                    Complete the core artist profile before
                    running a full digital audit.
                </p>


                <div class="check-list">

                    <div class="check-item">

                        <span
                            class="<?= $artistName !== ''
                                ? 'check complete'
                                : 'check' ?>"
                        >
                            <?= $artistName !== ''
                                ? '✓'
                                : '—' ?>
                        </span>

                        <span>
                            Artist name
                        </span>

                    </div>


                    <div class="check-item">

                        <span
                            class="<?= $stageName !== ''
                                ? 'check complete'
                                : 'check' ?>"
                        >
                            <?= $stageName !== ''
                                ? '✓'
                                : '—' ?>
                        </span>

                        <span>
                            Stage name
                        </span>

                    </div>


                    <div class="check-item">

                        <span
                            class="<?= $bio !== ''
                                ? 'check complete'
                                : 'check' ?>"
                        >
                            <?= $bio !== ''
                                ? '✓'
                                : '—' ?>
                        </span>

                        <span>
                            Artist bio
                        </span>

                    </div>


                    <div class="check-item">

                        <span
                            class="<?= $genre !== ''
                                ? 'check complete'
                                : 'check' ?>"
                        >
                            <?= $genre !== ''
                                ? '✓'
                                : '—' ?>
                        </span>

                        <span>
                            Genre
                        </span>

                    </div>


                    <div class="check-item">

                        <span
                            class="<?= $subgenre !== ''
                                ? 'check complete'
                                : 'check' ?>"
                        >
                            <?= $subgenre !== ''
                                ? '✓'
                                : '—' ?>
                        </span>

                        <span>
                            Subgenre
                        </span>

                    </div>


                    <div class="check-item">

                        <span
                            class="<?= $connectedPlatforms > 0
                                ? 'check complete'
                                : 'check' ?>"
                        >
                            <?= $connectedPlatforms > 0
                                ? '✓'
                                : '—' ?>
                        </span>

                        <span>
                            Digital platform
                        </span>

                    </div>

                </div>


                <a
                    href="?page=artist-edit&id=<?= (int) $artist['id'] ?>"
                    class="button button-dark button-full"
                >
                    Complete Profile
                </a>

            </article>


            <!-- =============================================
                 QUICK ACTIONS
                 ============================================= -->

            <article class="sidebar-card">

                <div class="card-label">
                    QUICK ACTIONS
                </div>

                <div class="quick-actions">

                    <a
                        href="?page=artist-edit&id=<?= (int) $artist['id'] ?>"
                        class="quick-action"
                    >

                        <span>
                            Edit artist profile
                        </span>

                        <span>
                            →
                        </span>

                    </a>


                    <a
                        href="?page=artists"
                        class="quick-action"
                    >

                        <span>
                            Back to My Artists
                        </span>

                        <span>
                            →
                        </span>

                    </a>

                </div>

            </article>


        </aside>

    </section>


    <!-- =====================================================
         FOOTER
         ===================================================== -->

    <footer class="artist-footer">

        <span>
            NasyidOS
        </span>

        <span>
            Powered by Sarang Seni Studio
        </span>

    </footer>

</main>

</body>
</html>