<?php

declare(strict_types=1);


/*
|--------------------------------------------------------------------------
| Track Edit View
|--------------------------------------------------------------------------
*/

$trackId =
    (int) (
        $track['id']
        ?? 0
    );


$releaseId =
    (int) (
        $track['release_id']
        ?? $release['id']
        ?? 0
    );


$releaseTitle =
    (string) (
        $release['title']
        ?? 'Release'
    );


$artistName =
    (string) (
        $release['artist_name']
        ?? ''
    );


$trackNumber =
    (string) (
        $track['track_number']
        ?? ''
    );


$discNumber =
    (string) (
        $track['disc_number']
        ?? 1
    );


$trackTitle =
    (string) (
        $track['track_title']
        ?? ''
    );


$versionLabel =
    (string) (
        $track['version_label']
        ?? ''
    );


$isrc =
    (string) (
        $track['isrc']
        ?? ''
    );


$durationSeconds =
    (string) (
        $track['duration_seconds']
        ?? ''
    );


$language =
    (string) (
        $track['language']
        ?? ''
    );


$lyrics =
    (string) (
        $track['lyrics']
        ?? ''
    );


$lyricsStatus =
    (string) (
        $track['lyrics_status']
        ?? 'missing'
    );


$audioStatus =
    (string) (
        $track['audio_status']
        ?? 'missing'
    );


$isExplicit =
    (int) (
        $track['is_explicit']
        ?? 0
    );


$featuringArtists =
    (string) (
        $track['featuring_artists']
        ?? ''
    );


$composers =
    (string) (
        $track['composers']
        ?? ''
    );


$lyricists =
    (string) (
        $track['lyricists']
        ?? ''
    );


$producers =
    (string) (
        $track['producers']
        ?? ''
    );


$notes =
    (string) (
        $track['notes']
        ?? ''
    );


$errors =
    isset($errors) &&
    is_array($errors)
        ? $errors
        : [];

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
        Edit Track · NasyidOS
    </title>

    <link
        rel="stylesheet"
        href="assets/css/app.css"
    >

    <link
        rel="stylesheet"
        href="assets/css/releases.css"
    >

    <link
        rel="stylesheet"
        href="assets/css/track-edit.css"
    >

</head>

<body>

<div class="track-page">

    <header class="track-topbar">

        <div class="track-topbar-inner">

            <a
                href="?page=dashboard"
                class="track-brand"
            >
                Nasyid<span>OS</span>
            </a>


            <nav class="track-nav">

                <a
                    href="?page=releases"
                >
                    My Releases
                </a>

                <a
                    href="?page=artists"
                >
                    My Artists
                </a>

                <a
                    href="?page=logout"
                >
                    Logout
                </a>

            </nav>

        </div>

    </header>


    <main class="track-main">

        <div class="track-breadcrumb">

            <a href="?page=dashboard">
                Workspace
            </a>

            <span>/</span>

            <a href="?page=releases">
                Releases
            </a>

            <span>/</span>

            <a href="?page=release&id=<?= $releaseId ?>">
                <?= Security::escape(
                    $releaseTitle
                ) ?>
            </a>

            <span>/</span>

            <span>
                Edit Track
            </span>

        </div>


        <a
            href="?page=release&id=<?= $releaseId ?>"
            class="track-back-link"
        >
            ← Back to Release
        </a>


        <section class="track-header">

            <div>

                <div class="track-eyebrow">
                    TRACK EDITOR
                </div>

                <h1>
                    Edit Track
                </h1>

                <p>
                    Update the metadata and optimisation
                    information for this track.
                </p>

            </div>


            <div class="track-header-badge">

                <span>
                    Track
                </span>

                <strong>
                    <?= Security::escape(
                        $discNumber .
                        '.' .
                        $trackNumber
                    ) ?>
                </strong>

            </div>

        </section>


        <section class="track-release-card">

            <div class="track-release-card-label">
                RELEASE
            </div>

            <div class="track-release-card-content">

                <div>

                    <h2>
                        <?= Security::escape(
                            $releaseTitle
                        ) ?>
                    </h2>

                    <?php if ($artistName !== ''): ?>

                        <p>
                            <?= Security::escape(
                                $artistName
                            ) ?>
                        </p>

                    <?php endif; ?>

                </div>

                <a
                    href="?page=release&id=<?= $releaseId ?>"
                    class="track-small-link"
                >
                    View Release →
                </a>

            </div>

        </section>


        <?php if ($errors): ?>

            <section class="track-error">

                <div class="track-error-title">
                    Unable to save changes
                </div>

                <ul>

                    <?php foreach ($errors as $error): ?>

                        <li>
                            <?= Security::escape(
                                $error
                            ) ?>
                        </li>

                    <?php endforeach; ?>

                </ul>

            </section>

        <?php endif; ?>


        <form
            method="post"
            action="?page=track-edit&id=<?= $trackId ?>&release_id=<?= $releaseId ?>"
            class="track-form"
        >

            <input
                type="hidden"
                name="csrf_token"
                value="<?= Security::escape(
                    Security::csrfToken()
                ) ?>"
            >

            <input
                type="hidden"
                name="id"
                value="<?= $trackId ?>"
            >

            <input
                type="hidden"
                name="release_id"
                value="<?= $releaseId ?>"
            >


            <!-- =========================================================
                 CORE METADATA
                 ========================================================= -->

            <section class="track-card">

                <div class="track-card-header">

                    <div>

                        <span class="track-section-number">
                            01
                        </span>

                        <h2>
                            Track Identity
                        </h2>

                    </div>

                </div>


                <div class="track-grid">


                    <div class="track-field">

                        <label
                            for="disc_number"
                        >
                            Disc Number
                        </label>

                        <input
                            type="number"
                            id="disc_number"
                            name="disc_number"
                            min="1"
                            max="999"
                            required
                            value="<?= Security::escape(
                                $discNumber
                            ) ?>"
                        >

                    </div>


                    <div class="track-field">

                        <label
                            for="track_number"
                        >
                            Track Number
                        </label>

                        <input
                            type="number"
                            id="track_number"
                            name="track_number"
                            min="1"
                            max="999"
                            required
                            value="<?= Security::escape(
                                $trackNumber
                            ) ?>"
                        >

                    </div>


                    <div class="track-field track-field-full">

                        <label
                            for="track_title"
                        >
                            Track Title
                        </label>

                        <input
                            type="text"
                            id="track_title"
                            name="track_title"
                            maxlength="255"
                            required
                            value="<?= Security::escape(
                                $trackTitle
                            ) ?>"
                        >

                    </div>


                    <div class="track-field">

                        <label
                            for="version_label"
                        >
                            Version Label
                        </label>

                        <input
                            type="text"
                            id="version_label"
                            name="version_label"
                            maxlength="100"
                            value="<?= Security::escape(
                                $versionLabel
                            ) ?>"
                            placeholder="Original"
                        >

                    </div>


                    <div class="track-field">

                        <label
                            for="isrc"
                        >
                            ISRC
                        </label>

                        <input
                            type="text"
                            id="isrc"
                            name="isrc"
                            maxlength="20"
                            value="<?= Security::escape(
                                $isrc
                            ) ?>"
                            placeholder="MYXXX2500001"
                        >

                    </div>


                    <div class="track-field">

                        <label
                            for="duration_seconds"
                        >
                            Duration
                        </label>

                        <input
                            type="number"
                            id="duration_seconds"
                            name="duration_seconds"
                            min="0"
                            max="86400"
                            value="<?= Security::escape(
                                $durationSeconds
                            ) ?>"
                            placeholder="240"
                        >

                        <span class="track-help">
                            Duration dalam saat.
                        </span>

                    </div>


                    <div class="track-field">

                        <label
                            for="language"
                        >
                            Language
                        </label>

                        <input
                            type="text"
                            id="language"
                            name="language"
                            maxlength="80"
                            value="<?= Security::escape(
                                $language
                            ) ?>"
                            placeholder="Malay"
                        >

                    </div>


                    <div class="track-field track-field-full">

                        <label class="track-checkbox">

                            <input
                                type="checkbox"
                                name="is_explicit"
                                value="1"
                                <?= $isExplicit === 1
                                    ? 'checked'
                                    : '' ?>
                            >

                            <span>
                                This track contains explicit content.
                            </span>

                        </label>

                    </div>


                </div>

            </section>


            <!-- =========================================================
                 LYRICS
                 ========================================================= -->

            <section class="track-card">

                <div class="track-card-header">

                    <div>

                        <span class="track-section-number">
                            02
                        </span>

                        <h2>
                            Lyrics
                        </h2>

                    </div>

                </div>


                <div class="track-grid">


                    <div class="track-field">

                        <label
                            for="lyrics_status"
                        >
                            Lyrics Status
                        </label>

                        <select
                            id="lyrics_status"
                            name="lyrics_status"
                        >

                            <option
                                value="missing"
                                <?= $lyricsStatus === 'missing'
                                    ? 'selected'
                                    : '' ?>
                            >
                                Missing
                            </option>

                            <option
                                value="draft"
                                <?= $lyricsStatus === 'draft'
                                    ? 'selected'
                                    : '' ?>
                            >
                                Draft
                            </option>

                            <option
                                value="complete"
                                <?= $lyricsStatus === 'complete'
                                    ? 'selected'
                                    : '' ?>
                            >
                                Complete
                            </option>

                        </select>

                    </div>


                    <div class="track-field">

                        <label>
                            Optimisation Note
                        </label>

                        <span class="track-static-note">
                            Complete lyrics improve the
                            readiness of the release metadata.
                        </span>

                    </div>


                    <div class="track-field track-field-full">

                        <label
                            for="lyrics"
                        >
                            Lyrics
                        </label>

                        <textarea
                            id="lyrics"
                            name="lyrics"
                            rows="14"
                            maxlength="30000"
                            placeholder="Enter lyrics..."
                        ><?= Security::escape(
                            $lyrics
                        ) ?></textarea>

                    </div>


                </div>

            </section>


            <!-- =========================================================
                 AUDIO
                 ========================================================= -->

            <section class="track-card">

                <div class="track-card-header">

                    <div>

                        <span class="track-section-number">
                            03
                        </span>

                        <h2>
                            Audio Readiness
                        </h2>

                    </div>

                </div>


                <div class="track-grid">


                    <div class="track-field">

                        <label
                            for="audio_status"
                        >
                            Audio Status
                        </label>

                        <select
                            id="audio_status"
                            name="audio_status"
                        >

                            <option
                                value="missing"
                                <?= $audioStatus === 'missing'
                                    ? 'selected'
                                    : '' ?>
                            >
                                Missing
                            </option>

                            <option
                                value="draft"
                                <?= $audioStatus === 'draft'
                                    ? 'selected'
                                    : '' ?>
                            >
                                Draft
                            </option>

                            <option
                                value="ready"
                                <?= $audioStatus === 'ready'
                                    ? 'selected'
                                    : '' ?>
                            >
                                Ready
                            </option>

                        </select>

                    </div>


                    <div class="track-field">

                        <label>
                            Readiness
                        </label>

                        <span class="track-static-note">
                            Track audio preparation status.
                        </span>

                    </div>


                </div>

            </section>


            <!-- =========================================================
                 CONTRIBUTORS
                 ========================================================= -->

            <section class="track-card">

                <div class="track-card-header">

                    <div>

                        <span class="track-section-number">
                            04
                        </span>

                        <h2>
                            Contributors
                        </h2>

                    </div>

                </div>


                <div class="track-grid">


                    <div class="track-field track-field-full">

                        <label
                            for="featuring_artists"
                        >
                            Featuring Artists
                        </label>

                        <input
                            type="text"
                            id="featuring_artists"
                            name="featuring_artists"
                            maxlength="1000"
                            value="<?= Security::escape(
                                $featuringArtists
                            ) ?>"
                            placeholder="Artist A, Artist B"
                        >

                    </div>


                    <div class="track-field">

                        <label
                            for="composers"
                        >
                            Composers
                        </label>

                        <textarea
                            id="composers"
                            name="composers"
                            rows="5"
                            maxlength="2000"
                        ><?= Security::escape(
                            $composers
                        ) ?></textarea>

                    </div>


                    <div class="track-field">

                        <label
                            for="lyricists"
                        >
                            Lyricists
                        </label>

                        <textarea
                            id="lyricists"
                            name="lyricists"
                            rows="5"
                            maxlength="2000"
                        ><?= Security::escape(
                            $lyricists
                        ) ?></textarea>

                    </div>


                    <div class="track-field track-field-full">

                        <label
                            for="producers"
                        >
                            Producers
                        </label>

                        <textarea
                            id="producers"
                            name="producers"
                            rows="5"
                            maxlength="2000"
                        ><?= Security::escape(
                            $producers
                        ) ?></textarea>

                    </div>


                </div>

            </section>


            <!-- =========================================================
                 NOTES
                 ========================================================= -->

            <section class="track-card">

                <div class="track-card-header">

                    <div>

                        <span class="track-section-number">
                            05
                        </span>

                        <h2>
                            Internal Notes
                        </h2>

                    </div>

                </div>


                <div class="track-field">

                    <label
                        for="notes"
                    >
                        Notes
                    </label>

                    <textarea
                        id="notes"
                        name="notes"
                        rows="7"
                        maxlength="10000"
                        placeholder="Internal notes..."
                    ><?= Security::escape(
                        $notes
                    ) ?></textarea>

                </div>

            </section>


            <!-- =========================================================
                 SAVE ACTION
                 ========================================================= -->

            <div class="track-actions">

                <a
                    href="?page=release&id=<?= $releaseId ?>"
                    class="track-button track-button-secondary"
                >
                    Cancel
                </a>

                <button
                    type="submit"
                    class="track-button track-button-primary"
                >
                    Save Changes
                </button>

            </div>


        </form>


        <!-- =============================================================
             DANGER ZONE
             ============================================================= -->

        <section class="track-danger-card">

            <div>

                <div class="track-danger-label">
                    DANGER ZONE
                </div>

                <h2>
                    Delete Track
                </h2>

                <p>
                    This action permanently removes this track
                    from the release.
                </p>

            </div>


            <form
                method="post"
                action="?page=track-delete&id=<?= $trackId ?>&release_id=<?= $releaseId ?>"
                onsubmit="return confirm('Are you sure you want to permanently delete this track?');"
            >

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= Security::escape(
                        Security::csrfToken()
                    ) ?>"
                >

                <input
                    type="hidden"
                    name="id"
                    value="<?= $trackId ?>"
                >

                <input
                    type="hidden"
                    name="release_id"
                    value="<?= $releaseId ?>"
                >

                <button
                    type="submit"
                    class="track-button track-button-danger"
                >
                    Delete Track
                </button>

            </form>

        </section>


    </main>

</div>

</body>

</html>