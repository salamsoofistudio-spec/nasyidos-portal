<?php

declare(strict_types=1);

function track_form_e(mixed $value): string
{
    return Security::escape($value);
}

$releaseId = (int) ($release['id'] ?? 0);

$releaseTitle =
    (string) ($release['title'] ?? '');

$artistName =
    (string) (
        $release['artist_name']
        ?? $release['stage_name']
        ?? ''
    );

$track = is_array($track ?? null)
    ? $track
    : [];

$errors = is_array($errors ?? null)
    ? $errors
    : [];

$trackNumber =
    $track['track_number'] ?? '';

$discNumber =
    $track['disc_number'] ?? 1;

$trackTitle =
    $track['track_title'] ?? '';

$versionLabel =
    $track['version_label'] ?? '';

$isrc =
    $track['isrc'] ?? '';

$durationSeconds =
    $track['duration_seconds'] ?? '';

$language =
    $track['language']
    ?? ($release['language'] ?? '');

$lyrics =
    $track['lyrics'] ?? '';

$lyricsStatus =
    $track['lyrics_status']
    ?? 'not_started';

$audioStatus =
    $track['audio_status']
    ?? 'not_ready';

$isExplicit =
    !empty($track['is_explicit']);

$featuringArtists =
    $track['featuring_artists'] ?? '';

$composers =
    $track['composers'] ?? '';

$lyricists =
    $track['lyricists'] ?? '';

$producers =
    $track['producers'] ?? '';

$notes =
    $track['notes'] ?? '';

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
        Add Track · NasyidOS
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
        href="assets/css/tracks.css"
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
                Workspace / Releases /
                <?= track_form_e($releaseTitle) ?> /
                Add Track
            </div>

        </div>


        <div class="release-topbar-actions">

            <a
                href="?page=release&id=<?= $releaseId ?>"
                class="release-link"
            >
                Back to Release
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
            href="?page=release&id=<?= $releaseId ?>"
            class="release-back"
        >
            ← Back to Release
        </a>


        <section class="release-page-header">

            <div class="release-eyebrow">
                TRACK EDITOR
            </div>

            <h1>
                Add Track
            </h1>

            <p>
                Add structured metadata, lyrics,
                audio readiness and contributor
                information for this track.
            </p>

            <div class="track-release-context">

                <strong>
                    <?= track_form_e($releaseTitle) ?>
                </strong>

                <?php if ($artistName !== ''): ?>

                    <span>
                        <?= track_form_e($artistName) ?>
                    </span>

                <?php endif; ?>

            </div>

        </section>


        <?php if ($errors): ?>

            <div class="release-form-errors">

                <strong>
                    Sila semak perkara berikut:
                </strong>

                <ul>

                    <?php foreach ($errors as $error): ?>

                        <li>
                            <?= track_form_e($error) ?>
                        </li>

                    <?php endforeach; ?>

                </ul>

            </div>

        <?php endif; ?>


        <form
            method="post"
            action="?page=track-create&release_id=<?= $releaseId ?>"
            class="release-form"
        >

            <input
                type="hidden"
                name="csrf_token"
                value="<?= track_form_e(
                    Security::csrfToken()
                ) ?>"
            >


            <!-- =========================================================
                 01 — TRACK IDENTITY
                 ========================================================= -->

            <section class="release-form-card">

                <div class="release-form-card-head">

                    <div>

                        <div class="release-eyebrow">
                            01
                        </div>

                        <h2>
                            Track Identity
                        </h2>

                    </div>

                </div>


                <div class="release-form-grid">

                    <div class="release-field">

                        <label for="disc_number">
                            Disc Number
                        </label>

                        <input
                            type="number"
                            id="disc_number"
                            name="disc_number"
                            min="1"
                            max="999"
                            required
                            value="<?= track_form_e(
                                $discNumber
                            ) ?>"
                        >

                    </div>


                    <div class="release-field">

                        <label for="track_number">
                            Track Number
                        </label>

                        <input
                            type="number"
                            id="track_number"
                            name="track_number"
                            min="1"
                            max="999"
                            required
                            value="<?= track_form_e(
                                $trackNumber
                            ) ?>"
                        >

                        <span class="release-field-help">
                            Nombor track mestilah unik
                            dalam disc yang sama.
                        </span>

                    </div>


                    <div class="release-field full">

                        <label for="track_title">
                            Track Title
                        </label>

                        <input
                            type="text"
                            id="track_title"
                            name="track_title"
                            maxlength="200"
                            required
                            value="<?= track_form_e(
                                $trackTitle
                            ) ?>"
                            placeholder="Contoh: Cinta Ini"
                        >

                    </div>


                    <div class="release-field">

                        <label for="version_label">
                            Version Label
                        </label>

                        <input
                            type="text"
                            id="version_label"
                            name="version_label"
                            maxlength="100"
                            value="<?= track_form_e(
                                $versionLabel
                            ) ?>"
                            placeholder="Original / Acoustic / Live"
                        >

                    </div>


                    <div class="release-field">

                        <label for="isrc">
                            ISRC
                        </label>

                        <input
                            type="text"
                            id="isrc"
                            name="isrc"
                            maxlength="50"
                            value="<?= track_form_e(
                                $isrc
                            ) ?>"
                            placeholder="MY-XXX-25-00001"
                        >

                    </div>


                    <div class="release-field">

                        <label for="duration_seconds">
                            Duration (seconds)
                        </label>

                        <input
                            type="number"
                            id="duration_seconds"
                            name="duration_seconds"
                            min="0"
                            max="86400"
                            value="<?= track_form_e(
                                $durationSeconds
                            ) ?>"
                            placeholder="240"
                        >

                        <span class="release-field-help">
                            Contoh 4 minit = 240 saat.
                        </span>

                    </div>


                    <div class="release-field">

                        <label for="language">
                            Track Language
                        </label>

                        <input
                            type="text"
                            id="language"
                            name="language"
                            maxlength="80"
                            value="<?= track_form_e(
                                $language
                            ) ?>"
                            placeholder="Malay"
                        >

                    </div>


                    <div class="release-field full">

                        <label class="release-checkbox">

                            <input
                                type="checkbox"
                                name="is_explicit"
                                value="1"
                                <?= $isExplicit
                                    ? 'checked'
                                    : '' ?>
                            >

                            <span>
                                This track contains
                                explicit content.
                            </span>

                        </label>

                    </div>

                </div>

            </section>


            <!-- =========================================================
                 02 — LYRICS
                 ========================================================= -->

            <section class="release-form-card">

                <div class="release-form-card-head">

                    <div>

                        <div class="release-eyebrow">
                            02
                        </div>

                        <h2>
                            Lyrics
                        </h2>

                    </div>

                </div>


                <div class="release-form-grid">

                    <div class="release-field">

                        <label for="lyrics_status">
                            Lyrics Status
                        </label>

                        <select
                            id="lyrics_status"
                            name="lyrics_status"
                            required
                        >

                            <?php
                            $lyricsStatuses = [
                                'not_started' =>
                                    'Not Started',

                                'draft' =>
                                    'Draft',

                                'review' =>
                                    'Review',

                                'complete' =>
                                    'Complete',
                            ];
                            ?>

                            <?php foreach (
                                $lyricsStatuses
                                as $value => $label
                            ): ?>

                                <option
                                    value="<?= track_form_e(
                                        $value
                                    ) ?>"
                                    <?= $lyricsStatus === $value
                                        ? 'selected'
                                        : '' ?>
                                >
                                    <?= track_form_e(
                                        $label
                                    ) ?>
                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>


                    <div class="release-field full">

                        <label for="lyrics">
                            Lyrics
                        </label>

                        <textarea
                            id="lyrics"
                            name="lyrics"
                            rows="14"
                            maxlength="30000"
                            placeholder="Masukkan lirik lagu..."
                        ><?= track_form_e(
                            $lyrics
                        ) ?></textarea>

                        <span class="release-field-help">
                            Simpan lirik yang telah mendapat
                            kebenaran/hak penggunaan.
                        </span>

                    </div>

                </div>

            </section>


            <!-- =========================================================
                 03 — AUDIO READINESS
                 ========================================================= -->

            <section class="release-form-card">

                <div class="release-form-card-head">

                    <div>

                        <div class="release-eyebrow">
                            03
                        </div>

                        <h2>
                            Audio Readiness
                        </h2>

                    </div>

                </div>


                <div class="release-form-grid">

                    <div class="release-field">

                        <label for="audio_status">
                            Audio Status
                        </label>

                        <select
                            id="audio_status"
                            name="audio_status"
                            required
                        >

                            <?php
                            $audioStatuses = [
                                'not_ready' =>
                                    'Not Ready',

                                'demo' =>
                                    'Demo',

                                'mastered' =>
                                    'Mastered',

                                'ready' =>
                                    'Ready',
                            ];
                            ?>

                            <?php foreach (
                                $audioStatuses
                                as $value => $label
                            ): ?>

                                <option
                                    value="<?= track_form_e(
                                        $value
                                    ) ?>"
                                    <?= $audioStatus === $value
                                        ? 'selected'
                                        : '' ?>
                                >
                                    <?= track_form_e(
                                        $label
                                    ) ?>
                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>

                </div>

            </section>


            <!-- =========================================================
                 04 — CONTRIBUTORS
                 ========================================================= -->

            <section class="release-form-card">

                <div class="release-form-card-head">

                    <div>

                        <div class="release-eyebrow">
                            04
                        </div>

                        <h2>
                            Contributors
                        </h2>

                    </div>

                </div>


                <div class="release-form-grid">

                    <div class="release-field full">

                        <label for="featuring_artists">
                            Featuring Artists
                        </label>

                        <input
                            type="text"
                            id="featuring_artists"
                            name="featuring_artists"
                            maxlength="1000"
                            value="<?= track_form_e(
                                $featuringArtists
                            ) ?>"
                            placeholder="Artist A, Artist B"
                        >

                    </div>


                    <div class="release-field">

                        <label for="composers">
                            Composers
                        </label>

                        <textarea
                            id="composers"
                            name="composers"
                            rows="4"
                            maxlength="2000"
                            placeholder="Nama composer..."
                        ><?= track_form_e(
                            $composers
                        ) ?></textarea>

                    </div>


                    <div class="release-field">

                        <label for="lyricists">
                            Lyricists
                        </label>

                        <textarea
                            id="lyricists"
                            name="lyricists"
                            rows="4"
                            maxlength="2000"
                            placeholder="Nama lyricist..."
                        ><?= track_form_e(
                            $lyricists
                        ) ?></textarea>

                    </div>


                    <div class="release-field full">

                        <label for="producers">
                            Producers
                        </label>

                        <textarea
                            id="producers"
                            name="producers"
                            rows="4"
                            maxlength="2000"
                            placeholder="Nama producer..."
                        ><?= track_form_e(
                            $producers
                        ) ?></textarea>

                    </div>

                </div>

            </section>


            <!-- =========================================================
                 05 — INTERNAL NOTES
                 ========================================================= -->

            <section class="release-form-card">

                <div class="release-form-card-head">

                    <div>

                        <div class="release-eyebrow">
                            05
                        </div>

                        <h2>
                            Internal Notes
                        </h2>

                    </div>

                </div>


                <div class="release-form-grid">

                    <div class="release-field full">

                        <label for="notes">
                            Notes
                        </label>

                        <textarea
                            id="notes"
                            name="notes"
                            rows="7"
                            maxlength="10000"
                            placeholder="Nota dalaman untuk release..."
                        ><?= track_form_e(
                            $notes
                        ) ?></textarea>

                    </div>

                </div>

            </section>


            <!-- =========================================================
                 ACTIONS
                 ========================================================= -->

            <div class="release-form-actions">

                <a
                    href="?page=release&id=<?= $releaseId ?>"
                    class="release-button secondary"
                >
                    Cancel
                </a>

                <button
                    type="submit"
                    class="release-button primary"
                >
                    Save Track
                </button>

            </div>

        </form>

    </main>

</div>

</body>

</html>