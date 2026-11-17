<?php
declare(strict_types=1);

function genres(): array
{
    static $genres;
    return $genres ??= db_query('SELECT slug, name FROM genres ORDER BY position')->fetchAll(PDO::FETCH_KEY_PAIR);
}

function cities(): array
{
    static $cities;
    return $cities ??= db_query('SELECT name FROM cities ORDER BY position')->fetchAll(PDO::FETCH_COLUMN);
}

function photographer_rows(?int $id = null): array
{
    $sql = "SELECT u.id, u.name, p.city, p.price, p.experience AS exp, p.night, p.video, p.trip,
                   p.avatar, p.about, UNIX_TIMESTAMP(u.created_at) AS registered_at,
                   (SELECT GROUP_CONCAT(g.genre ORDER BY g.position SEPARATOR ',')
                    FROM photographer_genres g WHERE g.photographer_id = u.id) AS genres
            FROM photographer_profiles p JOIN users u ON u.id = p.user_id";
    $rows = $id === null ? db_query($sql . ' ORDER BY u.id') : db_query($sql . ' WHERE u.id = ?', [$id]);
    return array_map(static function (array $row): array {
        foreach (['id', 'price', 'exp'] as $key) {
            $row[$key] = (int) $row[$key];
        }
        foreach (['night', 'video', 'trip'] as $key) {
            $row[$key] = (bool) $row[$key];
        }
        $row['created'] = -(int) $row['registered_at'];
        $row['genres'] = $row['genres'] === null ? [] : explode(',', $row['genres']);
        return $row;
    }, $rows->fetchAll());
}

function photographers(): array
{
    static $profiles;
    return $profiles ??= photographer_rows();
}

function photographer_by_id(int $id): ?array
{
    return photographer_rows($id)[0] ?? null;
}

function photographer_services(array $profile): array
{
    return db_query('SELECT title, price FROM services WHERE photographer_id = ? ORDER BY id', [$profile['id']])->fetchAll();
}

function photographer_dates(int $id): array
{
    return db_query('SELECT available_on FROM available_dates
                     WHERE photographer_id = ? AND available_on >= CURRENT_DATE
                     ORDER BY available_on LIMIT 12', [$id])->fetchAll(PDO::FETCH_COLUMN);
}

function work_rows(string $clause, array $params): array
{
    $rows = db_query("SELECT w.id, w.title, w.genre, w.sample_file, w.is_public, w.allow_download, w.views,
                            u.id AS author_id, u.name AS author,
                            GREATEST(0, TIMESTAMPDIFF(HOUR, w.created_at, CURRENT_TIMESTAMP)) AS hours_ago
                     FROM works w JOIN users u ON u.id = w.photographer_id $clause", $params)->fetchAll();
    foreach ($rows as &$row) {
        $row['file'] = $row['sample_file'] ?? ('work:' . $row['id']);
        $row['hours_ago'] = (int) $row['hours_ago'];
    }
    return $rows;
}

function photographer_works(array $profile, bool $includePrivate = false): array
{
    $visibility = $includePrivate ? '' : ' AND w.is_public = 1';
    return work_rows("WHERE w.photographer_id = ?$visibility ORDER BY w.id", [$profile['id']]);
}

function all_works(?string $genre = null): array
{
    $order = ' ORDER BY w.created_at DESC, w.id ASC';
    if ($genre === null) {
        return work_rows('WHERE w.is_public = 1' . $order, []);
    }
    return work_rows('WHERE w.is_public = 1 AND w.genre = ?' . $order, [$genre]);
}

function photographer_requests(int $id): array
{
    return db_query('SELECT id, name, contact, shoot_on AS date, genre, message, status, created_at AS created
                     FROM requests WHERE photographer_id = ? ORDER BY created_at DESC, id DESC', [$id])->fetchAll();
}

function record_profile_view(int $id): void
{
    db_query('INSERT INTO profile_views (photographer_id, viewed_on) VALUES (?, CURRENT_DATE)
              ON DUPLICATE KEY UPDATE total = total + 1', [$id]);
}

function photographer_stats(int $id): array
{
    return db_query("SELECT
        (SELECT COALESCE(SUM(total), 0) FROM profile_views
         WHERE photographer_id = ? AND viewed_on >= CURRENT_DATE - INTERVAL 1 MONTH) AS views,
        (SELECT COUNT(*) FROM requests WHERE photographer_id = ? AND status = 'new') AS requests,
        (SELECT COUNT(*) FROM requests WHERE photographer_id = ?
            AND created_at >= CURRENT_DATE - INTERVAL 1 MONTH) AS monthly_requests,
        (SELECT COUNT(*) FROM favorites WHERE photographer_id = ?) AS favorites",
        [$id, $id, $id, $id])->fetch();
}

function platform_stats(): array
{
    return db_query('SELECT
        (SELECT COUNT(*) FROM photographer_profiles) AS photographers,
        (SELECT COUNT(*) FROM works WHERE is_public = 1) AS works,
        (SELECT COUNT(*) FROM genres) AS genres')->fetch();
}

function save_request(array $data): void
{
    db_query('INSERT INTO requests (photographer_id, name, contact, shoot_on, genre, message)
              VALUES (:photographer_id, :name, :contact, :date, :genre, :message)', $data);
}

function save_feedback(array $data): void
{
    db_query('INSERT INTO feedback_messages (name, email, message) VALUES (:name, :email, :message)', $data);
}

function save_user(array $data): array
{
    $pdo = db();
    $pdo->beginTransaction();
    try {
        db_query('INSERT INTO users (name, email, role, password_hash)
                  VALUES (:name, :email, :role, :hash)', $data);
        $id = (int) $pdo->lastInsertId();
        if ($data['role'] === ROLE_PHOTOGRAPHER) {
            db_query('INSERT INTO photographer_profiles (user_id, city) VALUES (?, ?)', [$id, DEFAULT_CITY]);
        }
        $pdo->commit();
        return ['id' => $id] + $data;
    } catch (Throwable $error) {
        $pdo->rollBack();
        throw $error;
    }
}

function user_email_exists(string $email): bool
{
    return (bool) db_query('SELECT 1 FROM users WHERE email = ?', [mb_strtolower($email)])->fetchColumn();
}
