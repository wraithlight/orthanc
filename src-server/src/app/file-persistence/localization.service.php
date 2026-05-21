<?php
class LocalizationService extends BaseIOService
{
  protected string $filePath;

  public function getLocalizationForLocale(
    string $locale,
  ): array
  {
    $envPath = getenv("LOCALIZATION_FILES_PATH") ?: "/../..";
    $this->filePath = __DIR__ . $envPath ."/$locale.json";
    $localization = $this->read();

    return $localization;
  }

}
