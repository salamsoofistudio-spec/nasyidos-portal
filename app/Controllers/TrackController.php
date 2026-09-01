<?php

declare(strict_types=1);

final class TrackController
{
    /*
    |--------------------------------------------------------------------------
    | Create Track
    |--------------------------------------------------------------------------
    */

    public static function create(): void
    {
        $user = Auth::requireLogin();

        Security::verifyCsrf();

        $releaseId = filter_input(
            INPUT_POST,
            'release_id',
            FILTER_VALIDATE_INT
        );

        if (!$releaseId) {
            http_response_code(400);
            exit('Invalid release.');
        }

        /*
        |--------------------------------------------------------------------------
        | Verify release belongs to current user
        |--------------------------------------------------------------------------
        */

        $release = Release::findForUser(
            (int) $releaseId,
            (int) $user['id']
        );

        if (!$release) {
            http_response_code(404);
            exit('Release not found.');
        }

        /*
        |--------------------------------------------------------------------------
        | Read form data
        |--------------------------------------------------------------------------
        */

        $data = self::validatedData();

        /*
        |--------------------------------------------------------------------------
        | Create
        |--------------------------------------------------------------------------
        */

        Track::create(
            (int) $releaseId,
            $data
        );

        /*
        |--------------------------------------------------------------------------
        | Redirect
        |--------------------------------------------------------------------------
        */

        header(
            'Location: ?page=release&id=' .
            (int) $releaseId
        );

        exit;
    }

/*
|--------------------------------------------------------------------------
| Show Track - GET
|--------------------------------------------------------------------------
| 32I-5
|--------------------------------------------------------------------------
*/

public static function show(): void
{
    $user =
        Auth::requireLogin();


    $releaseId =
        filter_input(
            INPUT_GET,
            'release_id',
            FILTER_VALIDATE_INT
        );


    $trackId =
        filter_input(
            INPUT_GET,
            'id',
            FILTER_VALIDATE_INT
        );


    if (
        !$releaseId ||
        $releaseId < 1 ||
        !$trackId ||
        $trackId < 1
    ) {

        http_response_code(404);

        exit(
            'Track tidak dijumpai.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Verify release ownership
    |--------------------------------------------------------------------------
    */

    $release =
        Release::findForUser(
            (int) $releaseId,
            (int) $user['id']
        );


    if (!$release) {

        http_response_code(404);

        exit(
            'Release tidak dijumpai.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Verify track ownership through release
    |--------------------------------------------------------------------------
    */

    $track =
        Track::findForRelease(
            (int) $trackId,
            (int) $releaseId,
            (int) $user['id']
        );


    if (!$track) {

        http_response_code(404);

        exit(
            'Track tidak dijumpai.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Render
    |--------------------------------------------------------------------------
    */

    require
        __DIR__ .
        '/../../views/track.php';

    exit;
}

    /*
    |--------------------------------------------------------------------------
    | Edit Track - GET
    |--------------------------------------------------------------------------
    */

    public static function edit(): void
    {
        $user = Auth::requireLogin();

        $trackId = filter_input(
            INPUT_GET,
            'id',
            FILTER_VALIDATE_INT
        );

        $releaseId = filter_input(
            INPUT_GET,
            'release_id',
            FILTER_VALIDATE_INT
        );

        if (
            !$trackId ||
            !$releaseId
        ) {
            http_response_code(400);
            exit('Invalid track.');
        }

        /*
        |--------------------------------------------------------------------------
        | Verify release belongs to user
        |--------------------------------------------------------------------------
        */

        $release = Release::findForUser(
            (int) $releaseId,
            (int) $user['id']
        );

        if (!$release) {
            http_response_code(404);
            exit('Release not found.');
        }

        /*
        |--------------------------------------------------------------------------
        | Find track owned through release
        |--------------------------------------------------------------------------
        */

        $track = Track::findForRelease(
            (int) $trackId,
            (int) $releaseId,
            (int) $user['id']
        );

        if (!$track) {
            http_response_code(404);
            exit('Track not found.');
        }

        /*
        |--------------------------------------------------------------------------
        | Render
        |--------------------------------------------------------------------------
        */

        require
            __DIR__ .
            '/../../views/track-edit.php';

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | Update Track - POST
    |--------------------------------------------------------------------------
    */

    public static function update(): void
    {
        $user = Auth::requireLogin();

        Security::verifyCsrf();

        $trackId = filter_input(
            INPUT_POST,
            'id',
            FILTER_VALIDATE_INT
        );

        $releaseId = filter_input(
            INPUT_POST,
            'release_id',
            FILTER_VALIDATE_INT
        );

        if (
            !$trackId ||
            !$releaseId
        ) {
            http_response_code(400);
            exit('Invalid track.');
        }

        /*
        |--------------------------------------------------------------------------
        | Verify release ownership
        |--------------------------------------------------------------------------
        */

        $release = Release::findForUser(
            (int) $releaseId,
            (int) $user['id']
        );

        if (!$release) {
            http_response_code(404);
            exit('Release not found.');
        }

        /*
        |--------------------------------------------------------------------------
        | Verify track ownership
        |--------------------------------------------------------------------------
        */

        $track = Track::findForRelease(
            (int) $trackId,
            (int) $releaseId,
            (int) $user['id']
        );

        if (!$track) {
            http_response_code(404);
            exit('Track not found.');
        }

        /*
        |--------------------------------------------------------------------------
        | Validate submitted data
        |--------------------------------------------------------------------------
        */

        $data = self::validatedData();

        /*
        |--------------------------------------------------------------------------
        | Update
        |--------------------------------------------------------------------------
        */

        Track::update(
            (int) $trackId,
            (int) $releaseId,
            (int) $user['id'],
            $data
        );

        /*
        |--------------------------------------------------------------------------
        | Redirect back to release
        |--------------------------------------------------------------------------
        */

        header(
            'Location: ?page=release&id=' .
            (int) $releaseId
        );

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | Delete Track - POST
    |--------------------------------------------------------------------------
    */

    public static function delete(): void
    {
        $user = Auth::requireLogin();

        Security::verifyCsrf();

        $trackId = filter_input(
            INPUT_POST,
            'id',
            FILTER_VALIDATE_INT
        );

        $releaseId = filter_input(
            INPUT_POST,
            'release_id',
            FILTER_VALIDATE_INT
        );

        if (
            !$trackId ||
            !$releaseId
        ) {
            http_response_code(400);
            exit('Invalid track.');
        }

        /*
        |--------------------------------------------------------------------------
        | Delete only if track belongs to user's release
        |--------------------------------------------------------------------------
        */

        $deleted = Track::delete(
            (int) $trackId,
            (int) $releaseId,
            (int) $user['id']
        );

        if (!$deleted) {
            http_response_code(404);
            exit('Track not found.');
        }

        header(
            'Location: ?page=release&id=' .
            (int) $releaseId
        );

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | Validate Track Form
    |--------------------------------------------------------------------------
    */

    private static function validatedData(): array
    {
        /*
        |--------------------------------------------------------------------------
        | Track number
        |--------------------------------------------------------------------------
        */

        $trackNumber = filter_input(
            INPUT_POST,
            'track_number',
            FILTER_VALIDATE_INT
        );

        if (
            $trackNumber === false ||
            $trackNumber === null ||
            $trackNumber < 1
        ) {
            http_response_code(422);
            exit('Track number must be 1 or greater.');
        }


        /*
        |--------------------------------------------------------------------------
        | Disc number
        |--------------------------------------------------------------------------
        */

        $discNumber = filter_input(
            INPUT_POST,
            'disc_number',
            FILTER_VALIDATE_INT
        );

        if (
            $discNumber === false ||
            $discNumber === null ||
            $discNumber < 1
        ) {
            http_response_code(422);
            exit('Disc number must be 1 or greater.');
        }


        /*
        |--------------------------------------------------------------------------
        | Track title
        |--------------------------------------------------------------------------
        */

        $trackTitle =
            trim(
                (string) (
                    $_POST['track_title'] ?? ''
                )
            );

        if ($trackTitle === '') {
            http_response_code(422);
            exit('Track title is required.');
        }

        if (
            mb_strlen($trackTitle) > 255
        ) {
            http_response_code(422);
            exit('Track title is too long.');
        }


        /*
        |--------------------------------------------------------------------------
        | Optional fields
        |--------------------------------------------------------------------------
        */

        $versionLabel =
            self::nullablePost(
                'version_label'
            );

        $isrc =
            self::nullablePost(
                'isrc'
            );

        $durationSeconds =
            self::nullableInteger(
                'duration_seconds'
            );

        $language =
            self::nullablePost(
                'language'
            );

        $lyrics =
            self::nullablePost(
                'lyrics'
            );

        $featuringArtists =
            self::nullablePost(
                'featuring_artists'
            );

        $composers =
            self::nullablePost(
                'composers'
            );

        $lyricists =
            self::nullablePost(
                'lyricists'
            );

        $producers =
            self::nullablePost(
                'producers'
            );

        $notes =
            self::nullablePost(
                'notes'
            );


        /*
        |--------------------------------------------------------------------------
        | Status fields
        |--------------------------------------------------------------------------
        */

        $lyricsStatus =
            (string) (
                $_POST['lyrics_status']
                ?? 'missing'
            );

        if (
            !in_array(
                $lyricsStatus,
                [
                    'missing',
                    'draft',
                    'complete',
                ],
                true
            )
        ) {
            $lyricsStatus = 'missing';
        }


        $audioStatus =
            (string) (
                $_POST['audio_status']
                ?? 'missing'
            );

        if (
            !in_array(
                $audioStatus,
                [
                    'missing',
                    'draft',
                    'ready',
                ],
                true
            )
        ) {
            $audioStatus = 'missing';
        }


        /*
        |--------------------------------------------------------------------------
        | Explicit
        |--------------------------------------------------------------------------
        */

        $isExplicit =
            isset($_POST['is_explicit'])
            ? 1
            : 0;


        /*
        |--------------------------------------------------------------------------
        | Return normalized data
        |--------------------------------------------------------------------------
        */

        return [
            'track_number' =>
                (int) $trackNumber,

            'disc_number' =>
                (int) $discNumber,

            'track_title' =>
                $trackTitle,

            'version_label' =>
                $versionLabel,

            'isrc' =>
                $isrc,

            'duration_seconds' =>
                $durationSeconds,

            'language' =>
                $language,

            'lyrics' =>
                $lyrics,

            'lyrics_status' =>
                $lyricsStatus,

            'audio_status' =>
                $audioStatus,

            'is_explicit' =>
                $isExplicit,

            'featuring_artists' =>
                $featuringArtists,

            'composers' =>
                $composers,

            'lyricists' =>
                $lyricists,

            'producers' =>
                $producers,

            'notes' =>
                $notes,
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | Nullable POST string
    |--------------------------------------------------------------------------
    */

    private static function nullablePost(
        string $key
    ): ?string {
        if (
            !isset($_POST[$key])
        ) {
            return null;
        }

        $value =
            trim(
                (string) $_POST[$key]
            );

        return $value === ''
            ? null
            : $value;
    }


    /*
    |--------------------------------------------------------------------------
    | Nullable Integer
    |--------------------------------------------------------------------------
    */

    private static function nullableInteger(
        string $key
    ): ?int {
        if (
            !isset($_POST[$key]) ||
            $_POST[$key] === ''
        ) {
            return null;
        }

        $value =
            filter_var(
                $_POST[$key],
                FILTER_VALIDATE_INT
            );

        if (
            $value === false ||
            $value < 0
        ) {
            http_response_code(422);

            exit(
                ucfirst($key) .
                ' must be a valid number.'
            );
        }

        return (int) $value;
    }
}