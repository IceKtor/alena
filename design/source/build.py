# Сборка экранов дизайна «Тёмная галерея»: HTML для каждого кадра (десктоп 1440 / мобильный 390)
import json, os
IMG = 'http://127.0.0.1:8766/img/photos/'
OUT = os.path.dirname(os.path.abspath(__file__)) + '/html/'
os.makedirs(OUT, exist_ok=True)

W = [  # key, название, жанр, автор, номер кадра, время
 ('road', 'Ночная трасса', 'Репортаж', 'Мичил Алексеев', '07A', '2 ч назад'),
 ('hat', 'Взгляд из зелени', 'Портрет', 'Алексей Петров', '29B', '3 ч назад'),
 ('pier', 'Причал на закате', 'Пейзаж', 'Лена Борисова', '19A', '5 ч назад'),
 ('dancer', 'Прыжок', 'Репортаж', 'Мария Саввина', '03C', '8 ч назад'),
 ('reflection', 'Огни у воды', 'Пейзаж', 'Айсен Охлопков', '22B', '11 ч назад'),
 ('bike', 'Старый велосипед', 'Предметная', 'Анна Григорьева', '36C', 'вчера'),
 ('camera', 'Девушка с камерой', 'Портрет', 'Мария Саввина', '11A', 'вчера'),
 ('root', 'Корни леса', 'Пейзаж', 'Виктор Громов', '28A', 'вчера'),
 ('drop', 'Капля', 'Предметная', 'Лена Борисова', '14B', '2 дня назад'),
 ('lighthouse', 'Маяк в сумерках', 'Пейзаж', 'Айсен Охлопков', '05C', '2 дня назад'),
 ('city', 'Город днём', 'Репортаж', 'Туйаара Попова', '17B', '3 дня назад'),
 ('tree', 'Дерево на закате', 'Пейзаж', 'Евгений Макаров', '09C', '3 дня назад'),
 ('statue', 'Свобода', 'Репортаж', 'Николай Егоров', '31A', '4 дня назад'),
 ('bike', 'Велосипед у стены', 'Предметная', 'Анна Григорьева', '12C', '4 дня назад'),
 ('dancer', 'Танец', 'Репортаж', 'Мария Саввина', '02B', '5 дней назад'),
 ('pier', 'Тихая вода', 'Пейзаж', 'Лена Борисова', '25C', '5 дней назад'),
]
P = [  # имя, жанры, город, цена, аватар, превью
 ('Алексей Петров', 'Портрет, Пейзаж, Репортаж', 'Якутск', '3 000', 'avatar1', ['hat', 'pier', 'tree']),
 ('Мария Саввина', 'Портрет, Репортаж', 'Якутск', '2 500', 'avatar2', ['camera', 'dancer', 'reflection']),
 ('Айсен Охлопков', 'Пейзаж, Репортаж', 'Якутск', '3 500', 'avatar3', ['lighthouse', 'pier', 'tree']),
 ('Саргылана Иванова', 'Свадебная, Love Story', 'Якутск', '5 500', 'avatar2', ['camera', 'hat', 'reflection']),
 ('Дмитрий Колесов', 'Портрет, Предметная', 'Якутск', '4 000', 'avatar4', ['dancer', 'bike', 'drop']),
 ('Николай Егоров', 'Репортаж, Предметная', 'Якутск', '2 800', 'avatar5', ['city', 'road', 'statue']),
]
GENRES = ['Портрет', 'Пейзаж', 'Репортаж', 'Предметная', 'Свадебная', 'Семейная', 'Love Story']

ICO = {
 'mail': '<rect x="3" y="5.5" width="18" height="13" rx="2"/><path d="m3.5 7 8.5 6.5L20.5 7"/>',
 'heart': '<path d="M12 20s-7.5-4.6-9.2-9.3C1.7 7.4 3.8 4.5 6.9 4.5c1.9 0 3.5 1 5.1 3 1.6-2 3.2-3 5.1-3 3.1 0 5.2 2.9 4.1 6.2C19.5 15.4 12 20 12 20z"/>',
 'search': '<circle cx="11" cy="11" r="6.5"/><path d="m20 20-4.2-4.2"/>',
 'pin': '<path d="M12 21s-6.5-5.6-6.5-11a6.5 6.5 0 0 1 13 0c0 5.4-6.5 11-6.5 11z"/><circle cx="12" cy="10" r="2.3"/>',
 'upload': '<path d="M12 16V4m0 0L7 9m5-5 5 5M4 20h16"/>',
 'x': '<path d="m6 6 12 12M18 6 6 18"/>',
 'check': '<path d="m5 12.5 4.5 4.5L19 7.5"/>',
 'chev': '<path d="m6 9 6 6 6-6"/>',
 'menu': '<path d="M4 8h16M4 16h16"/>',
 'bell': '<path d="M6 17V11a6 6 0 0 1 12 0v6l1.5 2h-15zM10 21h4"/>',
 'pencil': '<path d="m5 19 1-4L16.5 4.5a2 2 0 0 1 3 3L9 18zM14.5 6.5l3 3"/>',
 'arrow': '<path d="M4 12h16m-6-6 6 6-6 6"/>',
 'left': '<path d="M20 12H4m6-6-6 6 6 6"/>',
 'sliders': '<path d="M4 7h9m4 0h3M4 17h3m4 0h9"/><circle cx="15" cy="7" r="2"/><circle cx="9" cy="17" r="2"/>',
 'phone': '<path d="M6.5 3.5h3l1.5 4-2 1.5a11 11 0 0 0 6 6l1.5-2 4 1.5v3a2 2 0 0 1-2 2A15.5 15.5 0 0 1 4.5 5.5a2 2 0 0 1 2-2z"/>',
 'clock': '<circle cx="12" cy="12" r="8.5"/><path d="M12 7.5V12l3 2"/>',
 'cal': '<rect x="3.5" y="5" width="17" height="15" rx="2"/><path d="M3.5 10h17M8 3v4M16 3v4"/>',
 'image': '<rect x="3.5" y="4.5" width="17" height="15" rx="2"/><circle cx="9" cy="10" r="1.6"/><path d="m4 18 5-5 3.5 3.5L15 14l5 5"/>',
 'user': '<circle cx="12" cy="8.5" r="3.8"/><path d="M4.5 20c.8-4 3.8-6 7.5-6s6.7 2 7.5 6"/>',
}
def ico(n, extra=''):
    return f'<svg class="ico" viewBox="0 0 24 24" width="20" height="20"{extra}>{ICO[n]}</svg>'
def img(k, style='', cls=''):
    return f'<img src="{IMG}{k}.jpg"{f" class={chr(34)}{cls}{chr(34)}" if cls else ""}{f" style={chr(34)}{style}{chr(34)}" if style else ""}>'
def page(body, w, cls='', h=None, extra_style=''):
    st = f'--w:{w}px;' + (f'--h:{h}px;' if h else '')
    return (f'<!doctype html><html lang="ru"><meta charset="utf-8"><link rel="stylesheet" href="../design.css">'
            f'<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400;1,500&family=Inter:wght@400;500;600&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">'
            f'<body><div id="frame" class="{cls}" style="{st}{extra_style}">{body}</div></body></html>')

# ── общие блоки ──
def header(active='', user=None, over=False):
    items = [('home', 'Главная'), ('cat', 'Фотографы'), ('works', 'Работы'), ('about', 'О платформе')]
    nav = ''.join(f'<a class="up{" on" if k == active else ""}" href="#">{t}</a>' for k, t in items)
    if user == 'ph':
        right = f'<span class="bell">{ico("bell")}<b></b></span><span class="user"><span class="avw">{img("avatar1")}</span><span class="nm2">Алексей П.</span></span><span class="burger">{ico("menu")}</span>'
    elif user == 'cl':
        right = f'<span class="bell">{ico("bell")}</span><span class="user"><span class="nm2">Мария З.</span></span><span class="burger">{ico("menu")}</span>'
    else:
        right = f'<a class="up">Войти</a><a class="up uor">Разместить портфолио →</a><span class="burger">{ico("menu")}</span>'
    return (f'<header class="hd{" over" if over else ""}"><a class="logo">объектив<i>.</i></a><nav class="nav">{nav}</nav>'
            f'<div class="hd-r">{right}</div></header>')

def footer():
    return ('<footer class="ft-bar"><div><a class="logo">объектив<i>.</i></a><p class="about">Каталог фотографов Якутии: смотрите работы, сравнивайте цены, пишите напрямую.</p></div>'
            '<div><h4>Заказчикам</h4><ul><li>Найти фотографа</li><li>Свежие работы</li><li>Как выбрать</li></ul></div>'
            '<div><h4>Фотографам</h4><ul><li>Разместить портфолио</li><li>Личный кабинет</li><li>Советы по съёмке</li></ul></div>'
            '<div><h4>Платформа</h4><ul><li>О проекте</li><li>Правила</li><li>Контакты</li><li>Политика ПДн</li></ul></div></footer>'
            '<div class="legal"><span>© 2026 Объектив · учебный проект, фото и данные условные</span><span>Якутск</span></div>')

def mas(items, caps=True, time=False):
    out = ''
    for k, t, g, a, n, tm in items:
        c = (f'<figcaption><span class="fno">{n}</span><span><b>{t}</b><br><span class="mt">{g} · {a}{" · " + tm if time else ""}</span></span></figcaption>') if caps else ''
        out += f'<figure>{img(k)}{c}</figure>'
    return f'<div class="mas">{out}</div>'

def rows(m=False, n=6):
    out = ''
    for i, (nm, g, c, p, av, ks) in enumerate(P[:n]):
        th = ''.join(img(k) for k in ks[:3])
        out += (f'<div class="row"><span class="no">{i+1:02d}</span><span class="nm">{nm}</span><span class="gn">{g}</span>'
                f'<span class="ct">{c}</span><span class="pr">от {p} ₽ / час</span><span class="th">{th}</span></div>')
    return f'<div class="rows">{out}</div>'

def card(p):
    nm, g, c, pr, av, ks = p
    return (f'<article class="pc"><div class="col">{img(ks[0])}{img(ks[1])}{img(ks[2])}</div>'
            f'<div class="nm"><span>{nm}</span><span class="ac">{ico("mail")}{ico("heart")}</span></div><div class="gn">{g}</div>'
            f'<div class="meta"><span>{c}</span><span class="mono">от {pr} ₽ / час</span></div></article>')

def field(label, val='', ph=False, cls='', extra=''):
    return f'<label class="field"><small>{label}</small><div class="v {"plh " if ph else ""}{cls}"><span>{val}</span>{extra}</div></label>'

def chk(t, on=False):
    return f'<div class="chk{" on" if on else ""}"><s>{ico("check") if on else ""}</s><span>{t}</span></div>'
def tg(t, on=False):
    return f'<div class="tg{" on" if on else ""}"><s></s><span>{t}</span></div>'

def rng():
    return ('<div style="position:relative;height:26px;margin:4px 8px 6px"><div style="position:absolute;left:0;right:0;top:12px;height:2px;background:#34332F"></div>'
            '<div style="position:absolute;left:8%;right:22%;top:12px;height:2px;background:#E8743B"></div>'
            '<div style="position:absolute;left:calc(8% - 8px);top:4px;width:18px;height:18px;border:1px solid #ECEBE6;border-radius:50%;background:#0F0F0E"></div>'
            '<div style="position:absolute;right:calc(22% - 8px);top:4px;width:18px;height:18px;border:1px solid #ECEBE6;border-radius:50%;background:#0F0F0E"></div></div>'
            '<div class="mono" style="display:flex;justify-content:space-between;font-size:13px"><span>1 200 ₽</span><span>9 500 ₽</span></div>')

def filters():
    return ('<div class="flt"><div><h3>Город</h3>' + field('', 'Якутск', extra=ico('chev')) + '</div>'
            '<div><h3>Жанр</h3><div class="gl">' + ''.join(chk(g, i in (0, 1)) for i, g in enumerate(GENRES)) + '</div></div>'
            '<div><h3>Цена за час</h3>' + rng() + '</div>'
            '<div><h3>Дополнительно</h3><div class="gl">' + tg('Ночная съёмка') + tg('Видеосъёмка', True) + tg('Выезд в район') + '</div></div>'
            '<div style="display:grid;gap:10px"><a class="btn p blk">Показать 14</a><a class="btn blk">Сбросить всё</a></div></div>')

def pagination():
    return '<div class="pg"><span class="on">1</span><span>2</span><span>3</span><span>…</span><span>8</span><span class="up" style="font:12.5px var(--sans);letter-spacing:.12em;color:var(--tx)">Далее →</span></div>'

def cta_block():
    return ('<section class="cta"><div><h2>Снимаете? <em>Покажите, что умеете.</em></h2><p>Профиль бесплатный, заводится за пару минут. Заявки собираются в личном кабинете.</p></div>'
            '<a class="btn p">Создать профиль</a></section>')

# ── страницы ──
def home(m=False):
    n = 8 if m else 12
    return (f'<section class="hero stack">{img("road")}<div class="dim"></div>{header("home", over=True)}<div class="hc">'
            '<h1>Фотографы Якутии — <em>по их кадрам</em></h1>'
            '<p class="lead">Смотрите работы, сравнивайте цены и пишите фотографу напрямую. Если снимаете сами — заведите страницу, это бесплатно.</p>'
            '<div class="sline">' + field('Город', 'Якутск') + field('Жанр', 'Любой') + field('Бюджет за час', 'до 12 000 ₽') +
            '<a class="btn p">Найти фотографа</a></div></div></section>'
            '<section class="sec"><h2>Свежие кадры</h2><p class="sub">Последние публикации, новые сверху</p>' + mas(W[:n]) +
            '<div class="more"><a class="btn">Все работы</a></div></section>'
            '<section class="sec"><h2>Фотографы</h2><p class="sub">18 в каталоге · Якутск</p>' + rows(m, 4 if m else 6) +
            '<div class="more"><a class="btn">Весь каталог</a></div></section>' + cta_block() + footer())

def catalog(m=False):
    return (header('cat') +
            '<section class="ph"><div style="display:flex;justify-content:space-between;align-items:flex-end;gap:20px"><div><h1>Фотографы</h1><p class="cnt">Найдено: 14 фотографов · Якутск</p></div>'
            + ('' if m else f'<div class="field" style="width:240px"><small>Сортировка</small><div class="v"><span>Сначала новые</span>{ico("chev")}</div></div>') + '</div></section>'
            f'<div class="cat"><div class="fbar"><div>{ico("sliders")}Фильтры · 2</div><div>Сортировка{ico("chev")}</div></div>' + filters() +
            '<div><div class="pcs">' + ''.join(card(p) for p in (P[:3] if m else P)) + '</div>' + pagination() + '</div></div>' + footer().replace('margin-top', 'margin-top'))

def profile(m=False):
    mas_items = W[:9] if m else W[:9]
    ph = P[0]
    return (header('cat') + f'<div class="cover">{img("cover")}</div>'
            f'<div class="pf"><span class="avw">{img("avatar1")}</span><div class="inf"><h1>Алексей Петров</h1><div class="meta"><span style="display:flex;gap:6px;align-items:center">{ico("pin")}Якутск, опыт 8 лет</span><span class="up" style="font-size:12px">Портрет / Пейзаж / Репортаж</span></div></div>'
            '<div class="cta2"><a class="btn p">Связаться</a><span class="mono">от 3 000 ₽ за час</span></div></div>'
            '<div class="pf-tabs"><div class="tabs"><span class="tab on">Портфолио</span><span class="tab">Об авторе</span><span class="tab">Услуги и цены</span><span class="tab">Отзывы</span></div></div>'
            '<div class="pbody"><div>' + mas(mas_items, caps=False) + '<div class="more"><a class="btn">Показать ещё</a></div></div>'
            '<aside class="side"><section><h3>Услуги и цены</h3><ul class="pl"><li><span>Портретная съёмка</span><span class="mono">от 3 000 ₽</span></li><li><span>Пейзаж и путешествия</span><span class="mono">от 3 500 ₽</span></li><li><span>Репортаж</span><span class="mono">от 4 000 ₽</span></li></ul>'
            '<a class="btn p blk" style="margin-top:20px">Связаться</a><p style="font-size:12.5px;margin-top:10px;color:var(--ft)">Цены ориентировочные и не являются офертой.</p></section>'
            '<section><h3>Свободные даты</h3><div class="dates"><span class="dt">15</span><span class="dt on">16</span><span class="dt">22</span><span class="dt">23</span><span class="dt">29</span></div><p style="font-size:13px">Ноябрь, ответ в течение дня</p></section>'
            '<section><h3>Об авторе</h3><p>Снимаю портреты, города и пейзажи на плёнку и цифру. Люблю естественный свет, живые эмоции и честные кадры без постановки.</p></section></aside></div>'
            + footer() + '<div class="stickpad"></div>' +
            '<div class="stick"><div><b class="mono" style="font-weight:500">от 3 000 ₽ / час</b><small>Ответит в течение дня</small></div><a class="btn p">Связаться</a></div>')

def works(m=False):
    n = 10 if m else 16
    tabs = ''.join(f'<span class="tab{" on" if i == 0 else ""}">{t}</span>' for i, t in enumerate(['Все', 'Портрет', 'Пейзаж', 'Репортаж', 'Предметная', 'Свадьба']))
    return (header('works') + '<section class="ph"><h1>Работы</h1><p class="cnt">Лента обновляется ежедневно — новые кадры сверху.</p></section>'
            f'<div class="filters-row" style="margin-top:28px">{tabs}</div>' + f'<div class="wrap">{mas(W[:n], time=True)}<div class="more"><a class="btn">Показать ещё</a></div></div>'
            + cta_block() + footer())

def about(m=False):
    return (header('about') + '<section class="ph abt" style="padding-top:72px"><h1>Фотографы Якутии <em>в одном месте.</em> Заказчик выбирает по снимкам, фотограф не тратит время на сайт.</h1></section>'
            '<div class="facts"><div><b>18</b><span>фотографов</span></div><div><b>166</b><span>работ в ленте</span></div><div><b>7</b><span>жанров съёмки</span></div><div><b>0 ₽</b><span>стоит размещение</span></div></div>'
            '<section class="two"><div><h3>Если вы ищете фотографа</h3><p>Выберите город и жанр, укажите бюджет и посмотрите работы. Понравился стиль — напишите прямо из профиля. Регистрироваться для этого не нужно.</p><p>Хотите вернуться к кому-то позже — добавьте в избранное.</p></div>'
            '<div><h3>Если вы фотограф</h3><p>Заведите профиль, загрузите работы, укажите цены «от» и свободные даты. Заявки собираются в личном кабинете, там же видно, сколько человек смотрели ваш профиль.</p><p>Загружайте только то, что сняли сами или на что есть права.</p></div></section>'
            f'<section class="contact"><div><h3>Контакты</h3><ul class="ci"><li>{ico("mail")}<span><b>Почта</b>hello@obyektiv.example</span></li><li>{ico("phone")}<span><b>Телефон</b>+7 900 000-00-00</span></li><li>{ico("pin")}<span><b>Адрес</b>г. Якутск, ул. Кулаковского, 48</span></li><li>{ico("clock")}<span><b>Время ответа</b>В течение рабочего дня</span></li></ul></div>'
            '<div><h3>Написать нам</h3><div class="frm">' + field('Имя', 'Ваше имя', True) + field('Почта', 'name@mail.ru', True) + field('Сообщение', 'О чём хотите написать', True, 'area') + chk('Согласен на обработку данных') +
            '<div><a class="btn p">Отправить</a></div></div></div></section>' + footer())

def cab_nav(active):
    items = [('works', 'Мои работы'), ('profile', 'Профиль'), ('req', 'Заявки'), ('stats', 'Статистика'), ('set', 'Настройки')]
    return '<ul class="cnav">' + ''.join(f'<li class="{"on" if k == active else ""}"><span>{t}</span>{"<b class=cnt>3</b>" if k == "req" else ""}</li>' for k, t in items) + '</ul>'

def cab_works(m=False):
    n = 6 if m else 8
    figs = ''.join(f'<figure><div class="im">{img(k)}<span class="ed">{ico("pencil")}</span></div><figcaption>{t}<span>Просмотров: {312 - i*24}</span></figcaption></figure>' for i, (k, t, *_r) in enumerate(W[:n]))
    return (header('', 'ph') + '<div class="cab">' + cab_nav('works') + '<div><div class="chead"><h1>Мои работы</h1><a class="btn p">Загрузить работу</a></div>'
            '<div class="met"><div><b>1 284</b><span>просмотров профиля за месяц</span></div><div><b>12</b><span>новых заявок</span></div><div><b>87</b><span>добавили в избранное</span></div></div>'
            f'<h2 class="sh3">Опубликовано 24</h2><div class="wg">{figs}</div>'
            f'<div class="dz">{ico("upload")}<div><b>Перетащите фото сюда</b><span>JPG или PNG до 10 МБ. Можно выбрать сразу несколько файлов.</span></div><a class="btn">Выбрать файлы</a></div></div></div>' + footer())

def cab_req(m=False):
    reqs = [('Мария', '+7 900 000-00-00', 'Портрет', '24 ноября', 'Хочу портретную съёмку в студии, около часа.', 'new', 'Новая', 'сегодня, 10:24'),
            ('Сардаана', 'sardaana@mail.ru', 'Репортаж', '2 декабря', 'Нужен репортаж с корпоратива, три часа.', 'seen', 'Просмотрена', 'вчера, 18:02'),
            ('Иван', '+7 914 111-22-33', 'Пейзаж', '10 декабря', 'Съёмка пейзажей за городом, нужен выезд.', 'done', 'Обработана', '3 дня назад'),
            ('Айталина', 'aitalina@mail.ru', 'Портрет', '15 декабря', 'Семейный портрет, нас четверо, лучше на улице.', 'done', 'Обработана', '5 дней назад')]
    arts = ''
    for nm, ct, g, d, msg, st, stl, tm in reqs:
        arts += (f'<article><div class="who"><b>{nm}</b><span>{tm}</span></div><div class="msg"><p>{msg}</p><small><span>{g}</span><span>съёмка: {d}</span><span>{ct}</span></small></div>'
                 f'<div class="st"><span class="tag {st}"><i></i>{stl}</span><div class="field" style="width:170px"><div class="v" style="min-height:36px;font-size:14px"><span>Изменить статус</span>{ico("chev")}</div></div></div></article>')
    return (header('', 'ph') + '<div class="cab">' + cab_nav('req') + '<div><div class="chead"><h1>Заявки</h1><span class="mt">3 новых из 12</span></div><div class="rq">' + arts + '</div></div></div>' + footer())

def favorites(m=False):
    return (header('', 'cl') + '<section class="ph fav"><h1>Избранное</h1><p class="cnt">Фотографы, которых вы сохранили</p></section>'
            '<div class="wrap" style="padding-top:40px"><div class="pcs">' + ''.join(card(p) for p in P[:2 if m else 4]) + '</div></div>' + footer())

def notfound(m=False):
    return (header('') + '<section class="nf"><div class="big">404</div><h1>Кадр не в фокусе</h1><p>Такой страницы нет: адрес набран неверно или страницу удалили.</p><a class="btn p">На главную</a></section>' + footer())

# ── окна ──
def under(m):  # подложка для окон: главная
    return (f'<section class="hero stack" style="height:{844 if m else 900}px;min-height:0">{img("road")}<div class="dim"></div>{header("home", over=True)}<div class="hc" style="padding-top:0">'
            '<h1 style="margin-bottom:' + ('260px' if m else '120px') + '">Фотографы Якутии — <em>по их кадрам</em></h1></div></section>')

def modal(inner, m=False, w=None):
    grab = '<div class="grab"></div>'
    return f'<div class="ov"><div class="md"{f" style={chr(34)}width:{w}px{chr(34)}" if (w and not m) else ""}>{grab}{inner}</div></div>'
X = f'<span class="x">{ico("x")}</span>'

def m_login(m=False):
    return under(m) + modal(X + '<h2>Вход</h2><p class="lead2">Войдите, чтобы писать фотографам и вести портфолио.</p>' +
        field('Почта', 'name@mail.ru', True, 'focus') +
        '<div><div style="display:flex;justify-content:space-between;align-items:baseline;margin-bottom:6px"><small style="font-size:11px;letter-spacing:.12em;text-transform:uppercase;color:var(--mt)">Пароль</small><span class="mt" style="font-size:13px;text-decoration:underline;text-decoration-color:var(--or);text-underline-offset:3px">Забыли пароль?</span></div><div class="field"><div class="v"><span>••••••••</span></div></div></div>'
        '<a class="btn p blk">Войти</a><div class="or">или</div><a class="btn blk">Войти по коду из письма</a><p class="sw">Нет аккаунта? <u>Создать профиль</u></p>', m)

def m_register(m=False):
    return under(m) + modal(X + '<h2>Создать профиль</h2><p class="lead2">Выберите, как вы будете пользоваться Объективом.</p>'
        f'<div class="roles"><div class="role on">{ico("image")}<b>Я фотограф</b><small>Размещаю портфолио</small></div><div class="role">{ico("search")}<b>Я заказчик</b><small>Ищу исполнителя</small></div></div>' +
        field('Имя', 'Алексей Петров') + field('Почта', 'name@mail.ru', True) + field('Пароль', 'Не короче 8 символов', True) + chk('Согласен с правилами платформы', True) +
        '<a class="btn p blk">Создать профиль</a><p class="sw">Уже есть аккаунт? <u>Войти</u></p>', m)

def m_request(m=False):
    return under(m) + modal(X + f'<div class="who2"><span class="avw">{img("avatar1")}</span><div><h2 style="font-size:36px;margin:0 0 4px">Написать Алексею</h2><span class="mt" style="font-size:13.5px">Обычно отвечает в течение дня</span></div></div>' +
        field('Ваше имя', 'Мария') + field('Телефон или почта', '123', False, 'bad', ) + '<p class="err" style="margin-top:-14px;color:var(--red);font-size:13px">Укажите телефон или почту для связи</p>' +
        '<div class="two2">' + field('Дата съёмки', '24 ноября', extra=ico('cal')) + field('Жанр', 'Портрет', extra=ico('chev')) + '</div>' +
        field('Сообщение', 'Расскажите, что и где снимаем', True, 'area') + chk('Согласен на обработку данных', True) +
        '<p class="hint">Заявка не является договором или оплатой. Цены ориентировочные.</p><a class="btn p blk">Отправить заявку</a>', m, 520)

def m_work(m=False):
    return under(m) + modal(X + '<h2>Новая работа</h2>'
        f'<div class="up-prev">{img("statue")}<div><b style="font-weight:500">svoboda.jpg</b><small>4,2 МБ, загружено</small><span class="bar"><i></i></span><small style="color:var(--ok);display:flex;gap:6px;align-items:center">{ico("check")}Готово к публикации</small></div></div>' +
        field('Название', 'Свобода') + '<div class="two2">' + field('Жанр', 'Репортаж', extra=ico('chev')) + field('Теги', 'статуя, небо, ч/б') + '</div>' +
        tg('Показывать в портфолио', True) + tg('Разрешить скачивание') +
        '<div class="mbt"><a class="btn">Отмена</a><a class="btn p">Опубликовать</a></div>', m, 540)

def lightbox(m=False):
    xb = f'<span style="display:flex;width:40px;height:40px;border:1px solid #55534C;border-radius:50%;align-items:center;justify-content:center">{ico("x")}</span>'
    return (under(m) + '<div class="lb"><div class="top"><span class="mono" style="font-size:13px;color:var(--mt)">07 / 12</span>' + xb + '</div>'
            f'<div class="img"><span class="arr">{ico("left")}</span>{img("road")}<span class="arr">{ico("arrow")}</span></div>'
            '<div class="cap"><div><h3>Ночная трасса</h3><span class="mt">Репортаж · Мичил Алексеев · <span class="fno">07A</span></span></div><div style="display:flex;gap:12px;align-items:center"><a class="btn sm">Профиль автора</a><a class="btn p sm">Написать</a></div></div></div>')

def mobile_menu(m=True):
    return (under(True) + '<div class="mn">' + f'<header class="hd"><a class="logo">объектив<i>.</i></a><div class="hd-r"><span class="burger" style="display:flex">{ico("x")}</span></div></header>'
            '<ul><li class="on">Главная<span>→</span></li><li>Фотографы<span class="mt">→</span></li><li>Работы<span class="mt">→</span></li><li>О платформе<span class="mt">→</span></li></ul>'
            '<div class="bot"><a class="btn blk">Войти</a><a class="btn p blk">Разместить портфолио</a></div></div>')

def filters_sheet(m=True):
    body = ('<div class="grab"></div>' + f'<span class="x">{ico("x")}</span><h2>Фильтры</h2>' +
            field('Город', 'Якутск', extra=ico('chev')) +
            '<div><small style="display:block;font-size:11px;letter-spacing:.12em;text-transform:uppercase;color:var(--mt);margin-bottom:10px">Жанр</small><div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">' + ''.join(chk(g, i in (0, 1)) for i, g in enumerate(GENRES)) + '</div></div>'
            '<div><small style="display:block;font-size:11px;letter-spacing:.12em;text-transform:uppercase;color:var(--mt);margin-bottom:10px">Цена за час</small>' + rng() + '</div>'
            '<div style="display:grid;gap:12px">' + tg('Ночная съёмка') + tg('Видеосъёмка', True) + tg('Выезд в район') + '</div>'
            '<div class="mbt"><a class="btn">Сбросить</a><a class="btn p">Показать 14</a></div>')
    return ('<div class="und">' + header('cat') + '<section class="ph"><h1>Фотографы</h1><p class="cnt">Найдено: 14 фотографов · Якутск</p></section>'
            f'<div class="cat"><div class="fbar"><div>{ico("sliders")}Фильтры · 2</div><div>Сортировка{ico("chev")}</div></div>' + '<div class="pcs">' + card(P[0]) + '</div></div></div>'
            '<div class="ov"><div class="sheet">' + body + '</div></div>')

def design_system(m=False):
    sw = [('Фон', '#0F0F0E', '--bg'), ('Поверхность', '#171715', '--s1'), ('Поверхность 2', '#1F1F1C', '--s2'), ('Линия', '#34332F', '--line'), ('Линия сильная', '#55534C', '--line2'),
          ('Текст', '#ECEBE6', '--tx'), ('Вторичный', '#A09E96', '--mt'), ('Приглушённый', '#79776F', '--ft'), ('Акцент', '#E8743B', '--or'), ('Акцент светлый', '#F0A874', '--or2'),
          ('Жёлтый', '#F5B83D', '--amber'), ('Успех', '#5DBB9E', '--ok'), ('Ошибка', '#E5604F', '--red')]
    sws = ''.join(f'<div class="swc"><i style="background:{c}"></i><b>{n}</b><span>{c} · {v}</span></div>' for n, c, v in sw)
    return (f'<div class="ds"><section class="ph"><div style="display:flex;justify-content:space-between;align-items:flex-end"><h1>Дизайн-система: «Тёмная галерея»</h1><a class="logo">объектив<i>.</i></a></div>'
            '<p class="cnt" style="margin-top:14px;max-width:760px">Фото главнее интерфейса: тёмный нейтральный фон, тонкие линии вместо рамок и теней, крупная антиква в заголовках, один оранжевый акцент.</p></section>'
            f'<section class="blk2"><h2 class="t">Цвета</h2><div class="sw-g">{sws}</div></section>'
            '<section class="blk2"><h2 class="t">Типографика</h2><div class="g3"><div><div style="font:400 108px/1 var(--serif);letter-spacing:-.02em">Аа</div><p class="mt" style="margin-top:10px">Cormorant Garamond 400 / 500 / italic<br>Заголовки: 108 · 84 · 56 · 38 px, интервал 1,0</p></div>'
            '<div><div style="font:400 56px/1.1 var(--sans);letter-spacing:-.01em">Аа</div><p class="mt" style="margin-top:10px">Inter 400 / 500 / 600<br>Текст 15 px / 1,5 · подписи 12,5 px · кнопки 12,5 px, капс, трекинг 0,12 em</p></div>'
            '<div><div style="font:400 56px/1.1 var(--mono)">07A</div><p class="mt" style="margin-top:10px">IBM Plex Mono 400 / 500<br>Цены, номера кадров, счётчики</p></div></div></section>'
            '<section class="blk2"><h2 class="t">Кнопки и ссылки</h2><div class="rowf"><a class="btn p">Основная</a><a class="btn">Вторичная</a><a class="btn p sm">Малая</a><a class="btn sm">Малая вторичная</a><span class="up uor">Ссылка →</span><span class="up uline">Меню</span></div></section>'
            '<section class="blk2"><h2 class="t">Поля и контролы</h2><div class="g3"><div style="display:grid;gap:20px">' + field('Поле ввода', 'Введите текст', True) + field('В фокусе', 'Якутск', False, 'focus') + field('Ошибка', '123', False, 'bad') + '<p class="err" style="margin-top:-14px;color:var(--red);font-size:13px">Укажите телефон или почту для связи</p></div>'
            '<div style="display:grid;gap:16px;align-content:start">' + chk('Флажок') + chk('Флажок выбран', True) + tg('Переключатель') + tg('Переключатель включён', True) + '<div class="roles"><div class="role on"><b>Выбрано</b></div><div class="role"><b>Обычное</b></div></div></div>'
            '<div style="display:grid;gap:18px;align-content:start"><div class="tabs"><span class="tab on">Все</span><span class="tab">Портрет</span><span class="tab">Пейзаж</span></div><div style="display:flex;gap:26px"><span class="tag new"><i></i>Новая</span><span class="tag seen"><i></i>Просмотрена</span><span class="tag done"><i></i>Обработана</span></div><div class="fno">07A · номер кадра</div></div></div></section>'
            '<section class="blk2"><h2 class="t">Карточки</h2><div class="g3" style="grid-template-columns:1fr 1fr"><div>' + card(P[0]) + '</div><div><figure class="mas" style="column-count:1"><figure>' + img('hat') + '<figcaption><span class="fno">29B</span><span><b>Взгляд из зелени</b><br><span class="mt">Портрет · Алексей Петров</span></span></figcaption></figure></figure></div></div></section>'
            '<section class="blk2"><h2 class="t">Иконки · линия 1,5 px</h2><div class="rowf" style="gap:28px">' + ''.join(ico(n) for n in ['mail', 'heart', 'search', 'pin', 'upload', 'x', 'check', 'chev', 'menu', 'bell', 'pencil', 'arrow', 'sliders', 'phone', 'clock', 'cal', 'image', 'user']) + '</div></section>'
            '<section class="blk2" style="padding-bottom:80px"><h2 class="t">Сетка и размеры</h2><p class="mt" style="max-width:900px">Десктоп: кадр 1440 px, поля 56 px, мозаика 4 колонки по 18 px. Мобильная версия: кадр 390 px, поля 20 px, мозаика 2 колонки по 10 px, окна открываются нижним листом. Углы почти не скругляются: фото — 0, кнопки — 2 px, листы — 16 px сверху. Теней нет, глубина — линиями и затемнением подложки.</p></section></div>')

FRAMES = [  # файл, ширина, функция, класс, высота
 ('00 Дизайн-система', 1440, design_system, '', None),
 ('01 Главная', 1440, home, '', None), ('02 Каталог фотографов', 1440, catalog, '', None), ('03 Профиль фотографа', 1440, profile, '', None),
 ('04 Работы', 1440, works, '', None), ('05 О платформе', 1440, about, '', None), ('06 Личный кабинет - мои работы', 1440, cab_works, '', None),
 ('07 Личный кабинет - заявки', 1440, cab_req, '', None), ('08 Избранное', 1440, favorites, '', None), ('09 Ошибка 404', 1440, notfound, '', None),
 ('10 Окно - вход', 1440, m_login, 'fh', 900), ('11 Окно - регистрация', 1440, m_register, 'fh', 900), ('12 Окно - написать фотографу', 1440, m_request, 'fh', 900),
 ('13 Окно - новая работа', 1440, m_work, 'fh', 900), ('14 Просмотр фото', 1440, lightbox, 'fh', 900),
 ('20 Мобильная - главная', 390, home, '', None), ('21 Мобильная - каталог', 390, catalog, '', None), ('22 Мобильная - фильтры', 390, filters_sheet, 'fh', 844),
 ('23 Мобильная - профиль', 390, profile, '', None), ('24 Мобильная - работы', 390, works, '', None), ('25 Мобильная - о платформе', 390, about, '', None),
 ('26 Мобильная - кабинет, работы', 390, cab_works, '', None), ('27 Мобильная - кабинет, заявки', 390, cab_req, '', None), ('28 Мобильная - избранное', 390, favorites, '', None),
 ('29 Мобильная - ошибка 404', 390, notfound, '', None), ('30 Мобильная - меню', 390, mobile_menu, 'fh', 844),
 ('31 Мобильная - вход', 390, m_login, 'fh', 844), ('32 Мобильная - регистрация', 390, m_register, 'fh', 844), ('33 Мобильная - написать фотографу', 390, m_request, 'fh', 844),
 ('34 Мобильная - новая работа', 390, m_work, 'fh', 844), ('35 Мобильная - просмотр фото', 390, lightbox, 'fh', 844), ('36 Мобильная - дизайн-система', 390, design_system, '', None),
]
manifest = []
for name, w, fn, cls, h in FRAMES:
    m = (w == 390)
    html = page(fn(m), w, cls, h)
    path = OUT + name.replace(' ', '_') + '.html'
    open(path, 'w', encoding='utf8').write(html)
    manifest.append({'name': name, 'w': w, 'html': path, 'h': h})
json.dump(manifest, open(os.path.dirname(OUT[:-1]) + '/manifest.json', 'w', encoding='utf8'), ensure_ascii=False, indent=1)
print(len(manifest), 'кадров')
