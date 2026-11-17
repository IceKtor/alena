<?php
/** Сохранение основных полей профиля фотографа. */
declare(strict_types=1);
require __DIR__ . '/../bootstrap.php';
api_guard();

if (!is_role(ROLE_PHOTOGRAPHER)) {
    json_out(['ok' => false, 'error' => 'Редактировать профиль могут только фотографы.'], 403);
}

$id = (int) current_user()['id'];
$name = post_str('name');
$city = post_str('city');
$price = filter_var(post_str('price'), FILTER_VALIDATE_INT, ['options' => ['min_range' => PRICE_MIN, 'max_range' => PRICE_MAX]]);
$about = post_str('about');
$selected = $_POST['genres'] ?? [];
$allowedGenres = array_keys(genres());
$genres = is_array($selected) ? array_intersect($allowedGenres, array_filter($selected, 'is_string')) : [];
$errors = collect_errors([
    'name' => v_name($name),
    'city' => in_array($city, cities(), true) ? null : 'Выберите город из списка',
    'price' => $price === false ? 'Укажите цену от ' . PRICE_MIN . ' до ' . PRICE_MAX . ' ₽' : null,
    'about' => mb_strlen($about) > 1000 ? 'До 1000 символов' : null,
]);
if ($errors) {
    json_out(['ok' => false, 'errors' => $errors], 422);
}

$pdo = db();
$pdo->beginTransaction();
try {
    db_query('UPDATE users SET name = ? WHERE id = ?', [$name, $id]);
    db_query('UPDATE photographer_profiles SET city = ?, price = ?, about = ? WHERE user_id = ?',
        [$city, $price, $about, $id]);
    db_query('DELETE FROM photographer_genres WHERE photographer_id = ?', [$id]);
    foreach ($genres as $position => $genre) {
        db_query('INSERT INTO photographer_genres (photographer_id, genre, position) VALUES (?, ?, ?)',
            [$id, $genre, $position]);
    }
    $pdo->commit();
} catch (Throwable $error) {
    $pdo->rollBack();
    throw $error;
}
$_SESSION['account']['name'] = $name;
json_out(['ok' => true, 'redirect' => 'cabinet.php?section=profile']);
