<?php

declare(strict_types=1);

$errors =
    $_SESSION['artist_errors']
    ?? [];

$old =
    $_SESSION['old_artist']
    ?? [];

unset(
    $_SESSION['artist_errors'],
    $_SESSION['old_artist']
);
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
        Add Artist · NasyidOS
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
                    My Artists
                </div>

                <h1 class="page-title">
                    Add Artist
                </h1>

                <p class="page-description">
                    Create an artist profile that NasyidOS
                    can use for future release and
                    platform optimization analysis.
                </p>

            </div>


            <a
                href="?page=artists"
                class="secondary-button"
            >
                ← Back to Artists
            </a>

        </header>


        <?php if ($errors): ?>

            <div class="alert alert-error">

                <ul>

                    <?php foreach (
                        $errors
                        as $error
                    ): ?>

                        <li>

                            <?= Security::escape(
                                $error
                            ) ?>

                        </li>

                    <?php endforeach; ?>

                </ul>

            </div>

        <?php endif; ?>


        <form
            method="POST"
            action="?page=artists-create"
            class="form-card"
        >

            <input
                type="hidden"
                name="csrf_token"
                value="<?= Security::escape(
                    Security::csrfToken()
                ) ?>"
            >


            <!-- =====================================================
                 Artist Identity
                 ===================================================== -->

            <section class="form-section">

                <h2 class="form-section-title">
                    Artist Identity
                </h2>

                <p class="form-section-description">
                    Basic identity information for this artist.
                </p>


                <div class="form-grid">

                    <div class="form-group">

                        <label
                            for="artist_name"
                            class="form-label"
                        >
                            Artist Name
                            <span class="required">*</span>
                        </label>

                        <input
                            id="artist_name"
                            name="artist_name"
                            type="text"
                            class="form-input"
                            maxlength="150"
                            value="<?= Security::escape(
                                $old['artist_name']
                                ?? ''
                            ) ?>"
                            required
                        >

                        <div class="form-help">
                            Official artist or group name.
                        </div>

                    </div>


                    <div class="form-group">

                        <label
                            for="stage_name"
                            class="form-label"
                        >
                            Stage Name
                        </label>

                        <input
                            id="stage_name"
                            name="stage_name"
                            type="text"
                            class="form-input"
                            maxlength="150"
                            value="<?= Security::escape(
                                $old['stage_name']
                                ?? ''
                            ) ?>"
                        >

                        <div class="form-help">
                            Optional public-facing name.
                        </div>

                    </div>


                    <div class="form-group full">

                        <label
                            for="bio"
                            class="form-label"
                        >
                            Artist Bio
                        </label>

                        <textarea
                            id="bio"
                            name="bio"
                            class="form-textarea"
                            maxlength="5000"
                        ><?= Security::escape(
                            $old['bio']
                            ?? ''
                        ) ?></textarea>

                        <div class="form-help">
                            Maximum 5,000 characters.
                        </div>

                    </div>

                </div>

            </section>


            <!-- =====================================================
                 Genre
                 ===================================================== -->

            <section class="form-section">

                <h2 class="form-section-title">
                    Music Profile
                </h2>

                <p class="form-section-description">
                    Help NasyidOS understand the artist's
                    musical positioning.
                </p>


                <div class="form-grid">

                    <div class="form-group">

                        <label
                            for="genre"
                            class="form-label"
                        >
                            Genre
                        </label>

                        <input
                            id="genre"
                            name="genre"
                            type="text"
                            class="form-input"
                            maxlength="100"
                            value="<?= Security::escape(
                                $old['genre']
                                ?? 'Nasyid'
                            ) ?>"
                        >

                    </div>


                    <div class="form-group">

                        <label
                            for="subgenre"
                            class="form-label"
                        >
                            Subgenre
                        </label>

                        <input
                            id="subgenre"
                            name="subgenre"
                            type="text"
                            class="form-input"
                            maxlength="100"
                            placeholder="Contemporary Nasyid"
                            value="<?= Security::escape(
                                $old['subgenre']
                                ?? ''
                            ) ?>"
                        >

                    </div>

                </div>

            </section>


            <!-- =====================================================
                 Streaming Platforms
                 ===================================================== -->

            <section class="form-section">

                <h2 class="form-section-title">
                    Streaming Platforms
                </h2>

                <p class="form-section-description">
                    Connect the artist's official music
                    profiles.
                </p>


                <div class="form-grid">

                    <div class="form-group">

                        <label
                            for="spotify_url"
                            class="form-label"
                        >
                            Spotify URL
                        </label>

                        <input
                            id="spotify_url"
                            name="spotify_url"
                            type="url"
                            class="form-input"
                            maxlength="500"
                            placeholder="https://open.spotify.com/..."
                            value="<?= Security::escape(
                                $old['spotify_url']
                                ?? ''
                            ) ?>"
                        >

                    </div>


                    <div class="form-group">

                        <label
                            for="apple_music_url"
                            class="form-label"
                        >
                            Apple Music URL
                        </label>

                        <input
                            id="apple_music_url"
                            name="apple_music_url"
                            type="url"
                            class="form-input"
                            maxlength="500"
                            placeholder="https://music.apple.com/..."
                            value="<?= Security::escape(
                                $old['apple_music_url']
                                ?? ''
                            ) ?>"
                        >

                    </div>


                    <div class="form-group">

                        <label
                            for="youtube_url"
                            class="form-label"
                        >
                            YouTube URL
                        </label>

                        <input
                            id="youtube_url"
                            name="youtube_url"
                            type="url"
                            class="form-input"
                            maxlength="500"
                            placeholder="https://youtube.com/..."
                            value="<?= Security::escape(
                                $old['youtube_url']
                                ?? ''
                            ) ?>"
                        >

                    </div>

                </div>

            </section>


            <!-- =====================================================
                 Social Platforms
                 ===================================================== -->

            <section class="form-section">

                <h2 class="form-section-title">
                    Social Platforms
                </h2>

                <p class="form-section-description">
                    Connect official social profiles.
                </p>


                <div class="form-grid">

                    <div class="form-group">

                        <label
                            for="tiktok_url"
                            class="form-label"
                        >
                            TikTok URL
                        </label>

                        <input
                            id="tiktok_url"
                            name="tiktok_url"
                            type="url"
                            class="form-input"
                            maxlength="500"
                            placeholder="https://www.tiktok.com/@..."
                            value="<?= Security::escape(
                                $old['tiktok_url']
                                ?? ''
                            ) ?>"
                        >

                    </div>


                    <div class="form-group">

                        <label
                            for="instagram_url"
                            class="form-label"
                        >
                            Instagram URL
                        </label>

                        <input
                            id="instagram_url"
                            name="instagram_url"
                            type="url"
                            class="form-input"
                            maxlength="500"
                            placeholder="https://www.instagram.com/..."
                            value="<?= Security::escape(
                                $old['instagram_url']
                                ?? ''
                            ) ?>"
                        >

                    </div>


                    <div class="form-group">

                        <label
                            for="facebook_url"
                            class="form-label"
                        >
                            Facebook URL
                        </label>

                        <input
                            id="facebook_url"
                            name="facebook_url"
                            type="url"
                            class="form-input"
                            maxlength="500"
                            placeholder="https://www.facebook.com/..."
                            value="<?= Security::escape(
                                $old['facebook_url']
                                ?? ''
                            ) ?>"
                        >

                    </div>

                </div>

            </section>


            <!-- =====================================================
                 Actions
                 ===================================================== -->

            <div class="form-actions">

                <a
                    href="?page=artists"
                    class="secondary-button"
                >
                    Cancel
                </a>

                <button
                    type="submit"
                    class="primary-button"
                >
                    Create Artist
                </button>

            </div>

        </form>

    </main>

</div>

</body>

</html>