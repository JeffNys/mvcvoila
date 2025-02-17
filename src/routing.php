<?php

/**
 * This file dispatch routes.
 */

$routeParts = explode('/', ltrim($_SERVER['REQUEST_URI'], '/') ?: HOME_PAGE);
if (TRANSLATE) {
    $locale = $_SESSION['locale'] ?? DEFAULT_LANG;
    $localeUrl = $routeParts[0] ?? '';
    if (in_array($localeUrl, LANGS)) {
        $_SESSION['locale'] = $localeUrl;
        $routesTranslations = Voila\Translate\GetRouteTranslation::getTrans($localeUrl);
        if ($routesTranslations) {
            // insensitive case search
            foreach ($routesTranslations as $key => $value) {
                if (strtolower($value) === strtolower($_SERVER['REQUEST_URI'])) {
                    $translatedRequest = $key;
                    break;
                }
            }
            $routeParts = explode('/', ltrim($translatedRequest, '/') ?: HOME_PAGE);
        }
    }
}

$controller = 'App\Controller\\' . ucfirst($routeParts[0] ?? '') . 'Controller';
$method = $routeParts[1] ?? 'index';
$vars = array_slice($routeParts, 2);

if (class_exists($controller) && method_exists(new $controller(), $method)) {
    echo call_user_func_array([new $controller(), $method], $vars);
} else {
    header("HTTP/1.0 404 Not Found");
    $controller = 'App\Controller\ErrorController';
    $method = 'error404';
    echo call_user_func_array([new $controller(), $method], []);
}
