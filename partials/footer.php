</main>

<?= stripe() ?>
<footer class="site-footer">
  <div class="container">
    <div class="site-footer__grid">
      <div class="site-footer__brand">
        <a class="logo logo--light" href="index.php"><?= logo() ?></a>
        <p><?= e(APP_TAGLINE) ?></p>
      </div>
      <nav aria-label="Заказчикам">
        <h2 class="site-footer__title">Заказчикам</h2>
        <ul>
          <li><a href="photographers.php">Найти фотографа</a></li>
          <li><a href="works.php">Свежие работы</a></li>
          <li><a href="index.php#how">Как выбрать</a></li>
        </ul>
      </nav>
      <nav aria-label="Фотографам">
        <h2 class="site-footer__title">Фотографам</h2>
        <ul>
          <li><button type="button" class="footer-link" data-open-modal="register">Разместить портфолио</button></li>
          <li><a href="cabinet.php">Личный кабинет</a></li>
          <li><a href="about.php#rules">Советы по съёмке</a></li>
        </ul>
      </nav>
      <nav aria-label="Платформа">
        <h2 class="site-footer__title">Платформа</h2>
        <ul>
          <li><a href="about.php">О проекте</a></li>
          <li><a href="about.php#rules">Правила</a></li>
          <li><a href="about.php#contacts">Контакты</a></li>
          <li><a href="privacy.php">Политика ПДн</a></li>
        </ul>
      </nav>
    </div>
    <p class="site-footer__legal">© 2026 <?= e(APP_NAME) ?>. Учебный проект: тексты, фото и данные условные.</p>
  </div>
</footer>

<?php require __DIR__ . '/modals.php'; ?>
<div class="toast" role="status" aria-live="polite" hidden></div>
</body>
</html>
