<?php

declare(strict_types=1);

final class Release
{
    public static function allForUser(int $userId): array
    {
        $db = Database::connection();

        $stmt = $db->prepare(
            'SELECT
                r.*,
                a.stage_name,
                a.profile_image,
                (
                    SELECT COUNT(*)
                    FROM release_tracks rt
                    WHERE rt.release_id = r.id
                ) AS track_count,
                (
                    SELECT COUNT(*)
                    FROM release_platforms rp
                    WHERE rp.release_id = r.id
                ) AS platform_count,
                (
                    SELECT COUNT(*)
                    FROM release_platforms rp
                    WHERE rp.release_id = r.id
                    AND rp.platform_status = "live"
                ) AS live_platform_count
             FROM releases r
             LEFT JOIN artists a
                ON a.id = r.artist_id
             WHERE r.user_id = ?
             ORDER BY
                CASE r.release_status
                    WHEN "planned" THEN 1
                    WHEN "draft" THEN 2
                    WHEN "released" THEN 3
                    WHEN "archived" THEN 4
                    ELSE 5
                END,
                CASE
                    WHEN r.release_date IS NULL THEN 1
                    ELSE 0
                END,
                r.release_date ASC,
                r.updated_at DESC,
                r.id DESC'
        );

        $stmt->execute([$userId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function findForUser(
        int $releaseId,
        int $userId
    ): ?array {
        $db = Database::connection();

        $stmt = $db->prepare(
            'SELECT
                r.*,
                a.stage_name,
                a.profile_image
             FROM releases r
             LEFT JOIN artists a
                ON a.id = r.artist_id
             WHERE r.id = ?
             AND r.user_id = ?
             LIMIT 1'
        );

        $stmt->execute([
            $releaseId,
            $userId
        ]);

        $release = $stmt->fetch(PDO::FETCH_ASSOC);

        return $release ?: null;
    }

    public static function create(
        int $userId,
        array $data
    ): int {
        $db = Database::connection();

        $stmt = $db->prepare(
            'INSERT INTO releases
            (
                user_id,
                artist_id,
                artist_name,
                title,
                release_type,
                release_status,
                genre,
                language,
                release_date,
                upc,
                distributor,
                is_explicit,
                cover_art_url,
                pitch,
                short_hook,
                internal_notes
            )
            VALUES
            (
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
            )'
        );

        $stmt->execute([
            $userId,
            $data['artist_id'],
            $data['artist_name'],
            $data['title'],
            $data['release_type'],
            $data['release_status'],
            $data['genre'],
            $data['language'],
            $data['release_date'],
            $data['upc'],
            $data['distributor'],
            $data['is_explicit'],
            $data['cover_art_url'],
            $data['pitch'],
            $data['short_hook'],
            $data['internal_notes'],
        ]);

        return (int) $db->lastInsertId();
    }

    public static function update(
        int $releaseId,
        int $userId,
        array $data
    ): bool {
        $db = Database::connection();

        $stmt = $db->prepare(
            'UPDATE releases
             SET
                artist_id = ?,
                artist_name = ?,
                title = ?,
                release_type = ?,
                release_status = ?,
                genre = ?,
                language = ?,
                release_date = ?,
                upc = ?,
                distributor = ?,
                is_explicit = ?,
                cover_art_url = ?,
                pitch = ?,
                short_hook = ?,
                internal_notes = ?
             WHERE id = ?
             AND user_id = ?'
        );

        return $stmt->execute([
            $data['artist_id'],
            $data['artist_name'],
            $data['title'],
            $data['release_type'],
            $data['release_status'],
            $data['genre'],
            $data['language'],
            $data['release_date'],
            $data['upc'],
            $data['distributor'],
            $data['is_explicit'],
            $data['cover_art_url'],
            $data['pitch'],
            $data['short_hook'],
            $data['internal_notes'],
            $releaseId,
            $userId,
        ]);
    }

    public static function tracksForUser(
        int $releaseId,
        int $userId
    ): array {
        $db = Database::connection();

        $stmt = $db->prepare(
            'SELECT rt.*
             FROM release_tracks rt
             INNER JOIN releases r
                ON r.id = rt.release_id
             WHERE rt.release_id = ?
             AND r.user_id = ?
             ORDER BY
                rt.disc_number ASC,
                rt.track_number ASC,
                rt.id ASC'
        );

        $stmt->execute([
            $releaseId,
            $userId
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function platformsForUser(
        int $releaseId,
        int $userId
    ): array {
        $db = Database::connection();

        $stmt = $db->prepare(
            'SELECT rp.*
             FROM release_platforms rp
             INNER JOIN releases r
                ON r.id = rp.release_id
             WHERE rp.release_id = ?
             AND r.user_id = ?
             ORDER BY rp.platform ASC, rp.id ASC'
        );

        $stmt->execute([
            $releaseId,
            $userId
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
