<?php

declare(strict_types=1);

final class Track
{
    /*
    |--------------------------------------------------------------------------
    | Get all tracks belonging to a release owned by the user.
    |--------------------------------------------------------------------------
    */

    public static function allForRelease(
        int $releaseId,
        int $userId
    ): array {
        $db = Database::connection();

        $stmt = $db->prepare(
            'SELECT
                rt.id,
                rt.release_id,
                rt.track_number,
                rt.disc_number,
                rt.track_title,
                rt.version_label,
                rt.isrc,
                rt.duration_seconds,
                rt.language,
                rt.lyrics,
                rt.lyrics_status,
                rt.audio_status,
                rt.is_explicit,
                rt.featuring_artists,
                rt.composers,
                rt.lyricists,
                rt.producers,
                rt.notes,
                rt.created_at,
                rt.updated_at
             FROM release_tracks AS rt
             INNER JOIN releases AS r
                ON r.id = rt.release_id
             WHERE
                rt.release_id = ?
                AND r.user_id = ?
             ORDER BY
                rt.disc_number ASC,
                rt.track_number ASC,
                rt.id ASC'
        );

        $stmt->execute([
            $releaseId,
            $userId,
        ]);

        return $stmt->fetchAll();
    }


    /*
    |--------------------------------------------------------------------------
    | Find one track belonging to a release owned by the user.
    |--------------------------------------------------------------------------
    */

    public static function findForRelease(
        int $trackId,
        int $releaseId,
        int $userId
    ): ?array {
        $db = Database::connection();

        $stmt = $db->prepare(
            'SELECT
                rt.id,
                rt.release_id,
                rt.track_number,
                rt.disc_number,
                rt.track_title,
                rt.version_label,
                rt.isrc,
                rt.duration_seconds,
                rt.language,
                rt.lyrics,
                rt.lyrics_status,
                rt.audio_status,
                rt.is_explicit,
                rt.featuring_artists,
                rt.composers,
                rt.lyricists,
                rt.producers,
                rt.notes,
                rt.created_at,
                rt.updated_at
             FROM release_tracks AS rt
             INNER JOIN releases AS r
                ON r.id = rt.release_id
             WHERE
                rt.id = ?
                AND rt.release_id = ?
                AND r.user_id = ?
             LIMIT 1'
        );

        $stmt->execute([
            $trackId,
            $releaseId,
            $userId,
        ]);

        $track = $stmt->fetch();

        if (!$track) {
            return null;
        }

        return $track;
    }


    /*
    |--------------------------------------------------------------------------
    | Create a track.
    |--------------------------------------------------------------------------
    */

    public static function create(
        int $releaseId,
        array $data
    ): int {
        $db = Database::connection();

        $stmt = $db->prepare(
            'INSERT INTO release_tracks
            (
                release_id,
                track_number,
                disc_number,
                track_title,
                version_label,
                isrc,
                duration_seconds,
                language,
                lyrics,
                lyrics_status,
                audio_status,
                is_explicit,
                featuring_artists,
                composers,
                lyricists,
                producers,
                notes
            )
            VALUES
            (
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?
            )'
        );

        $stmt->execute([
            $releaseId,

            $data['track_number'],

            $data['disc_number'],

            $data['track_title'],

            $data['version_label'],

            $data['isrc'],

            $data['duration_seconds'],

            $data['language'],

            $data['lyrics'],

            $data['lyrics_status'],

            $data['audio_status'],

            $data['is_explicit'],

            $data['featuring_artists'],

            $data['composers'],

            $data['lyricists'],

            $data['producers'],

            $data['notes'],
        ]);

        return (int) $db->lastInsertId();
    }


    /*
    |--------------------------------------------------------------------------
    | Update a track.
    |--------------------------------------------------------------------------
    */

    public static function update(
        int $trackId,
        int $releaseId,
        int $userId,
        array $data
    ): bool {
        $db = Database::connection();

        $stmt = $db->prepare(
            'UPDATE release_tracks AS rt
             INNER JOIN releases AS r
                ON r.id = rt.release_id
             SET
                rt.track_number = ?,
                rt.disc_number = ?,
                rt.track_title = ?,
                rt.version_label = ?,
                rt.isrc = ?,
                rt.duration_seconds = ?,
                rt.language = ?,
                rt.lyrics = ?,
                rt.lyrics_status = ?,
                rt.audio_status = ?,
                rt.is_explicit = ?,
                rt.featuring_artists = ?,
                rt.composers = ?,
                rt.lyricists = ?,
                rt.producers = ?,
                rt.notes = ?
             WHERE
                rt.id = ?
                AND rt.release_id = ?
                AND r.user_id = ?'
        );

        $stmt->execute([
            $data['track_number'],

            $data['disc_number'],

            $data['track_title'],

            $data['version_label'],

            $data['isrc'],

            $data['duration_seconds'],

            $data['language'],

            $data['lyrics'],

            $data['lyrics_status'],

            $data['audio_status'],

            $data['is_explicit'],

            $data['featuring_artists'],

            $data['composers'],

            $data['lyricists'],

            $data['producers'],

            $data['notes'],

            $trackId,

            $releaseId,

            $userId,
        ]);

        return $stmt->rowCount() > 0;
    }


    /*
    |--------------------------------------------------------------------------
    | Delete a track.
    |--------------------------------------------------------------------------
    */

    public static function delete(
        int $trackId,
        int $releaseId,
        int $userId
    ): bool {
        $db = Database::connection();

        $stmt = $db->prepare(
            'DELETE rt
             FROM release_tracks AS rt
             INNER JOIN releases AS r
                ON r.id = rt.release_id
             WHERE
                rt.id = ?
                AND rt.release_id = ?
                AND r.user_id = ?'
        );

        $stmt->execute([
            $trackId,
            $releaseId,
            $userId,
        ]);

        return $stmt->rowCount() > 0;
    }
}