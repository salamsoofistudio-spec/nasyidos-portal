<?php

declare(strict_types=1);


/*
|--------------------------------------------------------------------------
| Artist Edit View
|--------------------------------------------------------------------------
|
| Expected variables:
|
| $artist
|
| Supplied by:
| ArtistController::edit()
|
*/


/*
|--------------------------------------------------------------------------
| Escape helper
|--------------------------------------------------------------------------
*/

function artist_edit_e(?string $value): string
{
    return htmlspecialchars(
        $value ?? '',
        ENT_QUOTES,
        'UTF-8'
    );
}


/*
|--------------------------------------------------------------------------
| Session validation messages
|--------------------------------------------------------------------------
*/

$errors =
    $_SESSION['artist_errors']
    ?? [];

if (!is_array($errors)) {
    $errors = [];
}


/*
|--------------------------------------------------------------------------
| Old form data
|--------------------------------------------------------------------------
|
| If validation failed, controller stores the submitted values
| here so the user does not lose their input.
|
*/

$old =
    $_SESSION['old_artist']
    ?? [];

if (!is_array($old)) {
    $old = [];
}


/*
|--------------------------------------------------------------------------
| Helper for form values
|--------------------------------------------------------------------------
*/

$formValue =
    static function (
        string $field,
        string $fallback = ''
    ) use (
        $old,
        $artist
    ): string {

        if (
            array_key_exists(
                $field,
                $old
            )
        ) {

            return (string) $old[$field];
        }

        return (string) (
            $artist[$field]
            ?? $fallback
        );
    };


/*
|--------------------------------------------------------------------------
| Artist ID
|--------------------------------------------------------------------------
*/

$artistId =
    (int) (
        $artist['id']
        ?? 0
    );


/*
|--------------------------------------------------------------------------
| Basic artist information
|--------------------------------------------------------------------------
*/

$artistName =
    $formValue(
        'artist_name'
    );

$stageName =
    $formValue(
        'stage_name'
    );

$bio =
    $formValue(
        'bio'
    );

$genre =
    $formValue(
        'genre',
        'Nasyid'
    );

$subgenre =
    $formValue(
        'subgenre'
    );

$profileImage =
    $formValue(
        'profile_image'
    );


/*
|--------------------------------------------------------------------------
| Platform URLs
|--------------------------------------------------------------------------
*/

$spotifyUrl =
    $formValue(
        'spotify_url'
    );

$appleMusicUrl =
    $formValue(
        'apple_music_url'
    );

$youtubeUrl =
    $formValue(
        'youtube_url'
    );

$tiktokUrl =
    $formValue(
        'tiktok_url'
    );

$instagramUrl =
    $formValue(
        'instagram_url'
    );

$facebookUrl =
    $formValue(
        'facebook_url'
    );


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
        is_array($words)
        &&
        count($words) >= 2
    ) {

        $initials =
            mb_strtoupper(
                mb_substr(
                    (string) $words[0],
                    0,
                    1
                )
                .
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
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Edit Artist —
        <?= artist_edit_e($artistName) ?>
        — NasyidOS
    </title>

    <meta
        name="description"
        content="Edit artist profile in NasyidOS."
    >

    <link
        rel="stylesheet"
        href="/assets/css/artist-edit.css"
    >

</head>


<body>

<main class="edit-page">


    <!-- =====================================================
         TOP BAR
         ===================================================== -->

    <header class="edit-topbar">

        <a
            href="?page=artist&id=<?= $artistId ?>"
            class="back-link"
        >

            <span
                class="back-icon"
                aria-hidden="true"
            >
                ←
            </span>

            <span>
                Back to Artist
            </span>

        </a>


        <div class="topbar-brand">
            NasyidOS
        </div>

    </header>


    <!-- =====================================================
         PAGE HEADER
         ===================================================== -->

    <section class="page-header">

        <div>

            <div class="eyebrow">
                ARTIST PROFILE
            </div>

            <h1>
                Edit Artist
            </h1>

            <p>
                Update your artist identity and digital platform
                profiles before running a NasyidOS audit.
            </p>

        </div>


        <div class="artist-mini">

            <div class="artist-mini-avatar">
                <?= artist_edit_e($initials) ?>
            </div>

            <div>

                <strong>
                    <?= artist_edit_e($artistName) ?>
                </strong>

                <?php if ($stageName !== ''): ?>

                    <span>
                        <?= artist_edit_e($stageName) ?>
                    </span>

                <?php endif; ?>

            </div>

        </div>

    </section>


    <!-- =====================================================
         VALIDATION ERRORS
         ===================================================== -->

    <?php if (!empty($errors)): ?>

        <section
            class="alert alert-error"
            aria-live="polite"
        >

            <div class="alert-icon">
                !
            </div>

            <div>

                <strong>
                    Please check the following:
                </strong>

                <ul>

                    <?php foreach ($errors as $error): ?>

                        <li>
                            <?= artist_edit_e(
                                (string) $error
                            ) ?>
                        </li>

                    <?php endforeach; ?>

                </ul>

            </div>

        </section>

    <?php endif; ?>


    <!-- =====================================================
         FORM
         ===================================================== -->

    <form
        method="POST"
        action="?page=artist-edit&id=<?= $artistId ?>"
        class="edit-form"
        autocomplete="off"
    >

        <!-- =================================================
             SECURITY
             ================================================= -->

        <input
            type="hidden"
            name="csrf_token"
            value="<?= artist_edit_e(
                Security::csrfToken()
            ) ?>"
        >


        <!-- =================================================
             IDENTITY SECTION
             ================================================= -->

        <section class="form-card">

            <div class="form-card-header">

                <div>

                    <div class="section-kicker">
                        01
                    </div>

                    <h2>
                        Artist Identity
                    </h2>

                    <p>
                        Keep the artist identity consistent across
                        digital platforms.
                    </p>

                </div>

            </div>


            <div class="form-grid form-grid-two">


                <!-- Artist Name -->

                <div class="field">

                    <label
                        for="artist_name"
                    >
                        Artist Name
                        <span class="required">
                            *
                        </span>
                    </label>

                    <input
                        type="text"
                        id="artist_name"
                        name="artist_name"
                        value="<?= artist_edit_e(
                            $artistName
                        ) ?>"
                        maxlength="150"
                        required
                    >

                    <span class="field-help">
                        Official artist or group name.
                    </span>

                </div>


                <!-- Stage Name -->

                <div class="field">

                    <label
                        for="stage_name"
                    >
                        Stage Name
                    </label>

                    <input
                        type="text"
                        id="stage_name"
                        name="stage_name"
                        value="<?= artist_edit_e(
                            $stageName
                        ) ?>"
                        maxlength="150"
                    >

                    <span class="field-help">
                        Public-facing name if different.
                    </span>

                </div>


                <!-- Genre -->

                <div class="field">

                    <label
                        for="genre"
                    >
                        Genre
                    </label>

                    <select
                        id="genre"
                        name="genre"
                    >

                        <?php
                        $genres = [
                            'Nasyid',
                            'Islamic',
                            'Pop',
                            'Nasheed',
                            'Contemporary',
                            'Other'
                        ];
                        ?>

                        <?php foreach (
                            $genres as $genreOption
                        ): ?>

                            <option
                                value="<?= artist_edit_e(
                                    $genreOption
                                ) ?>"
                                <?= $genre === $genreOption
                                    ? 'selected'
                                    : '' ?>
                            >
                                <?= artist_edit_e(
                                    $genreOption
                                ) ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                    <span class="field-help">
                        Primary music category.
                    </span>

                </div>


                <!-- Subgenre -->

                <div class="field">

                    <label
                        for="subgenre"
                    >
                        Subgenre
                    </label>

                    <input
                        type="text"
                        id="subgenre"
                        name="subgenre"
                        value="<?= artist_edit_e(
                            $subgenre
                        ) ?>"
                        maxlength="100"
                        placeholder="e.g. Contemporary Nasyid"
                    >

                    <span class="field-help">
                        More specific sound or positioning.
                    </span>

                </div>


            </div>


            <!-- Bio -->

            <div class="field field-full">

                <label
                    for="bio"
                >
                    Artist Bio
                </label>

                <textarea
                    id="bio"
                    name="bio"
                    rows="7"
                    maxlength="5000"
                    placeholder="Write a concise and meaningful artist biography..."
                ><?= artist_edit_e(
                    $bio
                ) ?></textarea>

                <div class="field-footer">

                    <span class="field-help">
                        Use a clear bio that can later support
                        platform positioning and discovery.
                    </span>

                    <span
                        class="character-hint"
                        id="bio-counter"
                    >
                        0 / 5000
                    </span>

                </div>

            </div>

        </section>


        <!-- =================================================
             PLATFORM SECTION
             ================================================= -->

        <section class="form-card">

            <div class="form-card-header">

                <div>

                    <div class="section-kicker">
                        02
                    </div>

                    <h2>
                        Digital Platforms
                    </h2>

                    <p>
                        Connect the artist's official profiles.
                        These links will become inputs for future
                        NasyidOS audits.
                    </p>

                </div>

            </div>


            <div class="platform-form-grid">


                <!-- Spotify -->

                <div class="platform-field">

                    <div class="platform-heading">

                        <div class="platform-symbol">
                            S
                        </div>

                        <div>

                            <label
                                for="spotify_url"
                            >
                                Spotify
                            </label>

                            <span>
                                Music streaming
                            </span>

                        </div>

                    </div>

                    <input
                        type="url"
                        id="spotify_url"
                        name="spotify_url"
                        value="<?= artist_edit_e(
                            $spotifyUrl
                        ) ?>"
                        maxlength="500"
                        placeholder="https://open.spotify.com/artist/..."
                    >

                </div>


                <!-- Apple Music -->

                <div class="platform-field">

                    <div class="platform-heading">

                        <div class="platform-symbol">
                            A
                        </div>

                        <div>

                            <label
                                for="apple_music_url"
                            >
                                Apple Music
                            </label>

                            <span>
                                Music streaming
                            </span>

                        </div>

                    </div>

                    <input
                        type="url"
                        id="apple_music_url"
                        name="apple_music_url"
                        value="<?= artist_edit_e(
                            $appleMusicUrl
                        ) ?>"
                        maxlength="500"
                        placeholder="https://music.apple.com/..."
                    >

                </div>


                <!-- YouTube -->

                <div class="platform-field">

                    <div class="platform-heading">

                        <div class="platform-symbol">
                            Y
                        </div>

                        <div>

                            <label
                                for="youtube_url"
                            >
                                YouTube
                            </label>

                            <span>
                                Video & music
                            </span>

                        </div>

                    </div>

                    <input
                        type="url"
                        id="youtube_url"
                        name="youtube_url"
                        value="<?= artist_edit_e(
                            $youtubeUrl
                        ) ?>"
                        maxlength="500"
                        placeholder="https://youtube.com/@..."
                    >

                </div>


                <!-- TikTok -->

                <div class="platform-field">

                    <div class="platform-heading">

                        <div class="platform-symbol">
                            T
                        </div>

                        <div>

                            <label
                                for="tiktok_url"
                            >
                                TikTok
                            </label>

                            <span>
                                Short-form video
                            </span>

                        </div>

                    </div>

                    <input
                        type="url"
                        id="tiktok_url"
                        name="tiktok_url"
                        value="<?= artist_edit_e(
                            $tiktokUrl
                        ) ?>"
                        maxlength="500"
                        placeholder="https://www.tiktok.com/@..."
                    >

                </div>


                <!-- Instagram -->

                <div class="platform-field">

                    <div class="platform-heading">

                        <div class="platform-symbol">
                            I
                        </div>

                        <div>

                            <label
                                for="instagram_url"
                            >
                                Instagram
                            </label>

                            <span>
                                Social discovery
                            </span>

                        </div>

                    </div>

                    <input
                        type="url"
                        id="instagram_url"
                        name="instagram_url"
                        value="<?= artist_edit_e(
                            $instagramUrl
                        ) ?>"
                        maxlength="500"
                        placeholder="https://instagram.com/..."
                    >

                </div>


                <!-- Facebook -->

                <div class="platform-field">

                    <div class="platform-heading">

                        <div class="platform-symbol">
                            F
                        </div>

                        <div>

                            <label
                                for="facebook_url"
                            >
                                Facebook
                            </label>

                            <span>
                                Community & social
                            </span>

                        </div>

                    </div>

                    <input
                        type="url"
                        id="facebook_url"
                        name="facebook_url"
                        value="<?= artist_edit_e(
                            $facebookUrl
                        ) ?>"
                        maxlength="500"
                        placeholder="https://facebook.com/..."
                    >

                </div>


            </div>

        </section>


        <!-- =================================================
             PROFILE IMAGE
             ================================================= -->

        <section class="form-card">

            <div class="form-card-header">

                <div>

                    <div class="section-kicker">
                        03
                    </div>

                    <h2>
                        Profile Image
                    </h2>

                    <p>
                        For V1.0 this field accepts an existing
                        image URL. File upload will be introduced
                        in a later security-controlled module.
                    </p>

                </div>

            </div>


            <div class="field">

                <label
                    for="profile_image"
                >
                    Profile Image URL
                </label>

                <input
                    type="url"
                    id="profile_image"
                    name="profile_image"
                    value="<?= artist_edit_e(
                        $profileImage
                    ) ?>"
                    maxlength="500"
                    placeholder="https://example.com/artist-image.jpg"
                >

                <span class="field-help">
                    Use HTTPS whenever possible.
                </span>

            </div>

        </section>


        <!-- =================================================
             FORM ACTIONS
             ================================================= -->

        <section class="form-actions">

            <a
                href="?page=artist&id=<?= $artistId ?>"
                class="button button-secondary"
            >
                Cancel
            </a>


            <button
                type="submit"
                class="button button-primary"
            >
                Save Changes
            </button>

        </section>


    </form>


    <!-- =====================================================
         FOOTER
         ===================================================== -->

    <footer class="edit-footer">

        <span>
            NasyidOS
        </span>

        <span>
            Powered by Sarang Seni Studio
        </span>

    </footer>


</main>


<script src="/assets/js/artist-edit.js"></script>

</body>

</html>