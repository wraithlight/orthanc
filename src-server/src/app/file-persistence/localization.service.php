<?php
class LocalizationService extends BaseIOService
{
  protected string $filePath;

  public function getLocalizationForLocale(
    string $locale,
  ): array
  {
    $this->filePath = __DIR__ . getenv("LOCALIZATION_FILES_PATH") ."/$locale.json";
    $localization = $this->read();

    return $localization;
  }

}
