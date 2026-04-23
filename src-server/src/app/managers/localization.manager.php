<?php

class LocalizationManager {

  private $_localizationService;

  public function __construct()
  {
    $this->_localizationService = new LocalizationService();
  }

  public function getLocalization(
    Locale $locale
  ): object {
    $localization = $this->_localizationService->getLocalizationForLocale($locale->value);
    return (object) $localization;
  }

}
