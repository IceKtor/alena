<?php
/** Избранное заказчика (ФТ-13). */
declare(strict_types=1);
require __DIR__ . '/server/bootstrap.php';

page_start(['title' => 'Избранное', 'noindex' => true]);
$user = current_user();
$list = [];
if (is_role(ROLE_CLIENT)) {
    foreach (favorites() as $id) { // TODO(DB): SELECT … FROM favorites JOIN photographer_profiles
        if ($p = photographer_by_id($id)) {
            $list[] = $p;
        }
    }
}
require __DIR__ . '/partials/header.php';
?>
<section class="section section--page">
  <div class="container">
    <h1>Избранное</h1>
    <?php if (!$user): ?>
      <div class="card card--pad empty"><p class="muted">Войдите, чтобы видеть избранных фотографов.</p>
        <button type="button" class="btn btn--primary" data-open-modal="login">Войти</button></div>
    <?php elseif (!is_role(ROLE_CLIENT)): ?>
      <div class="card card--pad empty"><p class="muted">Избранное доступно заказчикам.</p></div>
    <?php elseif (!$list): ?>
      <div class="card card--pad empty"><p class="muted">Пока никого нет. Нажмите «сердце» на карточке фотографа.</p>
        <a class="btn btn--primary" href="photographers.php">Найти фотографа</a></div>
    <?php else: ?>
      <div class="grid grid--2"><?php foreach ($list as $p): ?><?= photographer_card($p) ?><?php endforeach; ?></div>
    <?php endif; ?>
  </div>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
