<?php

$controllerPath = __DIR__ . '/../src/Controller/';
$viewPath = __DIR__ . '/../src/View/';

$controllers = findFiles($controllerPath);
$views = findFiles($viewPath);

$sentences = [];

foreach ($controllers as $controller) {
  if (strpos($controller, "AbstractController.php")) continue;
  $newSentences = getTranslate($controller);
  if ($newSentences) {
    foreach ($newSentences as $newSentence) {
      if ($newSentence) {
        $sentences[] = $newSentence;
      }
    }
  }
}

foreach ($views as $view) {
  $newSentences = getTrans($view);
  if ($newSentences) {
    foreach ($newSentences as $newSentence) {
      if ($newSentence) {
        $sentences[] = $newSentence;
      }
    }
  }
}

var_dump($sentences);










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
    if (is_file("$directory/$value")) {
      $result[] = "$directory/$value";
      continue;
    }
    foreach (findFiles("$directory/$value") as $value) {
      $result[] = $value;
    }
  }
  return $result;
}

function getTranslate(string $file): array
{
  $sentences = [];
  if (is_file($file)) {
    $content = file_get_contents($file);
    $offset = 0;
    $infiniteLoop = 1000;
    do {
      $pos = 0;
      $pos = strpos($content, 'translate', $offset);
      if ($pos) {
        preg_match("/translate\\([^)]*\\)/i", $content, $sentencesInFile, PREG_OFFSET_CAPTURE, $offset);
        $offset = $sentencesInFile[0][1];
        $offset += 11;
        $sentence = $sentencesInFile[0][0];
        // $sentence = strtr("translate(\"", $sentence, "");
        $sentence = substr($sentence, 11, -2);
        $sentences[] = $sentence;
        $sentencesInFile = [];
      } else {
        $pos = 0;
      }
      if ($infiniteLoop < 0) {
        $pos = 0;
      } else {
        $infiniteLoop--;
      }
    } while ($pos);
  }
  return $sentences;
}

function getTrans(string $file): array
{
  $sentences = [];
  if (is_file($file)) {

    $content = file_get_contents($file);
    $offset = 0;
    $infiniteLoop = 1000;
    do {
      $pos = 0;
      $pos = strpos($content, 'trans', $offset);
      if ($pos) {
        preg_match("/\{% trans %\}[A-Za-z]+\{% endtrans %\}/i", $content, $sentencesInFile, PREG_OFFSET_CAPTURE, $offset);
        $offset = $sentencesInFile[0][1] ?? $offset;
        $offset += 11;
        $sentence = $sentencesInFile[0][0] ?? "{% trans %}{% endtrans %}";
        // $sentence = strtr("translate(\"", $sentence, "");
        $sentence = substr($sentence, 11, -14);
        if ($sentence) {
          $sentences[] = $sentence;
        }
        $sentencesInFile = [];
      } else {
        $pos = 0;
      }
      if ($infiniteLoop < 0) {
        $pos = 0;
      } else {
        $infiniteLoop--;
      }
    } while ($pos);
  }
  return $sentences;
}
