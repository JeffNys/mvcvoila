<?php
// 12 hours server session life
session_start([
    "cookie_lifetime" => 0,
    "gc_maxlifetime" => 43200,
    "name" => "voila_sess_id",
    "cookie_httponly" => true,
    "cookie_samesite" => "Strict",
]);

require_once __DIR__ . '/../vendor/autoload.php';

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';
if (!APP_PROD) {
    require_once __DIR__ . '/../config/debug.php';
}

require_once __DIR__ . '/../src/routing.php';
