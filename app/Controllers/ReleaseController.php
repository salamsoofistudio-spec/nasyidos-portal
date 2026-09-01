<?php

declare(strict_types=1);

final class ReleaseController
{
    public static function index(): void
    {
        $user = Auth::requireLogin();

        $userId = (int) $user['id'];

        $releases = Release::allForUser($userId);

        $success = $_SESSION['success'] ?? '';
        $errors = $_SESSION['release_errors'] ?? [];

        if (!is_string($success)) {
            $success = '';
        }

        if (!is_array($errors)) {
            $errors = [];
        }

        unset(
            $_SESSION['success'],
            $_SESSION['release_errors']
        );

        require __DIR__ . '/../../views/releases.php';

        exit;
    }

    public static function show(): void
    {
        $user = Auth::requireLogin();

        $releaseId = filter_input(
            INPUT_GET,
            'id',
            FILTER_VALIDATE_INT
        );

        if (!$releaseId || $releaseId < 1) {
            http_response_code(404);
            exit('Release tidak dijumpai.');
        }

        $release = Release::findForUser(
            (int) $releaseId,
            (int) $user['id']
        );

        if (!$release) {
            http_response_code(404);
            exit('Release tidak dijumpai.');
        }

        $tracks = Release::tracksForUser(
            (int) $releaseId,
            (int) $user['id']
        );

        $platforms = Release::platformsForUser(
            (int) $releaseId,
            (int) $user['id']
        );

        require __DIR__ . '/../../views/release-detail.php';

        exit;
    }

    public static function create(): void
    {
        Security::verifyCsrf();

        $user = Auth::requireLogin();

        $userId = (int) $user['id'];

        $data = self::collectInput();

        $errors = self::validate(
            $data,
            $userId
        );

        if ($errors) {
            $_SESSION['release_errors'] = $errors;
            $_SESSION['old_release'] = $data;

            header('Location: ?page=release-create');

            exit;
        }

        $artist = Artist::findForUser(
            $data['artist_id'],
            $userId
        );

        if (!$artist) {
            http_response_code(404);
            exit('Artist tidak dijumpai.');
        }

        $data['artist_name'] =
            trim(
                (string) $artist['artist_name']
            );

        $releaseId = Release::create(
            $userId,
            $data
        );

        $_SESSION['success'] =
            'Release berjaya dicipta.';

        unset(
            $_SESSION['old_release'],
            $_SESSION['release_errors']
        );

        header(
            'Location: ?page=release&id=' .
            $releaseId
        );

        exit;
    }

    public static function edit(): void
    {
        $user = Auth::requireLogin();

        $releaseId = filter_input(
            INPUT_GET,
            'id',
            FILTER_VALIDATE_INT
        );

        if (!$releaseId || $releaseId < 1) {
            http_response_code(404);
            exit('Release tidak dijumpai.');
        }

        $release = Release::findForUser(
            (int) $releaseId,
            (int) $user['id']
        );

        if (!$release) {
            http_response_code(404);
            exit('Release tidak dijumpai.');
        }

        $artists = Artist::allForUser(
            (int) $user['id']
        );

        $errors = $_SESSION['release_errors'] ?? [];
        $old = $_SESSION['old_release'] ?? [];

        if (!is_array($errors)) {
            $errors = [];
        }

        if (!is_array($old)) {
            $old = [];
        }

        unset(
            $_SESSION['release_errors'],
            $_SESSION['old_release']
        );

        if ($old) {
            $release = array_merge(
                $release,
                $old
            );
        }

        require __DIR__ . '/../../views/release-edit.php';

        exit;
    }

    public static function update(): void
    {
        Security::verifyCsrf();

        $user = Auth::requireLogin();

        $userId = (int) $user['id'];

        $releaseId = filter_input(
            INPUT_GET,
            'id',
            FILTER_VALIDATE_INT
        );

        if (!$releaseId || $releaseId < 1) {
            http_response_code(404);
            exit('Release tidak dijumpai.');
        }

        /*
        |--------------------------------------------------------------------------
        | Verify ownership before doing anything.
        |--------------------------------------------------------------------------
        */

        $existing = Release::findForUser(
            (int) $releaseId,
            $userId
        );

        if (!$existing) {
            http_response_code(404);
            exit('Release tidak dijumpai.');
        }

        /*
        |--------------------------------------------------------------------------
        | Collect submitted data.
        |--------------------------------------------------------------------------
        */

        $data = self::collectInput();

        /*
        |--------------------------------------------------------------------------
        | Validate submitted data.
        |--------------------------------------------------------------------------
        */

        $errors = self::validate(
            $data,
            $userId
        );

        if ($errors) {
            $_SESSION['release_errors'] = $errors;
            $_SESSION['old_release'] = $data;

            header(
                'Location: ?page=release-edit&id=' .
                (int) $releaseId
            );

            exit;
        }

        /*
        |--------------------------------------------------------------------------
        | Resolve artist.
        |--------------------------------------------------------------------------
        */

        $artist = Artist::findForUser(
            $data['artist_id'],
            $userId
        );

        if (!$artist) {
            http_response_code(404);
            exit('Artist tidak dijumpai.');
        }

        $data['artist_name'] =
            trim(
                (string) $artist['artist_name']
            );

        /*
        |--------------------------------------------------------------------------
        | Calculate audit information before transaction.
        |--------------------------------------------------------------------------
        */

        $audit = self::buildAudit(
            $existing,
            $data
        );

        /*
        |--------------------------------------------------------------------------
        | UPDATE + AUDIT
        |--------------------------------------------------------------------------
        |
        | Both operations must succeed together.
        |
        */

        $db = Database::connection();

        try {

            $db->beginTransaction();

            /*
            |--------------------------------------------------------------------------
            | Update release.
            |--------------------------------------------------------------------------
            */

            $updated = Release::update(
                (int) $releaseId,
                $userId,
                $data
            );

            if (!$updated) {
                throw new RuntimeException(
                    'Release gagal dikemaskini.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Insert release audit.
            |--------------------------------------------------------------------------
            */

            $auditStmt = $db->prepare(
                'INSERT INTO release_audits
                (
                    user_id,
                    release_id,
                    score,
                    tier,
                    notes_json
                )
                VALUES
                (
                    ?,
                    ?,
                    ?,
                    ?,
                    ?
                )'
            );

            $auditStmt->execute([
                $userId,
                (int) $releaseId,
                $audit['score'],
                $audit['tier'],
                $audit['notes_json'],
            ]);

            /*
            |--------------------------------------------------------------------------
            | Commit only after UPDATE + AUDIT both succeed.
            |--------------------------------------------------------------------------
            */

            $db->commit();

        } catch (Throwable $e) {

            if ($db->inTransaction()) {
                $db->rollBack();
            }

            error_log(
                'Release update/audit failed: ' .
                $e->getMessage()
            );

            $_SESSION['release_errors'] = [
                'Release gagal dikemaskini. Sila cuba lagi.'
            ];

            $_SESSION['old_release'] = $data;

            header(
                'Location: ?page=release-edit&id=' .
                (int) $releaseId
            );

            exit;
        }

        /*
        |--------------------------------------------------------------------------
        | Success.
        |--------------------------------------------------------------------------
        */

        $_SESSION['success'] =
            'Release berjaya dikemaskini.';

        unset(
            $_SESSION['old_release'],
            $_SESSION['release_errors']
        );

        header(
            'Location: ?page=release&id=' .
            (int) $releaseId
        );

        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | BUILD RELEASE AUDIT
    |--------------------------------------------------------------------------
    |
    | Produces a simple metadata completeness score.
    |
    */

    private static function buildAudit(
        array $existing,
        array $data
    ): array {

        /*
        |--------------------------------------------------------------------------
        | Score
        |--------------------------------------------------------------------------
        |
        | 10 metadata areas.
        |
        */

        $checks = [
            'artist' =>
                $data['artist_id'] > 0,

            'title' =>
                trim(
                    (string) $data['title']
                ) !== '',

            'release_type' =>
                trim(
                    (string) $data['release_type']
                ) !== '',

            'release_status' =>
                trim(
                    (string) $data['release_status']
                ) !== '',

            'genre' =>
                trim(
                    (string) $data['genre']
                ) !== '',

            'language' =>
                trim(
                    (string) $data['language']
                ) !== '',

            'release_date' =>
                $data['release_date'] !== null
                &&
                $data['release_date'] !== '',

            'upc' =>
                trim(
                    (string) $data['upc']
                ) !== '',

            'distributor' =>
                trim(
                    (string) $data['distributor']
                ) !== '',

            'cover_art_url' =>
                trim(
                    (string) $data['cover_art_url']
                ) !== '',
        ];

        $completed = 0;

        foreach ($checks as $complete) {
            if ($complete) {
                $completed++;
            }
        }

        $score = (int) round(
            ($completed / count($checks)) * 100
        );

        /*
        |--------------------------------------------------------------------------
        | Tier
        |--------------------------------------------------------------------------
        */

        if ($score >= 90) {

            $tier = 'excellent';

        } elseif ($score >= 70) {

            $tier = 'good';

        } elseif ($score >= 50) {

            $tier = 'needs_review';

        } else {

            $tier = 'incomplete';
        }

        /*
        |--------------------------------------------------------------------------
        | Detect changed fields.
        |--------------------------------------------------------------------------
        */

        $trackedFields = [
            'artist_id',
            'artist_name',
            'title',
            'release_type',
            'release_status',
            'genre',
            'language',
            'release_date',
            'upc',
            'distributor',
            'is_explicit',
            'cover_art_url',
            'pitch',
            'short_hook',
            'internal_notes',
        ];

        $changes = [];

        foreach ($trackedFields as $field) {

            $before =
                $existing[$field] ?? null;

            $after =
                $data[$field] ?? null;

            if (
                (string) $before !==
                (string) $after
            ) {

                $changes[$field] = [
                    'before' => $before,
                    'after' => $after,
                ];
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Audit notes.
        |--------------------------------------------------------------------------
        */

        $notes = [
            'event' => 'release_updated',

            'score' => $score,

            'tier' => $tier,

            'metadata' => [
                'completed' => $completed,
                'total' => count($checks),
            ],

            'changed_fields' => array_keys(
                $changes
            ),

            'changes' => $changes,
        ];

        $notesJson = json_encode(
            $notes,
            JSON_UNESCAPED_UNICODE |
            JSON_UNESCAPED_SLASHES |
            JSON_THROW_ON_ERROR
        );

        return [
            'score' => $score,
            'tier' => $tier,
            'notes_json' => $notesJson,
        ];
    }

    private static function collectInput(): array
    {
        return [
            'artist_id' => max(
                0,
                (int) (
                    $_POST['artist_id']
                    ?? 0
                )
            ),

            'artist_name' => trim(
                (string) (
                    $_POST['artist_name']
                    ?? ''
                )
            ),

            'title' => trim(
                (string) (
                    $_POST['title']
                    ?? ''
                )
            ),

            'release_type' => trim(
                (string) (
                    $_POST['release_type']
                    ?? 'single'
                )
            ),

            'release_status' => trim(
                (string) (
                    $_POST['release_status']
                    ?? 'draft'
                )
            ),

            'genre' => trim(
                (string) (
                    $_POST['genre']
                    ?? ''
                )
            ),

            'language' => trim(
                (string) (
                    $_POST['language']
                    ?? ''
                )
            ),

            'release_date' => self::nullableString(
                $_POST['release_date']
                ?? ''
            ),

            'upc' => trim(
                (string) (
                    $_POST['upc']
                    ?? ''
                )
            ),

            'distributor' => trim(
                (string) (
                    $_POST['distributor']
                    ?? ''
                )
            ),

            'is_explicit' =>
                !empty($_POST['is_explicit'])
                    ? 1
                    : 0,

            'cover_art_url' => trim(
                (string) (
                    $_POST['cover_art_url']
                    ?? ''
                )
            ),

            'pitch' => trim(
                (string) (
                    $_POST['pitch']
                    ?? ''
                )
            ),

            'short_hook' => trim(
                (string) (
                    $_POST['short_hook']
                    ?? ''
                )
            ),

            'internal_notes' => trim(
                (string) (
                    $_POST['internal_notes']
                    ?? ''
                )
            ),
        ];
    }

    private static function validate(
        array $data,
        int $userId
    ): array {
        $errors = [];

        $validTypes = [
            'single',
            'ep',
            'album',
            'compilation',
        ];

        $validStatuses = [
            'draft',
            'planned',
            'released',
            'archived',
        ];

        if ($data['artist_id'] < 1) {
            $errors[] =
                'Sila pilih artist.';
        } else {
            $artist = Artist::findForUser(
                $data['artist_id'],
                $userId
            );

            if (!$artist) {
                $errors[] =
                    'Artist yang dipilih tidak sah.';
            }
        }

        if (
            mb_strlen($data['title']) < 1
        ) {
            $errors[] =
                'Tajuk release diperlukan.';
        }

        if (
            mb_strlen($data['title']) > 200
        ) {
            $errors[] =
                'Tajuk release terlalu panjang.';
        }

        if (
            !in_array(
                $data['release_type'],
                $validTypes,
                true
            )
        ) {
            $errors[] =
                'Release type tidak sah.';
        }

        if (
            !in_array(
                $data['release_status'],
                $validStatuses,
                true
            )
        ) {
            $errors[] =
                'Release status tidak sah.';
        }

        if (
            mb_strlen($data['genre']) > 120
        ) {
            $errors[] =
                'Genre terlalu panjang.';
        }

        if (
            mb_strlen($data['language']) > 80
        ) {
            $errors[] =
                'Language terlalu panjang.';
        }

        if (
            $data['release_date'] !== null
            &&
            !self::validDate(
                $data['release_date']
            )
        ) {
            $errors[] =
                'Release date tidak sah.';
        }

        if (
            mb_strlen($data['upc']) > 50
        ) {
            $errors[] =
                'UPC terlalu panjang.';
        }

        if (
            mb_strlen($data['distributor']) > 150
        ) {
            $errors[] =
                'Distributor terlalu panjang.';
        }

        if (
            $data['cover_art_url'] !== ''
            &&
            (
                !filter_var(
                    $data['cover_art_url'],
                    FILTER_VALIDATE_URL
                )
                ||
                mb_strlen(
                    $data['cover_art_url']
                ) > 500
            )
        ) {
            $errors[] =
                'Cover art URL tidak sah.';
        }

        if (
            mb_strlen($data['pitch']) > 10000
        ) {
            $errors[] =
                'Pitch terlalu panjang.';
        }

        if (
            mb_strlen($data['short_hook']) > 5000
        ) {
            $errors[] =
                'Short hook terlalu panjang.';
        }

        if (
            mb_strlen($data['internal_notes']) > 10000
        ) {
            $errors[] =
                'Internal notes terlalu panjang.';
        }

        return $errors;
    }

    private static function validDate(
        string $value
    ): bool {
        $date = DateTime::createFromFormat(
            'Y-m-d',
            $value
        );

        return $date !== false
            && $date->format('Y-m-d') === $value;
    }

    private static function nullableString(
        mixed $value
    ): ?string {
        $value = trim((string) $value);

        return $value === ''
            ? null
            : $value;
    }
}