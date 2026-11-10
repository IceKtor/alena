<?php
/**
 * Модальные окна (ТЗ 10.7): «Вход», «Создать профиль», «Написать фотографу», «Новая работа».
 * Закрываются кнопкой «×», клавишей Esc и «Отмена». Формы отправляются в server/api/*.php через js/app.js.
 */
$genreOptions = genres();
?>

<!-- Вход (ФТ-08) -->
<dialog class="modal" id="modal-login" aria-labelledby="login-title">
  <div class="modal__box">
    <button type="button" class="modal__close icon-btn" data-close-modal aria-label="Закрыть"><?= icon('x') ?></button>
    <h2 class="modal__title" id="login-title">Вход</h2>
    <p class="muted modal__lead" data-login-lead>Войдите, чтобы писать фотографам и вести портфолио.</p>
    <form class="form" data-api="server/api/login.php" novalidate>
      <div class="field">
        <label for="login-email">Почта</label>
        <span class="input"><?= icon('mail') ?><input id="login-email" name="email" type="email" autocomplete="email" placeholder="name@mail.ru" required></span>
        <p class="field__error" data-error-for="email" role="alert"></p>
      </div>
      <div class="field">
        <div class="field__row"><label for="login-pass">Пароль</label><button type="button" class="text-link" data-stub="Восстановление пароля появится вместе с базой данных">Забыли пароль?</button></div>
        <span class="input"><input id="login-pass" name="password" type="password" autocomplete="current-password" required></span>
        <p class="field__error" data-error-for="password" role="alert"></p>
      </div>
      <p class="form__error" data-form-error role="alert"></p>
      <button type="submit" class="btn btn--primary btn--block">Войти</button>
      <p class="or"><span>или</span></p>
      <!-- ЗАГЛУШКА (ФТ-18, желательное): вход по коду из письма, нужна почтовая отправка -->
      <button type="button" class="btn btn--ghost btn--block" data-stub="Вход по коду из письма появится позже (ФТ-18)">Войти по коду из письма</button>
      <p class="modal__switch">Нет аккаунта? <button type="button" class="text-link" data-open-modal="register">Создать профиль</button></p>
      <p class="hint">Демо-доступ: foto@demo.ru (фотограф) или client@demo.ru (заказчик), пароль demo12345.</p>
    </form>
  </div>
</dialog>

<!-- Создать профиль (ФТ-08) -->
<dialog class="modal" id="modal-register" aria-labelledby="reg-title">
  <div class="modal__box">
    <button type="button" class="modal__close icon-btn" data-close-modal aria-label="Закрыть"><?= icon('x') ?></button>
    <h2 class="modal__title" id="reg-title">Создать профиль</h2>
    <p class="muted modal__lead">Выберите, как вы будете пользоваться Объективом.</p>
    <form class="form" data-api="server/api/register.php" novalidate>
      <fieldset class="roles">
        <legend class="sr-only">Роль</legend>
        <label class="role">
          <input type="radio" name="role" value="photographer" checked>
          <span class="role__box"><?= icon('camera') ?><strong>Я фотограф</strong><small>Размещаю портфолио</small></span>
        </label>
        <label class="role">
          <input type="radio" name="role" value="client">
          <span class="role__box"><?= icon('search') ?><strong>Я заказчик</strong><small>Ищу исполнителя</small></span>
        </label>
      </fieldset>
      <div class="field">
        <label for="reg-name">Имя</label>
        <span class="input"><input id="reg-name" name="name" type="text" autocomplete="name" placeholder="Алексей Петров" maxlength="50" required></span>
        <p class="field__error" data-error-for="name" role="alert"></p>
      </div>
      <div class="field">
        <label for="reg-email">Почта</label>
        <span class="input"><?= icon('mail') ?><input id="reg-email" name="email" type="email" autocomplete="email" placeholder="name@mail.ru" required></span>
        <p class="field__error" data-error-for="email" role="alert"></p>
      </div>
      <div class="field">
        <label for="reg-pass">Пароль</label>
        <span class="input"><input id="reg-pass" name="password" type="password" autocomplete="new-password" placeholder="Не короче 8 символов" required></span>
        <p class="field__error" data-error-for="password" role="alert"></p>
      </div>
      <div class="field">
        <label class="check"><input type="checkbox" name="consent" value="1"><span class="check__box"><?= icon('check') ?></span><span>Согласен с <a href="privacy.php" target="_blank" rel="noopener">правилами платформы</a></span></label>
        <p class="field__error" data-error-for="consent" role="alert"></p>
      </div>
      <p class="form__error" data-form-error role="alert"></p>
      <button type="submit" class="btn btn--primary btn--block">Создать профиль</button>
      <p class="modal__switch">Уже есть аккаунт? <button type="button" class="text-link" data-open-modal="login">Войти</button></p>
    </form>
  </div>
</dialog>

<!-- Написать фотографу (ФТ-07) -->
<dialog class="modal" id="modal-request" aria-labelledby="req-title">
  <div class="modal__box">
    <button type="button" class="modal__close icon-btn" data-close-modal aria-label="Закрыть"><?= icon('x') ?></button>
    <div class="modal__head">
      <img class="avatar" src="" alt="" width="56" height="56" data-req-avatar hidden>
      <div>
        <h2 class="modal__title" id="req-title">Написать <span data-req-name>фотографу</span></h2>
        <p class="muted modal__lead">Обычно отвечает в течение дня</p>
      </div>
    </div>
    <form class="form" data-api="server/api/request.php" novalidate>
      <input type="hidden" name="photographer_id" value="">
      <div class="field">
        <label for="req-name-f">Ваше имя</label>
        <span class="input"><input id="req-name-f" name="name" type="text" autocomplete="name" maxlength="50" required></span>
        <p class="field__error" data-error-for="name" role="alert"></p>
      </div>
      <div class="field">
        <label for="req-contact">Телефон или почта</label>
        <span class="input"><input id="req-contact" name="contact" type="text" autocomplete="email" placeholder="+7 900 000-00-00" required></span>
        <p class="field__error" data-error-for="contact" role="alert"></p>
      </div>
      <div class="field-row">
        <div class="field">
          <label for="req-date">Дата съёмки</label>
          <span class="input"><input id="req-date" name="date" type="date" min="<?= date('Y-m-d') ?>" required></span>
          <p class="field__error" data-error-for="date" role="alert"></p>
        </div>
        <div class="field">
          <label for="req-genre">Жанр</label>
          <span class="input input--select"><select id="req-genre" name="genre" required>
            <option value="">Выберите</option>
            <?php foreach ($genreOptions as $slug => $label): ?><option value="<?= e($slug) ?>"><?= e($label) ?></option><?php endforeach; ?>
          </select><?= icon('chevron', 'icon--sm') ?></span>
          <p class="field__error" data-error-for="genre" role="alert"></p>
        </div>
      </div>
      <div class="field">
        <label for="req-msg">Сообщение</label>
        <span class="input input--area"><textarea id="req-msg" name="message" rows="4" maxlength="1000" placeholder="Расскажите, что и где снимаем"></textarea></span>
        <p class="field__error" data-error-for="message" role="alert"></p>
      </div>
      <div class="field">
        <label class="check"><input type="checkbox" name="consent" value="1"><span class="check__box"><?= icon('check') ?></span><span>Согласен на <a href="privacy.php" target="_blank" rel="noopener">обработку данных</a></span></label>
        <p class="field__error" data-error-for="consent" role="alert"></p>
      </div>
      <p class="note">Заявка не является договором или оплатой. Цены фотографов ориентировочные.</p>
      <p class="form__error" data-form-error role="alert"></p>
      <button type="submit" class="btn btn--primary btn--block">Отправить заявку</button>
    </form>
  </div>
</dialog>

<!-- Новая работа (ФТ-10) -->
<dialog class="modal modal--wide" id="modal-work" aria-labelledby="work-title">
  <div class="modal__box">
    <button type="button" class="modal__close icon-btn" data-close-modal aria-label="Закрыть"><?= icon('x') ?></button>
    <h2 class="modal__title" id="work-title">Новая работа</h2>
    <form class="form" data-api="server/api/work.php" enctype="multipart/form-data" novalidate>
      <div class="upload-preview" data-preview hidden>
        <img src="" alt="Предпросмотр выбранного файла" width="96" height="84" data-preview-img>
        <div class="upload-preview__info">
          <strong data-preview-name></strong>
          <span class="muted" data-preview-size></span>
          <span class="progress"><span class="progress__bar" data-progress></span></span>
          <span class="upload-preview__status"><?= icon('check') ?><span data-preview-status>Готово к публикации</span></span>
        </div>
      </div>
      <div class="field">
        <label class="btn btn--ghost btn--block" for="work-file">Выбрать файл</label>
        <input class="sr-only" id="work-file" name="file" type="file" accept="image/jpeg,image/png">
        <p class="field__error" data-error-for="file" role="alert"></p>
      </div>
      <div class="field">
        <label for="work-title-f">Название</label>
        <span class="input"><input id="work-title-f" name="title" type="text" maxlength="100" required></span>
        <p class="field__error" data-error-for="title" role="alert"></p>
      </div>
      <div class="field-row">
        <div class="field">
          <label for="work-genre">Жанр</label>
          <span class="input input--select"><select id="work-genre" name="genre" required>
            <option value="">Выберите</option>
            <?php foreach ($genreOptions as $slug => $label): ?><option value="<?= e($slug) ?>"><?= e($label) ?></option><?php endforeach; ?>
          </select><?= icon('chevron', 'icon--sm') ?></span>
          <p class="field__error" data-error-for="genre" role="alert"></p>
        </div>
        <div class="field">
          <label for="work-tags">Теги</label>
          <span class="input"><input id="work-tags" name="tags" type="text" placeholder="статуя, небо, чёрно-белое"></span>
          <p class="field__error" data-error-for="tags" role="alert"></p>
        </div>
      </div>
      <label class="switch"><input type="checkbox" name="public" value="1" checked><span class="switch__track"></span><span>Показывать в портфолио</span></label>
      <label class="switch"><input type="checkbox" name="download" value="1"><span class="switch__track"></span><span>Разрешить скачивание</span></label>
      <p class="hint">Загружая фото, вы подтверждаете, что у вас есть права на изображение.</p>
      <p class="form__error" data-form-error role="alert"></p>
      <div class="modal__buttons">
        <button type="button" class="btn btn--ghost" data-close-modal>Отмена</button>
        <button type="submit" class="btn btn--primary">Опубликовать</button>
      </div>
    </form>
  </div>
</dialog>

<!-- Просмотр фото крупно (ФТ-19, желательное) -->
<dialog class="lightbox" id="lightbox" aria-label="Просмотр фото">
  <button type="button" class="modal__close icon-btn" data-close-modal aria-label="Закрыть"><?= icon('x') ?></button>
  <figure><img src="" alt=""><figcaption></figcaption></figure>
</dialog>
