/* Объектив — интерфейс: модальные окна, формы, фильтры, вкладки, галерея.
   Проверка в браузере — только подсказка; обязательная проверка выполняется на сервере (ТЗ 8.4). */
(function () {
  'use strict';

  var $ = function (s, r) { return (r || document).querySelector(s); };
  var $$ = function (s, r) { return Array.prototype.slice.call((r || document).querySelectorAll(s)); };
  var csrf = ($('meta[name="csrf-token"]') || {}).content || '';

  /* ── Всплывающее сообщение ── */
  var toastTimer;
  function toast(text, isError) {
    var t = $('.toast');
    if (!t) return;
    t.textContent = text;
    t.classList.toggle('is-error', !!isError);
    t.hidden = false;
    clearTimeout(toastTimer);
    toastTimer = setTimeout(function () { t.hidden = true; }, 4200);
  }

  /* ── Запросы к серверу ── */
  function post(url, body) {
    var opts = { method: 'POST', headers: { 'X-CSRF-Token': csrf, 'Accept': 'application/json' }, credentials: 'same-origin' };
    opts.body = body instanceof FormData ? body : new URLSearchParams(body);
    return fetch(url, opts).then(function (r) {
      return r.json().catch(function () { return { ok: false, error: 'Ошибка сервера. Попробуйте позже.' }; })
        .then(function (j) { j._status = r.status; return j; });
    }).catch(function () { return { ok: false, error: 'Нет связи с сервером. Проверьте интернет.' }; });
  }

  /* ── Меню ── */
  var toggle = $('.menu-toggle'), panel = $('#site-nav');
  if (toggle && panel) {
    toggle.addEventListener('click', function () {
      var open = panel.classList.toggle('is-open');
      toggle.setAttribute('aria-expanded', open);
      toggle.setAttribute('aria-label', open ? 'Закрыть меню' : 'Открыть меню');
    });
  }
  var um = $('.user-menu__btn');
  if (um) {
    var list = $('.user-menu__list');
    um.addEventListener('click', function (e) {
      e.stopPropagation();
      var open = list.hidden;
      list.hidden = !open;
      um.setAttribute('aria-expanded', open);
    });
    document.addEventListener('click', function () { list.hidden = true; um.setAttribute('aria-expanded', 'false'); });
  }
  $$('[data-logout]').forEach(function (b) {
    b.addEventListener('click', function () {
      post('server/api/logout.php', {}).then(function () { location.href = 'index.php'; });
    });
  });

  /* ── Модальные окна (<dialog>: Esc закрывает, фокус удерживается внутри) ── */
  function openModal(name) {
    var d = $('#modal-' + name);
    if (!d) return null;
    $$('dialog[open]').forEach(function (o) { if (o !== d) o.close(); });
    if (!d.open) d.showModal();
    var first = $('input:not([type=hidden]):not([type=radio]):not([type=file]), select, textarea', d);
    if (first) setTimeout(function () { first.focus(); }, 30);
    return d;
  }
  document.addEventListener('click', function (e) {
    var openBtn = e.target.closest('[data-open-modal]');
    if (openBtn) { e.preventDefault(); openModal(openBtn.getAttribute('data-open-modal')); return; }
    var req = e.target.closest('[data-open-request]');
    if (req) { e.preventDefault(); openRequest(req); return; }
    if (e.target.closest('[data-close-modal]')) { var d = e.target.closest('dialog'); if (d) d.close(); return; }
    var stub = e.target.closest('[data-stub]');
    if (stub) { e.preventDefault(); toast(stub.getAttribute('data-stub')); return; }
    // клик по затемнению (вне окна) закрывает
    if (e.target.tagName === 'DIALOG' && e.target.open) e.target.close();
  });
  $$('[data-stub-change]').forEach(function (el) {
    el.addEventListener('change', function () { toast(el.getAttribute('data-stub-change')); });
  });
  $$('[data-stub-form]').forEach(function (f) {
    f.addEventListener('submit', function (e) { e.preventDefault(); toast(f.getAttribute('data-stub-form')); });
  });
  var auto = $('[data-autoopen]');
  if (auto) openModal(auto.getAttribute('data-open-modal'));

  function openRequest(btn) {
    var d = $('#modal-request');
    $('[name=photographer_id]', d).value = btn.getAttribute('data-id');
    $('[data-req-name]', d).textContent = (btn.getAttribute('data-name') || '').split(' ')[0] || 'фотографу';
    var av = $('[data-req-avatar]', d), src = btn.getAttribute('data-avatar');
    av.hidden = !src; if (src) av.src = src;
    clearErrors($('form', d));
    openModal('request');
  }

  /* ── Проверка полей в браузере (те же сообщения, что на сервере) ── */
  var rules = {
    name: function (v) { return /^[A-Za-zА-Яа-яЁё][A-Za-zА-Яа-яЁё \-]{1,49}$/.test(v) ? '' : 'Укажите имя от 2 до 50 букв'; },
    email: function (v) { return /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(v) ? '' : 'Укажите почту в формате name@mail.ru'; },
    password: function (v, f) {
      if (f.hasAttribute('data-login')) return v ? '' : 'Введите пароль';
      return (v.length >= 8 && /[A-Za-zА-Яа-яЁё]/.test(v) && /\d/.test(v)) ? '' : 'Пароль не короче 8 символов, с буквой и цифрой';
    },
    contact: function (v) {
      if (!v) return 'Укажите телефон или почту для связи';
      if (v.indexOf('@') > -1) return rules.email(v) ? 'Укажите телефон или почту для связи' : '';
      var d = v.replace(/\D/g, ''); if (d.length === 11 && /^[78]/.test(d)) d = d.slice(1);
      return d.length === 10 ? '' : 'Укажите телефон или почту для связи';
    },
    date: function (v) { return v && v >= new Date().toISOString().slice(0, 10) ? '' : 'Укажите дату не раньше сегодняшней'; },
    genre: function (v) { return v ? '' : 'Выберите жанр из списка'; },
    message: function (v, f) { return v.length > 1000 ? 'Сообщение не длиннее 1000 символов' : (f.id === 'c-msg' && !v.trim() ? 'Сообщение не длиннее 1000 символов' : ''); },
    title: function (v) { return v.length >= 1 && v.length <= 100 ? '' : 'Укажите название до 100 символов'; },
    tags: function (v) {
      var t = v.split(',').map(function (s) { return s.trim(); }).filter(Boolean);
      return t.length <= 10 && t.every(function (s) { return s.length <= 30; }) ? '' : 'Не более 10 тегов по 30 символов';
    }
  };

  function setError(form, name, msg) {
    var p = $('[data-error-for="' + name + '"]', form);
    var input = form.elements[name];
    if (p) p.textContent = msg || '';
    var wrap = input && input.closest && (input.closest('.input') || null);
    if (wrap) wrap.classList.toggle('is-invalid', !!msg);
    if (input && input.setAttribute && !(input instanceof RadioNodeList)) input.setAttribute('aria-invalid', msg ? 'true' : 'false');
  }
  function clearErrors(form) {
    if (!form) return;
    $$('[data-error-for]', form).forEach(function (p) { p.textContent = ''; });
    $$('.is-invalid', form).forEach(function (w) { w.classList.remove('is-invalid'); });
    var fe = $('[data-form-error]', form); if (fe) fe.textContent = '';
  }
  function clientValidate(form) {
    var first = null, bad = false;
    Array.prototype.forEach.call(form.elements, function (el) {
      if (!el.name) return;
      var msg = '';
      if (el.name === 'consent') msg = el.checked ? '' : 'Для отправки необходимо согласие';
      else if (el.name === 'file') { if (el.files.length) msg = fileError(el.files[0]); else msg = 'Допустимы файлы JPG и PNG до 10 МБ'; }
      else if (rules[el.name] && el.type !== 'hidden') {
        if (el.name === 'message' && el.id !== 'c-msg') msg = rules.message(el.value, el);
        else msg = rules[el.name](el.value.trim(), form);
      }
      if (el.name === 'password' && form.getAttribute('data-api').indexOf('login') > -1) msg = el.value ? '' : 'Введите пароль';
      setError(form, el.name, msg);
      if (msg && !first) first = el;
      if (msg) bad = true;
    });
    if (first) first.focus();
    return !bad;
  }

  /* ── Отправка форм ── */
  $$('form[data-api]').forEach(function (form) {
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      clearErrors(form);
      if (!clientValidate(form)) return;
      var btn = $('[type=submit]', form);
      btn.disabled = true;
      post(form.getAttribute('data-api'), new FormData(form)).then(function (res) {
        btn.disabled = false;
        if (res.ok) {
          var dlg = form.closest('dialog'); if (dlg) dlg.close();
          if (res.message) toast(res.message);
          if (form.hasAttribute('data-reset-on-success')) form.reset();
          else if (dlg) form.reset();
          if (res.redirect) { location.href = res.redirect; }
          else if (form.getAttribute('data-api').indexOf('login') > -1 || form.getAttribute('data-api').indexOf('register') > -1) { location.reload(); }
          resetPreview(form);
          return;
        }
        if (res.errors) {
          var firstEl = null;
          Object.keys(res.errors).forEach(function (k) { setError(form, k, res.errors[k]); if (!firstEl && form.elements[k]) firstEl = form.elements[k]; });
          if (firstEl && firstEl.focus) firstEl.focus();
        }
        if (res.error) { var fe = $('[data-form-error]', form); if (fe) fe.textContent = res.error; else toast(res.error, true); }
      });
    });
    // убираем ошибку поля при правке
    form.addEventListener('input', function (e) { if (e.target.name) setError(form, e.target.name, ''); });
  });

  /* ── Избранное (ФТ-13) ── */
  document.addEventListener('click', function (e) {
    var b = e.target.closest('[data-fav]');
    if (!b) return;
    e.preventDefault();
    post('server/api/favorite.php', { id: b.getAttribute('data-fav') }).then(function (res) {
      if (res.login) {
        var lead = $('[data-login-lead]'); if (lead) lead.textContent = 'Войдите как заказчик, чтобы добавлять фотографов в избранное.';
        openModal('login'); return;
      }
      if (!res.ok) { toast(res.error || 'Не удалось выполнить действие', true); return; }
      $$('[data-fav="' + b.getAttribute('data-fav') + '"]').forEach(function (x) {
        x.classList.toggle('is-on', res.on); x.setAttribute('aria-pressed', res.on);
      });
      toast(res.on ? 'Добавлено в избранное' : 'Убрано из избранного');
    });
  });

  /* ── Каталог: фильтры ── */
  var ft = $('.filters-toggle'), fl = $('#filters');
  if (ft && fl) {
    ft.addEventListener('click', function () {
      var open = fl.classList.toggle('is-open'); ft.setAttribute('aria-expanded', open);
    });
  }
  var range = $('[data-range]');
  if (range) {
    var min = +range.dataset.min, max = +range.dataset.max;
    var a = $('[name=price_min]', range), b = $('[name=price_max]', range), fill = $('.range__fill', range);
    var oa = $('[data-range-min]'), ob = $('[data-range-max]');
    var fmt = function (n) { return n.toLocaleString('ru-RU').replace(/\s/g, ' ') + ' ₽'; };
    var draw = function (src) {
      var x = +a.value, y = +b.value;
      if (x > y) { if (src === a) { a.value = y; x = y; } else { b.value = x; y = x; } }
      fill.style.left = ((x - min) / (max - min) * 100) + '%';
      fill.style.right = (100 - (y - min) / (max - min) * 100) + '%';
      oa.textContent = fmt(x); ob.textContent = fmt(y);
      a.style.zIndex = x > max - (max - min) / 8 ? 3 : 2;
    };
    a.addEventListener('input', function () { draw(a); }); b.addEventListener('input', function () { draw(b); });
    draw();
  }
  var filters = $('[data-filters]');
  if (filters) {
    var countBtn = $('[data-show-count]', filters), found = $('[data-found]'), t;
    var refresh = function () {
      var p = new URLSearchParams(new FormData(filters)); p.set('format', 'count');
      fetch('photographers.php?' + p.toString(), { credentials: 'same-origin' }).then(function (r) { return r.json(); }).then(function (j) {
        countBtn.textContent = j.label;
      }).catch(function () {});
    };
    filters.addEventListener('change', function () { clearTimeout(t); t = setTimeout(refresh, 150); });
    filters.addEventListener('input', function (e) { if (e.target.type === 'range') { clearTimeout(t); t = setTimeout(refresh, 250); } });
  }

  /* ── Вкладки профиля ── */
  var tabs = $$('.tabs [role=tab]');
  tabs.forEach(function (tab) {
    tab.addEventListener('click', function () { activate(tab); });
    tab.addEventListener('keydown', function (e) {
      var i = tabs.indexOf(tab), n = e.key === 'ArrowRight' ? i + 1 : e.key === 'ArrowLeft' ? i - 1 : null;
      if (n === null) return; e.preventDefault();
      var next = tabs[(n + tabs.length) % tabs.length]; activate(next); next.focus();
    });
  });
  function activate(tab) {
    tabs.forEach(function (t) {
      var on = t === tab; t.setAttribute('aria-selected', on); t.tabIndex = on ? 0 : -1;
      var p = document.getElementById(t.getAttribute('aria-controls')); if (p) p.hidden = !on;
    });
  }

  /* ── Галерея: «Показать ещё» и увеличенный просмотр (ФТ-19) ── */
  var gal = $('[data-gallery]'), more = $('[data-gallery-more]');
  if (gal && more) {
    var step = +gal.dataset.step || 9;
    more.addEventListener('click', function () {
      var hidden = $$('.masonry__item[hidden]', gal);
      hidden.slice(0, step).forEach(function (el) { el.hidden = false; });
      if (hidden.length <= step) more.hidden = true;
    });
  }
  var lb = $('#lightbox');
  if (lb) {
    document.addEventListener('click', function (e) {
      var it = e.target.closest('[data-lightbox]');
      if (!it) return;
      var img = $('img', lb), cap = $('figcaption', lb);
      img.src = it.getAttribute('data-lightbox'); img.alt = it.getAttribute('data-caption') || '';
      cap.textContent = it.getAttribute('data-caption') || '';
      lb.showModal();
    });
  }

  /* ── Загрузка работы: предпросмотр, проверка файла, индикатор (ФТ-10) ── */
  function fileError(f) {
    return (/^image\/(jpeg|png)$/.test(f.type) && f.size <= 10 * 1024 * 1024) ? '' : 'Допустимы файлы JPG и PNG до 10 МБ';
  }
  function fmtSize(n) { return (n / 1048576).toFixed(1).replace('.', ',') + ' МБ'; }
  var workForm = $('#modal-work form'), workFile = $('#work-file');
  function showPreview(file) {
    var box = $('[data-preview]', workForm);
    var err = fileError(file);
    setError(workForm, 'file', err);
    if (err) { box.hidden = true; return; }
    box.hidden = false;
    $('[data-preview-img]', workForm).src = URL.createObjectURL(file);
    $('[data-preview-name]', workForm).textContent = file.name;
    $('[data-preview-size]', workForm).textContent = fmtSize(file.size) + ', загружено';
    var bar = $('[data-progress]', workForm); bar.style.width = '0'; setTimeout(function () { bar.style.width = '100%'; }, 50);
    var title = $('[name=title]', workForm);
    if (!title.value) title.value = file.name.replace(/\.[^.]+$/, '');
  }
  function resetPreview(form) {
    if (form !== workForm) return;
    $('[data-preview]', form).hidden = true;
  }
  if (workFile) workFile.addEventListener('change', function () { if (workFile.files[0]) showPreview(workFile.files[0]); });

  var dz = $('[data-dropzone]');
  if (dz && workFile) {
    var pick = function (files) {
      if (!files || !files.length) return;
      var dt = new DataTransfer(); dt.items.add(files[0]); workFile.files = dt.files;
      openModal('work'); showPreview(files[0]);
      if (files.length > 1) toast('Файлы загружаются по одному — выбран первый. Загрузка нескольких появится с базой данных.');
    };
    ['dragenter', 'dragover'].forEach(function (ev) { dz.addEventListener(ev, function (e) { e.preventDefault(); dz.classList.add('is-over'); }); });
    ['dragleave', 'drop'].forEach(function (ev) { dz.addEventListener(ev, function (e) { e.preventDefault(); dz.classList.remove('is-over'); }); });
    dz.addEventListener('drop', function (e) { pick(e.dataTransfer.files); });
    $('#dz-files').addEventListener('change', function (e) { pick(e.target.files); e.target.value = ''; });
  }
})();
