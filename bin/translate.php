<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../vendor/autoload.php';

$controllerPath = __DIR__ . '/../src/Controller/';
$viewPath = __DIR__ . '/../src/View/';
$transPath = __DIR__ . '/../translation/';

$controllers = findFiles($controllerPath);
$views = findFiles($viewPath);

$sentences = [];

// find all sentences in controllers
foreach ($controllers as $controller) {
  $tempResult = strpos($controller["file"], "AbstractController.php");
  if (gettype($tempResult) == "integer") {
    $abstractController = true;
  } else {
    $abstractController = false;
  }
  if (!$abstractController) {
    $newSentences = getTranslate($controller);
    if ($newSentences) {
      foreach ($newSentences as $newSentence) {
        if ($newSentence) {
          $sentences[] = $newSentence;
        }
      }
    }
  }
}

// find all sentences in views
foreach ($views as $view) {
  $newSentences = getTranslate($view);
  if ($newSentences) {
    foreach ($newSentences as $newSentence) {
      if ($newSentence) {
        $sentences[] = $newSentence;
      }
    }
  }
}



// check if the language is supported
if (!empty($argv[1])) {
  $locale = $argv[1];
  if (!in_array($locale, LANGS)) {
    echo "This language is not supported\n";
    echo "Supported languages: " . implode(", ", LANGS) . "\n";
    echo "Default language is " . DEFAULT_LANG . "\n";
    echo "You can add a new language in config/config.php\n";
    $locale = DEFAULT_LANG;
  }
} else {
  $locale = DEFAULT_LANG;
}

// create or update translation file
if (file_exists($transPath . "$locale.json")) {
  $translations = json_decode(file_get_contents($transPath . "$locale.json"), true);
  foreach ($sentences as $sentence) {
    if (!array_key_exists($sentence, $translations)) {
      $translations[$sentence] = "__$sentence";
    }
  }
  $translations = json_encode($translations, JSON_PRETTY_PRINT);
  file_put_contents($transPath . "$locale.json", $translations);
  echo "File $locale.json updated in translation folder\n";
} else {
  $translations = [];
  foreach ($sentences as $sentence) {
    $translations[$sentence] = "__$sentence";
  }
  $translations = json_encode($translations, JSON_PRETTY_PRINT);
  file_put_contents($transPath . "$locale.json", $translations);
  echo "File $locale.json created in translation folder\n";
}

// find all routes
if ($locale !== DEFAULT_LANG) {
  $routes = [];

  foreach ($controllers as $controller) {
    $tempResult = strpos($controller["file"], "AbstractController.php");
    if (gettype($tempResult) == "integer") {
      $abstractController = true;
    } else {
      $abstractController = false;
    }
    if (!$abstractController) {
      $pathFile = $controller["directory"] . "/" . $controller["file"];
      if (is_file($pathFile)) {
        $controllerName = str_replace(".php", "", $pathFile);
        $controllerRoute = str_replace("Controller.php", "", $controller["file"]);
        $controllerWithNamespace = 'App\Controller\\' . $controllerRoute . 'Controller';
        $controller = new $controllerWithNamespace();
        $methodsInController = get_class_methods($controller);
        // add default index route
        $routes[] = $controllerRoute;
        foreach ($methodsInController as $method) {
          if ($method !== "__construct") {
            $methodInParent = new ReflectionMethod($controllerWithNamespace, $method);
            if ($methodInParent->class !== "App\Controller\AbstractController") {
              $routes[] = $controllerRoute . "/" . $method;
            }
          }
        }
      }
    }
  }

  // create or update route translation file
  if (file_exists($transPath . "routes$locale.json")) {
    $routeTranslations = json_decode(file_get_contents($transPath . "routes$locale.json"), true);
    foreach ($routes as $route) {
      if (!array_key_exists($route, $routeTranslations)) {
        $routeTranslations["/$route"] = "/$locale/$route";
      }
    }
    // add default route
    $homePage = HOME_PAGE;
    $routeTranslations["/$homePage"] = "/$locale";
    $routeTranslations = json_encode($routeTranslations, JSON_PRETTY_PRINT);
    file_put_contents($transPath . "routes$locale.json", $routeTranslations);
    echo "File routes$locale.json updated in translation folder\n";
  } else {
    $routeTranslations = [];
    foreach ($routes as $route) {
      $routeTranslations["/$route"] = "/$locale/$route";
    }
    // add default route
    $homePage = HOME_PAGE;
    $routeTranslations["/$homePage"] = "/$locale";
    $routeTranslations = json_encode($routeTranslations, JSON_PRETTY_PRINT);
    file_put_contents($transPath . "routes$locale.json", $routeTranslations);
    echo "File routes$locale.json created in translation folder\n";
  }
}










/**
 * Functions
 */

function findFiles(string $directory): array
{
  $root = scandir($directory);
  foreach ($root as $value) {
    if ($value === '.' || $value === '..') {
      continue;
    }
    if (is_file($directory . $value)) {
      $result[] = [
        "file" => "$value",
        "directory" => "$directory",
      ];
    } else {
      $subdir = findFiles($directory . $value . '/');
      foreach ($subdir as $value) {
        $result[] = $value;
      }
    }
  }
  return $result;
}

function getTranslate(array $file): array
{
  $sentences = [];
  $pathFile = $file["directory"] . "/" . $file["file"];
  if (is_file($pathFile)) {
    $content = file_get_contents($pathFile);
    $sentencesInFile = [];
    // trouver toute les chaines de caractère comprises entre translate(" et ")
    preg_match_all("/translate\(\"(.*?)\"\)/i", $content, $sentencesInFile);
    foreach ($sentencesInFile[1] as $sentence) {
      $sentences[] = $sentence;
    }
    preg_match_all("/translate\('(.*?)'\)/i", $content, $sentencesInFile2);
    foreach ($sentencesInFile2[1] as $sentence) {
      $sentences[] = $sentence;
    }
  }
  return $sentences;
}
