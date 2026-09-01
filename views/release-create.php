<?php

declare(strict_types=1);

$release = [
    'id' => 0,
    'artist_id' => '',
    'title' => '',
    'release_type' => 'single',
    'release_status' => 'draft',
    'genre' => '',
    'language' => '',
    'release_date' => '',
    'upc' => '',
    'distributor' => '',
    'is_explicit' => 0,
    'cover_art_url' => '',
    'short_hook' => '',
    'pitch' => '',
    'internal_notes' => '',
];

if (!empty($old ?? [])) {
    $release = array_merge($release, $old);
}

$releaseType =
    (string) ($release['release_type'] ?? 'single');

$releaseStatus =
    (string) ($release['release_status'] ?? 'draft');

function release_form_e(mixed $value): string
{
    return Security::escape($value);
}

$releaseType =
    (string) ($release['release_type'] ?? 'single');

$releaseStatus =
    (string) ($release['release_status'] ?? 'draft');

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
        Create Release · NasyidOS
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
                Workspace / Releases / Create
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


        <section class="release-page-header">

            <div class="release-eyebrow">
                RELEASE EDITOR
            </div>

            <h1>
                Create Release
            </h1>

            <p>
                Update release metadata. Track,
                platform and audit workflows are
                handled separately.
            </p>

        </section>


        <?php if ($errors): ?>

            <div class="release-form-errors">

                <strong>
                    Sila semak perkara berikut:
                </strong>

                <ul>
                    <?php foreach ($errors as $error): ?>

                        <li>
                            <?= release_form_e($error) ?>
                        </li>

                    <?php endforeach; ?>
                </ul>

            </div>

        <?php endif; ?>


        <form
            method="post"
            action="?page=release-create"
            class="release-form"
        >

            <input
                type="hidden"
                name="csrf_token"
                value="<?= release_form_e(
                    Security::csrfToken()
                ) ?>"
            >


            <section class="release-form-card">

                <div class="release-form-card-head">

                    <div>
                        <div class="release-eyebrow">
                            01
                        </div>

                        <h2>
                            Core Metadata
                        </h2>
                    </div>

                </div>


                <div class="release-form-grid">

                    <div class="release-field full">

                        <label for="artist_id">
                            Artist
                        </label>

                        <select
                            id="artist_id"
                            name="artist_id"
                            required
                        >

                            <option value="">
                                Select artist
                            </option>

                            <?php foreach ($artists as $artist): ?>

                                <?php
                                $artistId =
                                    (int) $artist['id'];

                                $artistLabel =
                                    trim(
                                        (string) (
                                            $artist['artist_name']
                                            ?? ''
                                        )
                                    );

                                if (
                                    !empty(
                                        $artist['stage_name']
                                    )
                                ) {
                                    $artistLabel .=
                                        ' · ' .
                                        trim(
                                            (string) (
                                                $artist['stage_name']
                                            )
                                        );
                                }
                                ?>

                                <option
                                    value="<?= $artistId ?>"
                                    <?= (int) (
                                        $release['artist_id']
                                        ?? 0
                                    ) === $artistId
                                        ? 'selected'
                                        : '' ?>
                                >
                                    <?= release_form_e(
                                        $artistLabel
                                    ) ?>
                                </option>

                            <?php endforeach; ?>

                        </select>

                        <span class="release-field-help">
                            Release mesti dimiliki oleh artist
                            yang juga dimiliki oleh user ini.
                        </span>

                    </div>


                    <div class="release-field full">

                        <label for="title">
                            Release Title
                        </label>

                        <input
                            type="text"
                            id="title"
                            name="title"
                            maxlength="200"
                            required
                            value="<?= release_form_e(
                                $release['title']
                                ?? ''
                            ) ?>"
                        >

                    </div>


                    <div class="release-field">

                        <label for="release_type">
                            Release Type
                        </label>

                        <select
                            id="release_type"
                            name="release_type"
                            required
                        >

                            <?php
                            $types = [
                                'single' => 'Single',
                                'ep' => 'EP',
                                'album' => 'Album',
                                'compilation' =>
                                    'Compilation',
                            ];
                            ?>

                            <?php foreach (
                                $types as $value => $label
                            ): ?>

                                <option
                                    value="<?= release_form_e(
                                        $value
                                    ) ?>"
                                    <?= $releaseType === $value
                                        ? 'selected'
                                        : '' ?>
                                >
                                    <?= release_form_e(
                                        $label
                                    ) ?>
                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>


                    <div class="release-field">

                        <label for="release_status">
                            Release Status
                        </label>

                        <select
                            id="release_status"
                            name="release_status"
                            required
                        >

                            <?php
                            $statuses = [
                                'draft' => 'Draft',
                                'planned' => 'Planned',
                                'released' => 'Released',
                                'archived' => 'Archived',
                            ];
                            ?>

                            <?php foreach (
                                $statuses as $value => $label
                            ): ?>

                                <option
                                    value="<?= release_form_e(
                                        $value
                                    ) ?>"
                                    <?= $releaseStatus === $value
                                        ? 'selected'
                                        : '' ?>
                                >
                                    <?= release_form_e(
                                        $label
                                    ) ?>
                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>


                    <div class="release-field">

                        <label for="genre">
                            Genre
                        </label>

                        <input
                            type="text"
                            id="genre"
                            name="genre"
                            maxlength="120"
                            value="<?= release_form_e(
                                $release['genre']
                                ?? ''
                            ) ?>"
                        >

                    </div>


                    <div class="release-field">

                        <label for="language">
                            Language
                        </label>

                        <input
                            type="text"
                            id="language"
                            name="language"
                            maxlength="80"
                            value="<?= release_form_e(
                                $release['language']
                                ?? ''
                            ) ?>"
                        >

                    </div>


                    <div class="release-field">

                        <label for="release_date">
                            Release Date
                        </label>

                        <input
                            type="date"
                            id="release_date"
                            name="release_date"
                            value="<?= release_form_e(
                                $release['release_date']
                                ?? ''
                            ) ?>"
                        >

                    </div>


                    <div class="release-field">

                        <label for="upc">
                            UPC
                        </label>

                        <input
                            type="text"
                            id="upc"
                            name="upc"
                            maxlength="50"
                            value="<?= release_form_e(
                                $release['upc']
                                ?? ''
                            ) ?>"
                        >

                    </div>


                    <div class="release-field">

                        <label for="distributor">
                            Distributor
                        </label>

                        <input
                            type="text"
                            id="distributor"
                            name="distributor"
                            maxlength="150"
                            value="<?= release_form_e(
                                $release['distributor']
                                ?? ''
                            ) ?>"
                        >

                    </div>


                    <div class="release-field full">

                        <label
                            class="release-checkbox"
                        >

                            <input
                                type="checkbox"
                                name="is_explicit"
                                value="1"
                                <?= !empty(
                                    $release['is_explicit']
                                )
                                    ? 'checked'
                                    : '' ?>
                            >

                            <span>
                                This release contains
                                explicit content.
                            </span>

                        </label>

                    </div>

                </div>

            </section>


            <section class="release-form-card">

                <div class="release-form-card-head">

                    <div>
                        <div class="release-eyebrow">
                            02
                        </div>

                        <h2>
                            Distribution
                        </h2>
                    </div>

                </div>


                <div class="release-form-grid">

                    <div class="release-field full">

                        <label for="cover_art_url">
                            Cover Art URL
                        </label>

                        <input
                            type="url"
                            id="cover_art_url"
                            name="cover_art_url"
                            maxlength="500"
                            value="<?= release_form_e(
                                $release['cover_art_url']
                                ?? ''
                            ) ?>"
                            placeholder="https://..."
                        >

                    </div>

                </div>

            </section>


            <section class="release-form-card">

                <div class="release-form-card-head">

                    <div>
                        <div class="release-eyebrow">
                            03
                        </div>

                        <h2>
                            Positioning
                        </h2>
                    </div>

                </div>


                <div class="release-form-grid">

                    <div class="release-field full">

                        <label for="short_hook">
                            Short Hook
                        </label>

                        <textarea
                            id="short_hook"
                            name="short_hook"
                            rows="4"
                            maxlength="5000"
                        ><?= release_form_e(
                            $release['short_hook']
                            ?? ''
                        ) ?></textarea>

                    </div>


                    <div class="release-field full">

                        <label for="pitch">
                            Release Pitch
                        </label>

                        <textarea
                            id="pitch"
                            name="pitch"
                            rows="8"
                            maxlength="10000"
                        ><?= release_form_e(
                            $release['pitch']
                            ?? ''
                        ) ?></textarea>

                    </div>


                    <div class="release-field full">

                        <label for="internal_notes">
                            Internal Notes
                        </label>

                        <textarea
                            id="internal_notes"
                            name="internal_notes"
                            rows="6"
                            maxlength="10000"
                        ><?= release_form_e(
                            $release['internal_notes']
                            ?? ''
                        ) ?></textarea>

                    </div>

                </div>

            </section>


            <div class="release-form-actions">

                <a
                    href="?page=releases"
                    class="release-button secondary"
                >
                    Cancel
                </a>

                <button
                    type="submit"
                    class="release-button primary"
                >
                    Save Release
                </button>

            </div>

        </form>

    </main>

</div>

</body>
</html>
