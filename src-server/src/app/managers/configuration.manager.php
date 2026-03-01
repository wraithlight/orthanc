<?php
class ConfigurationManager
{

  public function __construct()
  {
  }

  public function getConfiguration(): object {
    $payload = new stdClass();
    $payload->availableLocales = [
      "en",
      // "hr", "hu", "de"
    ];
    $payload->featureStates = [
      [
        "key" => "applicationDefaultLanguage",
        "value" => "en"
      ],
      [
        "key" => "loginScreenLanguageSwitch",
        "isEnabled" => false,
      ],
      [
        "key" => "loginScreenModeScreen",
        "isEnabled" => false,
        "defaultValue" => "Vanilla"
      ]
    ];
    return $payload;
  }

}
