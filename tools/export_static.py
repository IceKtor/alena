#!/usr/bin/env python3
"""
Статическая демо-копия сайта для GitHub Pages (PHP там не работает).

Запуск:
    php -S 127.0.0.1:8000 router.php        # в одном окне
    python3 tools/export_static.py OUT_DIR  # в другом

Скрипт обходит страницы локального сервера, сохраняет их как .html,
переписывает ссылки (photographer.php?id=3 → photographer-3.html и т. д.)
и копирует css/, js/, img/. Кабинет сохраняется от имени демо-фотографа.
Формы в копии не отправляются: js/app.js видит атрибут data-static и
показывает подсказку.
"""
import html
import http.cookiejar
import json
import os
import re
import shutil
import sys
import urllib.parse
import urllib.request

BASE = 'http://127.0.0.1:8000/'
ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
OUT = os.path.abspath(sys.argv[1] if len(sys.argv) > 1 else os.path.join(ROOT, '_static'))
GENRES = ['portrait', 'landscape', 'reportage', 'still', 'wedding']
SECTIONS = ['profile', 'requests', 'stats', 'settings']


def static_name(href: str) -> str | None:
    """Адрес PHP-страницы → имя статического файла (None — не трогать)."""
    if re.match(r'^(https?:|mailto:|tel:|#|data:)', href):
        return None
    u = urllib.parse.urlsplit(html.unescape(href))
    path, q = u.path, urllib.parse.parse_qs(u.query)
    frag = ('#' + u.fragment) if u.fragment else ''
    first = lambda k: (q.get(k) or [''])[0]
    if path in ('', 'index.php'):
        return 'index.html' + frag if path or u.query else None
    if path == 'photographers.php':
        page = first('page')
        only_page = set(q) <= {'page'}
        return (f'photographers-{page}.html' if page and only_page and page != '1' else 'photographers.html') + frag
    if path == 'photographer.php':
        return f'photographer-{first("id") or 1}.html' + frag
    if path == 'works.php':
        g = first('genre')
        return (f'works-{g}.html' if g in GENRES else 'works.html') + frag
    if path == 'cabinet.php':
        s = first('section')
        return (f'cabinet-{s}.html' if s in SECTIONS else 'cabinet.html') + frag
    if path in ('about.php', 'privacy.php', 'favorites.php', '404.php'):
        return path.replace('.php', '.html') + frag
    return None


def rewrite(page: str) -> str:
    def fix(m):
        attr, val = m.group(1), m.group(2)
        new = static_name(val)
        return f'{attr}="{html.escape(new, quote=True)}"' if new else m.group(0)
    page = re.sub(r'\b(href|action)="([^"]*)"', fix, page)
    page = page.replace('<html lang="ru">', '<html lang="ru" data-static>', 1)
    return page


def main():
    jar = http.cookiejar.CookieJar()
    opener = urllib.request.build_opener(urllib.request.HTTPCookieProcessor(jar))
    get = lambda p: opener.open(BASE + p).read().decode('utf-8')

    def save(src, name):
        try:
            text = get(src)
        except urllib.error.HTTPError as e:  # 404 и 401 тоже сохраняем
            text = e.read().decode('utf-8')
        with open(os.path.join(OUT, name), 'w', encoding='utf-8') as f:
            f.write(rewrite(text))
        print('  ', name)

    if os.path.isdir(OUT):
        for item in os.listdir(OUT):
            if item == '.git':
                continue
            p = os.path.join(OUT, item)
            shutil.rmtree(p) if os.path.isdir(p) else os.remove(p)
    os.makedirs(OUT, exist_ok=True)
    for d in ('css', 'js', 'img'):
        shutil.copytree(os.path.join(ROOT, d), os.path.join(OUT, d))
    open(os.path.join(OUT, '.nojekyll'), 'w').close()

    print('Страницы для гостя:')
    for src, name in [('index.php', 'index.html'), ('about.php', 'about.html'), ('privacy.php', 'privacy.html'),
                      ('favorites.php', 'favorites.html'), ('404.php', '404.html')]:
        save(src, name)
    first = get('photographers.php')
    pages = max([int(n) for n in re.findall(r'photographers\.php\?page=(\d+)', first)] + [1])
    save('photographers.php', 'photographers.html')
    for n in range(2, pages + 1):
        save(f'photographers.php?page={n}', f'photographers-{n}.html')
    get('photographers.php')  # «Назад в каталог» в профилях должна вести на первую страницу
    for pid in range(1, 19):
        save(f'photographer.php?id={pid}', f'photographer-{pid}.html')
    save('works.php?shown=500', 'works.html')
    for g in GENRES:
        save(f'works.php?genre={g}&shown=500', f'works-{g}.html')

    print('Кабинет демо-фотографа:')
    token = re.search(r'name="csrf-token" content="([^"]+)"', get('index.php')).group(1)
    req = urllib.request.Request(BASE + 'server/api/login.php', method='POST',
                                 data=urllib.parse.urlencode({'email': 'foto@demo.ru', 'password': 'demo12345'}).encode(),
                                 headers={'X-CSRF-Token': token})
    assert json.loads(opener.open(req).read())['ok'], 'не удалось войти демо-фотографом'
    save('cabinet.php', 'cabinet.html')
    for s in SECTIONS:
        save(f'cabinet.php?section={s}', f'cabinet-{s}.html')
    print('Готово:', OUT)


if __name__ == '__main__':
    main()
