<?php
/** POST: выход. */
declare(strict_types=1);
require __DIR__ . '/../bootstrap.php';
api_guard();
logout_user();
json_out(['ok' => true]);
