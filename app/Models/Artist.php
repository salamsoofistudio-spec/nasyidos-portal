<?php

declare(strict_types=1);

final class Artist
{
    /**
     * Get all active artists belonging to a user.
     */
    public static function allForUser(
        int $userId
    ): array {

        $db =
            Database::connection();

        $stmt = $db->prepare(
            'SELECT
                id,
                user_id,
                artist_name,
                stage_name,
                bio,
                genre,
                subgenre,
                spotify_url,
                apple_music_url,
                youtube_url,
                tiktok_url,
                instagram_url,
                facebook_url,
                profile_image,
                is_active,
                created_at,
                updated_at
             FROM artists
             WHERE user_id = ?
             AND is_active = 1
             ORDER BY artist_name ASC'
        );

        $stmt->execute([
            $userId
        ]);

        return $stmt->fetchAll(
            PDO::FETCH_ASSOC
        );
    }


    /**
     * Find one artist belonging to a user.
     */
    public static function findForUser(
        int $artistId,
        int $userId
    ): ?array {

        $db =
            Database::connection();

        $stmt = $db->prepare(
            'SELECT
                id,
                user_id,
                artist_name,
                stage_name,
                bio,
                genre,
                subgenre,
                spotify_url,
                apple_music_url,
                youtube_url,
                tiktok_url,
                instagram_url,
                facebook_url,
                profile_image,
                is_active,
                created_at,
                updated_at
             FROM artists
             WHERE id = ?
             AND user_id = ?
             LIMIT 1'
        );

        $stmt->execute([
            $artistId,
            $userId
        ]);

        $artist =
            $stmt->fetch(
                PDO::FETCH_ASSOC
            );

        return $artist ?: null;
    }


    /**
     * Create an artist.
     */
    public static function create(
        int $userId,
        array $data
    ): int {

        $db =
            Database::connection();

        $stmt = $db->prepare(
            'INSERT INTO artists
            (
                user_id,
                artist_name,
                stage_name,
                bio,
                genre,
                subgenre,
                spotify_url,
                apple_music_url,
                youtube_url,
                tiktok_url,
                instagram_url,
                facebook_url,
                profile_image,
                is_active
            )
            VALUES
            (
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1
            )'
        );

        $stmt->execute([
            $userId,
            $data['artist_name'],
            $data['stage_name'],
            $data['bio'],
            $data['genre'],
            $data['subgenre'],
            $data['spotify_url'],
            $data['apple_music_url'],
            $data['youtube_url'],
            $data['tiktok_url'],
            $data['instagram_url'],
            $data['facebook_url'],
            $data['profile_image'],
        ]);

        return (int) $db->lastInsertId();
    }


    /**
     * Update an artist belonging to a user.
     */
    public static function update(
        int $artistId,
        int $userId,
        array $data
    ): bool {

        $db =
            Database::connection();

        $stmt = $db->prepare(
            'UPDATE artists
             SET
                artist_name = ?,
                stage_name = ?,
                bio = ?,
                genre = ?,
                subgenre = ?,
                spotify_url = ?,
                apple_music_url = ?,
                youtube_url = ?,
                tiktok_url = ?,
                instagram_url = ?,
                facebook_url = ?,
                profile_image = ?
             WHERE id = ?
             AND user_id = ?'
        );

        return $stmt->execute([
            $data['artist_name'],
            $data['stage_name'],
            $data['bio'],
            $data['genre'],
            $data['subgenre'],
            $data['spotify_url'],
            $data['apple_music_url'],
            $data['youtube_url'],
            $data['tiktok_url'],
            $data['instagram_url'],
            $data['facebook_url'],
            $data['profile_image'],
            $artistId,
            $userId,
        ]);
    }
}