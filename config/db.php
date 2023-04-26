<?php

define('APP_DB', "sqlite");
// define('APP_DB', "mysql");

/**
 * This section define mysql access
 * if you use mysql
 */

define('APP_DB_USER', 'user');
define('APP_DB_PWD', 'password');
define('APP_DB_HOST', 'localhost');
define('APP_DB_NAME', 'database_name');

/**
 * This section define sqlite access
 */

define('APP_DB_PATH', __DIR__ . '/../SQLite/voila.db');
