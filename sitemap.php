<?php
/** sitemap.xml с профилями фотографов (ТЗ 16.1). TODO(DB): список профилей берётся из базы автоматически. */
declare(strict_types=1);
require __DIR__ . '/server/bootstrap.php';
$scheme = !empty($_SERVER['HTTPS']) ? 'https' : 'http';
$base = $scheme . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\') . '/';
$urls = ['index.php', 'photographers.php', 'works.php', 'about.php', 'privacy.php'];
foreach (photographers() as $p) {
    $urls[] = url('photographer.php', ['id' => $p['id']]);
}
header('Content-Type: application/xml; charset=utf-8');
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
foreach ($urls as $u) {
    echo '  <url><loc>' . htmlspecialchars($base . $u, ENT_XML1) . '</loc></url>' . "\n";
}
echo '</urlset>' . "\n";
