<?php

class LocalizationManager {

  private $_localizationService;

  public function __construct()
  {
    $this->_localizationService = new LocalizationService();
  }

  public function getLocalization(
    AppLocale $locale
  ): GetLocalizationLocaleResponsePayload {
    $localization = $this->_localizationService->getLocalizationForLocale($locale->value);
    return new GetLocalizationLocaleResponsePayload($localization);
  }

}
