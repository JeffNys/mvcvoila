<?php

namespace Voila\Translate;

class GetTranslation
{
  private static array $translations;

  private static $defaultLang = DEFAULT_LANG;
  private static $translationsPath = ROOT . 'translation/';

  private function __construct(string $locale)
  {
    self::$translations = self::getTranslations($locale);
    if (!self::$translations) {
      self::$translations = self::getTranslations(self::$defaultLang);
    }
  }

  private function getTranslations(string $locale): array
  {
    if (file_exists(self::$translationsPath . $locale . '.json')) {
      return json_decode(file_get_contents(self::$translationsPath . $locale . '.json'), true);
    } else {
      return [];
    }
  }

  public static function getTrans(string $locale): array
  {
    if (!isset(self::$translations)) {
      new self($locale);
    }
    return self::$translations;
  }
}
