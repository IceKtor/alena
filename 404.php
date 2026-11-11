<?php
/** Страница ошибки 404 (ТЗ 7). Подключается из server/helpers.php::not_found() и router.php. */
declare(strict_types=1);
require_once __DIR__ . '/server/bootstrap.php';
http_response_code(404);
page_start(['title' => 'Страница не найдена', 'noindex' => true]);
require __DIR__ . '/partials/header.php';
?>
<section class="nf">
  <div class="big" aria-hidden="true">404</div>
  <h1>Кадр не в фокусе</h1>
  <p>Такой страницы нет: адрес набран неверно или страницу удалили.</p>
  <a class="btn btn--primary" href="index.php">На главную</a>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
