<?php

class LocalizationController
{

  private $_localizationManager;

  public function __construct()
  {
    $this->_localizationManager = new LocalizationManager();
  }

  public function getLocalization(string $locale)
  {
    $appLocale = AppLocale::tryFrom($locale);

    if ($appLocale === null) {
      http_response_code(400);
      echo json_encode(createFailResponse(ErrorCode::ERROR_0400_LOCALE, "Invalid locale: $locale"));
      return;
    }

    $payload = $this->_localizationManager->getLocalization($appLocale);
    echo json_encode(createSuccessResponse($payload));
  }

}
