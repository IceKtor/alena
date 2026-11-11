<?php
/** Страница «О платформе» (ФТ-06). */
declare(strict_types=1);
require __DIR__ . '/server/bootstrap.php';

$stats = platform_stats();
page_start([
    'title'       => 'О платформе',
    'description' => 'Объектив — платформа, где кадры говорят за фотографа: бесплатное портфолио, поиск по жанру, городу и цене, заявки напрямую.',
    'nav'         => 'about',
]);
require __DIR__ . '/partials/header.php';
?>

<section class="ph abt" style="padding-top:72px">
  <h1>Каталог фотографов Якутии. Заказчик выбирает по снимкам, фотограф не тратит время на сайт.</h1>
</section>

<dl class="facts">
  <div><dt>фотографов</dt><dd><?= $stats['photographers'] ?></dd></div>
  <div><dt>работ в ленте</dt><dd><?= $stats['works'] ?></dd></div>
  <div><dt>жанров съёмки</dt><dd><?= $stats['genres'] ?></dd></div>
  <div><dt>стоит размещение</dt><dd>0 ₽</dd></div>
</dl>

<section class="two" id="rules">
  <div>
    <h2>Если вы ищете фотографа</h2>
    <p>Выберите город и жанр, укажите бюджет и посмотрите работы. Понравился стиль — напишите прямо из профиля. Регистрироваться для этого не нужно.</p>
    <p>Хотите вернуться к кому-то позже — добавьте в избранное (для этого нужна регистрация заказчика).</p>
  </div>
  <div>
    <h2>Если вы фотограф</h2>
    <p>Заведите профиль, загрузите работы, укажите цены «от» и свободные даты. Заявки собираются в личном кабинете, там же видно, сколько человек смотрели ваш профиль.</p>
    <p>Загружайте только то, что сняли сами или на что у вас есть права.</p>
  </div>
</section>
<p class="note" style="padding:14px var(--pad) 0;max-width:900px">Платформа только знакомит фотографа и заказчика: она не участвует в расчётах, не отвечает за качество и сроки и не заменяет договор. Цены — ориентировочные.</p>

<section class="contact" id="contacts">
  <div>
    <h2>Контакты</h2>
    <!-- ЗАГЛУШКА: контакты условные, заменить реальными -->
    <ul class="ci">
      <li><?= icon('mail') ?><span><b>Почта</b><a href="mailto:hello@obyektiv.example">hello@obyektiv.example</a></span></li>
      <li><?= icon('phone') ?><span><b>Телефон</b>+7 900 000-00-00</span></li>
      <li><?= icon('pin') ?><span><b>Адрес</b>г. Якутск, ул. Кулаковского, 48</span></li>
      <li><?= icon('clock') ?><span><b>Время ответа</b>В течение рабочего дня</span></li>
    </ul>
  </div>
  <div>
    <h2>Написать нам</h2>
    <form class="form" data-api="server/api/contact.php" data-reset-on-success novalidate>
      <div class="field">
        <label for="c-name"><small>Имя</small></label>
        <div class="v"><input id="c-name" name="name" type="text" autocomplete="name" maxlength="50" placeholder="Ваше имя" required></div>
        <p class="err" data-error-for="name" role="alert"></p>
      </div>
      <div class="field">
        <label for="c-email"><small>Почта</small></label>
        <div class="v"><input id="c-email" name="email" type="email" autocomplete="email" placeholder="name@mail.ru" required></div>
        <p class="err" data-error-for="email" role="alert"></p>
      </div>
      <div class="field">
        <label for="c-msg"><small>Сообщение</small></label>
        <div class="v v--area"><textarea id="c-msg" name="message" rows="4" maxlength="1000" placeholder="О чём хотите написать" required></textarea></div>
        <p class="err" data-error-for="message" role="alert"></p>
      </div>
      <div class="field">
        <label class="chk"><input type="checkbox" name="consent" value="1"><s><?= icon('check') ?></s><span>Согласен на <a href="privacy.php">обработку данных</a></span></label>
        <p class="err" data-error-for="consent" role="alert"></p>
      </div>
      <p class="form__error" data-form-error role="alert"></p>
      <div><button type="submit" class="btn btn--primary">Отправить</button></div>
    </form>
  </div>
</section>

<?php require __DIR__ . '/partials/footer.php'; ?>
