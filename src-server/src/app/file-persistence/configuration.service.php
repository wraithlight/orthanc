<?php

class ConfigurationService extends BaseIOService {

  protected string $filePath;

  public function __construct()
  {
    $envPath = getenv("CONFIGURATION_JSON_FILE_PATH") ?: "/../..";
    $this->filePath = __DIR__ . $envPath . "/configuration.json";
  }

  public function getConfiguration(): object
  {
    $readResult = $this->read();
    return (object)$readResult;
  }

}
