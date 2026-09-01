<?php

declare(strict_types=1);

final class ArtistController
{
    /**
     * =========================================================
     * CREATE ARTIST
     * =========================================================
     */
    public static function create(): void
    {
        Security::verifyCsrf();

        $user = Auth::requireLogin();

        $userId = (int) $user['id'];

        /*
        |--------------------------------------------------------------------------
        | Collect input
        |--------------------------------------------------------------------------
        */

        $data = [
            'artist_name' =>
                trim(
                    (string) (
                        $_POST['artist_name']
                        ?? ''
                    )
                ),

            'stage_name' =>
                trim(
                    (string) (
                        $_POST['stage_name']
                        ?? ''
                    )
                ),

            'bio' =>
                trim(
                    (string) (
                        $_POST['bio']
                        ?? ''
                    )
                ),

            'genre' =>
                trim(
                    (string) (
                        $_POST['genre']
                        ?? 'Nasyid'
                    )
                ),

            'subgenre' =>
                trim(
                    (string) (
                        $_POST['subgenre']
                        ?? ''
                    )
                ),

            'spotify_url' =>
                trim(
                    (string) (
                        $_POST['spotify_url']
                        ?? ''
                    )
                ),

            'apple_music_url' =>
                trim(
                    (string) (
                        $_POST['apple_music_url']
                        ?? ''
                    )
                ),

            'youtube_url' =>
                trim(
                    (string) (
                        $_POST['youtube_url']
                        ?? ''
                    )
                ),

            'tiktok_url' =>
                trim(
                    (string) (
                        $_POST['tiktok_url']
                        ?? ''
                    )
                ),

            'instagram_url' =>
                trim(
                    (string) (
                        $_POST['instagram_url']
                        ?? ''
                    )
                ),

            'facebook_url' =>
                trim(
                    (string) (
                        $_POST['facebook_url']
                        ?? ''
                    )
                ),

            'profile_image' =>
                trim(
                    (string) (
                        $_POST['profile_image']
                        ?? ''
                    )
                ),
        ];


        /*
        |--------------------------------------------------------------------------
        | Validate
        |--------------------------------------------------------------------------
        */

        $errors = [];


        if (
            mb_strlen(
                $data['artist_name']
            ) < 2
        ) {
            $errors[] =
                'Nama artis mesti sekurang-kurangnya 2 aksara.';
        }


        if (
            mb_strlen(
                $data['artist_name']
            ) > 150
        ) {
            $errors[] =
                'Nama artis terlalu panjang.';
        }


        if (
            mb_strlen(
                $data['stage_name']
            ) > 150
        ) {
            $errors[] =
                'Stage name terlalu panjang.';
        }


        if (
            mb_strlen(
                $data['bio']
            ) > 5000
        ) {
            $errors[] =
                'Bio terlalu panjang.';
        }


        /*
        |--------------------------------------------------------------------------
        | Validate URLs
        |--------------------------------------------------------------------------
        */

        $urlFields = [
            'spotify_url',
            'apple_music_url',
            'youtube_url',
            'tiktok_url',
            'instagram_url',
            'facebook_url',
        ];


        foreach (
            $urlFields as $field
        ) {

            if (
                $data[$field] !== ''
                &&
                !filter_var(
                    $data[$field],
                    FILTER_VALIDATE_URL
                )
            ) {

                $errors[] =
                    ucfirst(
                        str_replace(
                            '_',
                            ' ',
                            $field
                        )
                    ) .
                    ' tidak sah.';
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Validation failed
        |--------------------------------------------------------------------------
        */

        if ($errors) {

            $_SESSION['artist_errors'] =
                $errors;

            $_SESSION['old_artist'] =
                $data;

            header(
                'Location: ?page=artists-create'
            );

            exit;
        }


        /*
        |--------------------------------------------------------------------------
        | Create artist
        |--------------------------------------------------------------------------
        */

        $artistId =
            Artist::create(
                $userId,
                $data
            );


        /*
        |--------------------------------------------------------------------------
        | Audit log
        |--------------------------------------------------------------------------
        */

        self::writeAuditLog(
            $userId,
            'artist_created',
            'Artist ID: ' . $artistId
        );


        /*
        |--------------------------------------------------------------------------
        | Success
        |--------------------------------------------------------------------------
        */

        $_SESSION['success'] =
            'Artist berjaya ditambahkan.';


        unset(
            $_SESSION['old_artist'],
            $_SESSION['artist_errors']
        );


        header(
            'Location: ?page=artists'
        );

        exit;
    }


    /**
     * =========================================================
     * SHOW ARTIST
     * =========================================================
     */
    public static function show(): void
    {
        $user =
            Auth::requireLogin();

        $userId =
            (int) $user['id'];


        /*
        |--------------------------------------------------------------------------
        | Get artist ID
        |--------------------------------------------------------------------------
        */

        $artistId =
            filter_input(
                INPUT_GET,
                'id',
                FILTER_VALIDATE_INT
            );


        if (
            !$artistId
            ||
            $artistId < 1
        ) {

            http_response_code(404);

            exit(
                'Artist tidak dijumpai.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Ownership check
        |--------------------------------------------------------------------------
        */

        $artist =
            Artist::findForUser(
                (int) $artistId,
                $userId
            );


        /*
        |--------------------------------------------------------------------------
        | Artist not found / not owned
        |--------------------------------------------------------------------------
        */

        if (!$artist) {

            http_response_code(404);

            exit(
                'Artist tidak dijumpai.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Load view
        |--------------------------------------------------------------------------
        */

        require
            __DIR__ .
            '/../../views/artist-detail.php';

        exit;
    }


    /**
     * =========================================================
     * SHOW EDIT FORM
     * =========================================================
     */
    public static function edit(): void
    {
        $user =
            Auth::requireLogin();

        $userId =
            (int) $user['id'];


        /*
        |--------------------------------------------------------------------------
        | Get artist ID
        |--------------------------------------------------------------------------
        */

        $artistId =
            filter_input(
                INPUT_GET,
                'id',
                FILTER_VALIDATE_INT
            );


        if (
            !$artistId
            ||
            $artistId < 1
        ) {

            http_response_code(404);

            exit(
                'Artist tidak dijumpai.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Ownership check
        |--------------------------------------------------------------------------
        */

        $artist =
            Artist::findForUser(
                (int) $artistId,
                $userId
            );


        if (!$artist) {

            http_response_code(404);

            exit(
                'Artist tidak dijumpai.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Load edit view
        |--------------------------------------------------------------------------
        */

        require
            __DIR__ .
            '/../../views/artist-edit.php';

        exit;
    }


    /**
     * =========================================================
     * UPDATE ARTIST
     * =========================================================
     */
    public static function update(): void
    {
        Security::verifyCsrf();

        $user =
            Auth::requireLogin();

        $userId =
            (int) $user['id'];


        /*
        |--------------------------------------------------------------------------
        | Get artist ID
        |--------------------------------------------------------------------------
        */

        $artistId =
            filter_input(
                INPUT_GET,
                'id',
                FILTER_VALIDATE_INT
            );


        if (
            !$artistId
            ||
            $artistId < 1
        ) {

            http_response_code(404);

            exit(
                'Artist tidak dijumpai.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Verify ownership BEFORE accepting update
        |--------------------------------------------------------------------------
        */

        $existingArtist =
            Artist::findForUser(
                (int) $artistId,
                $userId
            );


        if (!$existingArtist) {

            http_response_code(404);

            exit(
                'Artist tidak dijumpai.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Collect input
        |--------------------------------------------------------------------------
        */

        $data = [
            'artist_name' =>
                trim(
                    (string) (
                        $_POST['artist_name']
                        ?? ''
                    )
                ),

            'stage_name' =>
                trim(
                    (string) (
                        $_POST['stage_name']
                        ?? ''
                    )
                ),

            'bio' =>
                trim(
                    (string) (
                        $_POST['bio']
                        ?? ''
                    )
                ),

            'genre' =>
                trim(
                    (string) (
                        $_POST['genre']
                        ?? 'Nasyid'
                    )
                ),

            'subgenre' =>
                trim(
                    (string) (
                        $_POST['subgenre']
                        ?? ''
                    )
                ),

            'spotify_url' =>
                trim(
                    (string) (
                        $_POST['spotify_url']
                        ?? ''
                    )
                ),

            'apple_music_url' =>
                trim(
                    (string) (
                        $_POST['apple_music_url']
                        ?? ''
                    )
                ),

            'youtube_url' =>
                trim(
                    (string) (
                        $_POST['youtube_url']
                        ?? ''
                    )
                ),

            'tiktok_url' =>
                trim(
                    (string) (
                        $_POST['tiktok_url']
                        ?? ''
                    )
                ),

            'instagram_url' =>
                trim(
                    (string) (
                        $_POST['instagram_url']
                        ?? ''
                    )
                ),

            'facebook_url' =>
                trim(
                    (string) (
                        $_POST['facebook_url']
                        ?? ''
                    )
                ),

            'profile_image' =>
                trim(
                    (string) (
                        $_POST['profile_image']
                        ?? ''
                    )
                ),
        ];


        /*
        |--------------------------------------------------------------------------
        | Validation
        |--------------------------------------------------------------------------
        */

        $errors = [];


        if (
            mb_strlen(
                $data['artist_name']
            ) < 2
        ) {

            $errors[] =
                'Nama artis mesti sekurang-kurangnya 2 aksara.';
        }


        if (
            mb_strlen(
                $data['artist_name']
            ) > 150
        ) {

            $errors[] =
                'Nama artis terlalu panjang.';
        }


        if (
            mb_strlen(
                $data['stage_name']
            ) > 150
        ) {

            $errors[] =
                'Stage name terlalu panjang.';
        }


        if (
            mb_strlen(
                $data['bio']
            ) > 5000
        ) {

            $errors[] =
                'Bio terlalu panjang.';
        }


        /*
        |--------------------------------------------------------------------------
        | URL validation
        |--------------------------------------------------------------------------
        */

        $urlFields = [
            'spotify_url',
            'apple_music_url',
            'youtube_url',
            'tiktok_url',
            'instagram_url',
            'facebook_url',
        ];


        foreach (
            $urlFields as $field
        ) {

            if (
                $data[$field] !== ''
                &&
                !filter_var(
                    $data[$field],
                    FILTER_VALIDATE_URL
                )
            ) {

                $errors[] =
                    ucfirst(
                        str_replace(
                            '_',
                            ' ',
                            $field
                        )
                    ) .
                    ' tidak sah.';
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Validation failed
        |--------------------------------------------------------------------------
        */

        if ($errors) {

            $_SESSION['artist_errors'] =
                $errors;

            $_SESSION['old_artist'] =
                $data;

            header(
                'Location: ?page=artist-edit&id=' .
                (int) $artistId
            );

            exit;
        }


        /*
        |--------------------------------------------------------------------------
        | Update artist
        |--------------------------------------------------------------------------
        */

        $updated =
            Artist::update(
                (int) $artistId,
                $userId,
                $data
            );


        if (!$updated) {

            $_SESSION['artist_errors'] = [
                'Artist gagal dikemaskini. Sila cuba lagi.'
            ];

            $_SESSION['old_artist'] =
                $data;

            header(
                'Location: ?page=artist-edit&id=' .
                (int) $artistId
            );

            exit;
        }


        /*
        |--------------------------------------------------------------------------
        | Audit log
        |--------------------------------------------------------------------------
        */

        self::writeAuditLog(
            $userId,
            'artist_updated',
            'Artist ID: ' . $artistId
        );


        /*
        |--------------------------------------------------------------------------
        | Success
        |--------------------------------------------------------------------------
        */

        $_SESSION['success'] =
            'Artist berjaya dikemaskini.';


        unset(
            $_SESSION['old_artist'],
            $_SESSION['artist_errors']
        );


        header(
            'Location: ?page=artist&id=' .
            (int) $artistId
        );

        exit;
    }


    /**
     * =========================================================
     * WRITE AUDIT LOG
     * =========================================================
     *
     * Centralised helper supaya create/update menggunakan
     * struktur audit yang sama.
     */
    private static function writeAuditLog(
        int $userId,
        string $action,
        string $meta
    ): void {

        $db =
            Database::connection();


        $stmt =
            $db->prepare(
                'INSERT INTO audit_logs
                (
                    user_id,
                    action,
                    meta,
                    ip_address,
                    user_agent
                )
                VALUES
                (?, ?, ?, ?, ?)'
            );


        $stmt->execute([
            $userId,

            $action,

            $meta,

            $_SERVER['REMOTE_ADDR']
                ?? null,

            substr(
                $_SERVER['HTTP_USER_AGENT']
                    ?? '',
                0,
                500
            ),
        ]);
    }
}