<?php
class ConfigurationManager
{

  public function __construct()
  {
  }

  public function getConfiguration(): object {
    $defaultLanguage = "en";

    $payload = new stdClass();
    $payload->availableLocales = [
      "en",
    ];
    $payload->featureStates = [
      "applicationDefaultLanguageDefault" => [
        "value" => $defaultLanguage,
        "type" => "VALUE"
      ],
      "loginScreenLanguageSwitchEnabled" => [
        "value" => false,
        "type" => "FLAG"
      ],
      "loginScreenLanguageSwitchDefault" => [
        "value" => $defaultLanguage,
        "type" => "VALUE"
      ],
      "loginScreenModeScreenEnabled" => [
        "value" => false,
        "type" => "FLAG"
      ],
      "loginScreenModeScreenDefault" => [
        "value" => "VALUE",
        "type" => GameMode::Vanilla
      ],
    ];
    return $payload;
  }

}
