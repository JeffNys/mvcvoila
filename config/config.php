<?php

/**
 * This file define config constants .
 *
 */
define('FORCE_HTTPS', false);

define('APP_PROD', false);

/**
 * translate constants
 * 
 */
define('TRANSLATE', true);
define('DEFAULT_LANG', 'en');
define('LANGS', ['en', 'fr']);

//Model (for connexion data, see unversionned db.php)
//VIew
define('APP_VIEW_PATH', __DIR__ . '/../src/View/');

// constants for voila
define('HOME_PAGE', 'home');
define('ROOT', __DIR__ . '/../');
