<?php
/** Страница ошибки 404 (ТЗ 7). Подключается из server/helpers.php::not_found() и router.php. */
declare(strict_types=1);
require_once __DIR__ . '/server/bootstrap.php';
http_response_code(404);
page_start(['title' => 'Страница не найдена', 'noindex' => true]);
require __DIR__ . '/partials/header.php';
?>
<section class="section section--page">
  <div class="container">
    <div class="card card--pad empty empty--big">
      <p class="mono error-code">404</p>
      <h1>Кадр не в фокусе</h1>
      <p class="muted">Такой страницы нет — возможно, адрес набран неверно или страница удалена.</p>
      <a class="btn btn--primary" href="index.php">На главную</a>
    </div>
  </div>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
