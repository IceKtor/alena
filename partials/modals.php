<?php
/**
 * Модальные окна: «Вход», «Создать профиль», «Написать фотографу», «Новая работа» и просмотр фото.
 * Закрываются кнопкой «×», клавишей Esc и «Отмена». Формы отправляются в server/api/*.php через js/app.js.
 * На мобильных экранах окна открываются нижним листом (стили в css/style.css).
 */
$genreOptions = genres();
$close = '<button type="button" class="md__close" data-close-modal aria-label="Закрыть">' . icon('x') . '</button>';
$genreSelect = function (string $id) use ($genreOptions): string {
    $o = '<option value="">Выберите</option>';
    foreach ($genreOptions as $slug => $label) {
        $o .= '<option value="' . e($slug) . '">' . e($label) . '</option>';
    }
    return '<div class="v v--select"><select id="' . $id . '" name="genre" required>' . $o . '</select>' . icon('chevron') . '</div>';
};
?>

<!-- Вход (ФТ-08) -->
<dialog class="dlg" id="modal-login" aria-labelledby="login-title">
  <div class="md">
    <?= $close ?>
    <h2 id="login-title">Вход</h2>
    <p class="lead2" data-login-lead>Войдите, чтобы писать фотографам и вести портфолио.</p>
    <form class="form" data-api="server/api/login.php" novalidate>
      <div class="field">
        <label for="login-email"><small>Почта</small></label>
        <div class="v"><input id="login-email" name="email" type="email" autocomplete="email" placeholder="name@mail.ru" required></div>
        <p class="err" data-error-for="email" role="alert"></p>
      </div>
      <div class="field">
        <div class="field__row"><label for="login-pass"><small>Пароль</small></label><button type="button" class="text-link" data-stub="Восстановление пароля появится вместе с базой данных">Забыли пароль?</button></div>
        <div class="v"><input id="login-pass" name="password" type="password" autocomplete="current-password" required></div>
        <p class="err" data-error-for="password" role="alert"></p>
      </div>
      <p class="form__error" data-form-error role="alert"></p>
      <button type="submit" class="btn btn--primary btn--block">Войти</button>
      <p class="or">или</p>
      <!-- ЗАГЛУШКА (ФТ-18, желательное): вход по коду из письма, нужна почтовая отправка -->
      <button type="button" class="btn btn--block" data-stub="Вход по коду из письма появится позже (ФТ-18)">Войти по коду из письма</button>
      <p class="sw">Нет аккаунта? <button type="button" class="text-link" data-open-modal="register">Создать профиль</button></p>
      <p class="hint">Демо-доступ: foto@demo.ru (фотограф) или client@demo.ru (заказчик), пароль demo12345.</p>
    </form>
  </div>
</dialog>

<!-- Создать профиль (ФТ-08) -->
<dialog class="dlg" id="modal-register" aria-labelledby="reg-title">
  <div class="md">
    <?= $close ?>
    <h2 id="reg-title">Создать профиль</h2>
    <p class="lead2">Выберите, как вы будете пользоваться Объективом.</p>
    <form class="form" data-api="server/api/register.php" novalidate>
      <fieldset class="roles">
        <legend class="sr-only">Роль</legend>
        <label class="role">
          <input type="radio" name="role" value="photographer" checked>
          <span class="role__box"><?= icon('image') ?><strong>Я фотограф</strong><small>Размещаю портфолио</small></span>
        </label>
        <label class="role">
          <input type="radio" name="role" value="client">
          <span class="role__box"><?= icon('search') ?><strong>Я заказчик</strong><small>Ищу исполнителя</small></span>
        </label>
      </fieldset>
      <div class="field">
        <label for="reg-name"><small>Имя</small></label>
        <div class="v"><input id="reg-name" name="name" type="text" autocomplete="name" placeholder="Алексей Петров" maxlength="50" required></div>
        <p class="err" data-error-for="name" role="alert"></p>
      </div>
      <div class="field">
        <label for="reg-email"><small>Почта</small></label>
        <div class="v"><input id="reg-email" name="email" type="email" autocomplete="email" placeholder="name@mail.ru" required></div>
        <p class="err" data-error-for="email" role="alert"></p>
      </div>
      <div class="field">
        <label for="reg-pass"><small>Пароль</small></label>
        <div class="v"><input id="reg-pass" name="password" type="password" autocomplete="new-password" placeholder="Не короче 8 символов" required></div>
        <p class="err" data-error-for="password" role="alert"></p>
      </div>
      <div class="field">
        <label class="chk"><input type="checkbox" name="consent" value="1"><s><?= icon('check') ?></s><span>Согласен с <a href="privacy.php" target="_blank" rel="noopener">правилами платформы</a></span></label>
        <p class="err" data-error-for="consent" role="alert"></p>
      </div>
      <p class="form__error" data-form-error role="alert"></p>
      <button type="submit" class="btn btn--primary btn--block">Создать профиль</button>
      <p class="sw">Уже есть аккаунт? <button type="button" class="text-link" data-open-modal="login">Войти</button></p>
    </form>
  </div>
</dialog>

<!-- Написать фотографу (ФТ-07) -->
<dialog class="dlg" id="modal-request" aria-labelledby="req-title">
  <div class="md md--wide">
    <?= $close ?>
    <div class="who2">
      <img src="" alt="" width="60" height="60" data-req-avatar hidden>
      <div>
        <h2 id="req-title">Написать <span data-req-name>фотографу</span></h2>
        <span class="mt" style="font-size:13.5px">Обычно отвечает в течение дня</span>
      </div>
    </div>
    <form class="form" data-api="server/api/request.php" novalidate>
      <input type="hidden" name="photographer_id" value="">
      <div class="field">
        <label for="req-name-f"><small>Ваше имя</small></label>
        <div class="v"><input id="req-name-f" name="name" type="text" autocomplete="name" maxlength="50" required></div>
        <p class="err" data-error-for="name" role="alert"></p>
      </div>
      <div class="field">
        <label for="req-contact"><small>Телефон или почта</small></label>
        <div class="v"><input id="req-contact" name="contact" type="text" autocomplete="email" placeholder="+7 900 000-00-00" required></div>
        <p class="err" data-error-for="contact" role="alert"></p>
      </div>
      <div class="form-row keep">
        <div class="field">
          <label for="req-date"><small>Дата съёмки</small></label>
          <div class="v"><input id="req-date" name="date" type="date" min="<?= date('Y-m-d') ?>" required></div>
          <p class="err" data-error-for="date" role="alert"></p>
        </div>
        <div class="field">
          <label for="req-genre"><small>Жанр</small></label>
          <?= $genreSelect('req-genre') ?>
          <p class="err" data-error-for="genre" role="alert"></p>
        </div>
      </div>
      <div class="field">
        <label for="req-msg"><small>Сообщение</small></label>
        <div class="v v--area"><textarea id="req-msg" name="message" rows="4" maxlength="1000" placeholder="Расскажите, что и где снимаем"></textarea></div>
        <p class="err" data-error-for="message" role="alert"></p>
      </div>
      <div class="field">
        <label class="chk"><input type="checkbox" name="consent" value="1"><s><?= icon('check') ?></s><span>Согласен на <a href="privacy.php" target="_blank" rel="noopener">обработку данных</a></span></label>
        <p class="err" data-error-for="consent" role="alert"></p>
      </div>
      <p class="hint">Заявка не является договором или оплатой. Цены ориентировочные.</p>
      <p class="form__error" data-form-error role="alert"></p>
      <button type="submit" class="btn btn--primary btn--block">Отправить заявку</button>
    </form>
  </div>
</dialog>

<!-- Новая работа (ФТ-10) -->
<dialog class="dlg" id="modal-work" aria-labelledby="work-title">
  <div class="md md--wide">
    <?= $close ?>
    <h2 id="work-title">Новая работа</h2>
    <form class="form" data-api="server/api/work.php" enctype="multipart/form-data" novalidate>
      <div class="upload-preview" data-preview hidden>
        <img src="" alt="Предпросмотр выбранного файла" width="96" height="84" data-preview-img>
        <div class="upload-preview__info">
          <strong data-preview-name></strong>
          <span class="mt" data-preview-size></span>
          <span class="progress"><span class="progress__bar" data-progress></span></span>
          <span class="upload-preview__status"><?= icon('check') ?><span data-preview-status>Готово к публикации</span></span>
        </div>
      </div>
      <div class="field">
        <label class="btn btn--block" for="work-file">Выбрать файл</label>
        <input class="sr-only" id="work-file" name="file" type="file" accept="image/jpeg,image/png">
        <p class="err" data-error-for="file" role="alert"></p>
      </div>
      <div class="field">
        <label for="work-title-f"><small>Название</small></label>
        <div class="v"><input id="work-title-f" name="title" type="text" maxlength="100" required></div>
        <p class="err" data-error-for="title" role="alert"></p>
      </div>
      <div class="form-row keep">
        <div class="field">
          <label for="work-genre"><small>Жанр</small></label>
          <?= $genreSelect('work-genre') ?>
          <p class="err" data-error-for="genre" role="alert"></p>
        </div>
        <div class="field">
          <label for="work-tags"><small>Теги</small></label>
          <div class="v"><input id="work-tags" name="tags" type="text" placeholder="статуя, небо, ч/б"></div>
          <p class="err" data-error-for="tags" role="alert"></p>
        </div>
      </div>
      <label class="tg"><input type="checkbox" name="public" value="1" checked><s></s><span>Показывать в портфолио</span></label>
      <label class="tg"><input type="checkbox" name="download" value="1"><s></s><span>Разрешить скачивание</span></label>
      <p class="hint">Загружая фото, вы подтверждаете, что у вас есть права на изображение.</p>
      <p class="form__error" data-form-error role="alert"></p>
      <div class="mbt">
        <button type="button" class="btn" data-close-modal>Отмена</button>
        <button type="submit" class="btn btn--primary">Опубликовать</button>
      </div>
    </form>
  </div>
</dialog>

<!-- Просмотр фото крупно (ФТ-19, желательное) -->
<dialog class="lightbox" id="lightbox" aria-label="Просмотр фото">
  <figure>
    <div class="lightbox__top"><span class="sr-only">Просмотр фото</span><button type="button" class="md__close" data-close-modal aria-label="Закрыть"><?= icon('x') ?></button></div>
    <img src="" alt="" width="1200" height="800">
    <figcaption></figcaption>
  </figure>
</dialog>
