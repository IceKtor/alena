<?php
/** Страница «Работы»: общая лента свежих работ (ФТ-06). */
declare(strict_types=1);
require __DIR__ . '/server/bootstrap.php';

$genreMap = [ // метки жанров ленты (ТЗ 10.5); «Свадьба» = «Свадебная»
    'portrait' => 'Портрет', 'landscape' => 'Пейзаж', 'reportage' => 'Репортаж',
    'still' => 'Предметная', 'wedding' => 'Свадьба',
];
$genre = (string) ($_GET['genre'] ?? '');
$genre = isset($genreMap[$genre]) ? $genre : '';
$shown = max(WORKS_PER_STEP, min(500, (int) ($_GET['shown'] ?? WORKS_PER_STEP)));
$shown = (int) (ceil($shown / WORKS_PER_STEP) * WORKS_PER_STEP);

$all   = all_works($genre ?: null);
$items = array_slice($all, 0, $shown);
$more  = count($all) > $shown;

page_start([
    'title'       => 'Работы',
    'description' => 'Лента свежих работ фотографов Якутии: портреты, пейзажи, репортажи. Выбирайте исполнителя по стилю.',
    'nav'         => 'works',
]);
require __DIR__ . '/partials/header.php';
?>

<section class="section section--page">
  <div class="container">
    <div class="page-head">
      <div>
        <h1>Работы</h1>
        <p class="muted">Лента обновляется ежедневно — самые новые публикации первыми.</p>
      </div>
      <!-- ЗАГЛУШКА: сортировка одна («Сначала новые»), остальные появятся с БД -->
      <span class="input input--select input--pill"><select aria-label="Сортировка" disabled><option>Сначала новые</option></select><?= icon('chevron', 'icon--sm') ?></span>
    </div>

    <nav class="chips" aria-label="Жанры">
      <a class="chip<?= $genre === '' ? ' chip--on' : '' ?>" href="works.php"<?= $genre === '' ? ' aria-current="true"' : '' ?>>Все</a>
      <?php foreach ($genreMap as $slug => $label): ?>
        <a class="chip<?= $genre === $slug ? ' chip--on' : '' ?>" href="<?= e(url('works.php', ['genre' => $slug])) ?>"<?= $genre === $slug ? ' aria-current="true"' : '' ?>><?= e($label) ?></a>
      <?php endforeach; ?>
    </nav>

    <?php if (!$items): ?>
      <div class="empty card"><h2>Работ пока нет</h2><p class="muted">В этом жанре пока ничего не опубликовано.</p><a class="btn btn--primary" href="works.php">Показать все</a></div>
    <?php else: ?>
      <div class="grid grid--3">
        <?php foreach ($items as $w): ?><?= work_card($w, true, $w['hours_ago'] < 24, true) ?><?php endforeach; ?>
      </div>
      <?php if ($more): ?>
        <div class="center"><a class="btn btn--ghost" href="<?= e(url('works.php', ['genre' => $genre, 'shown' => $shown + WORKS_PER_STEP])) ?>#after-<?= $shown ?>">Показать ещё</a></div>
      <?php endif; ?>
    <?php endif; ?>
  </div>
</section>

<section class="cta">
  <div class="container cta__inner">
    <h2>Хотите, чтобы вас увидели?</h2>
    <button type="button" class="btn btn--dark btn--lg" data-open-modal="register">Создать профиль</button>
  </div>
</section>

<?php require __DIR__ . '/partials/footer.php'; ?>
