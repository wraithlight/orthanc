<?php

class ConfigService extends BaseIOService {

  protected string $filePath;

  public function __construct()
  {
    $envPath = getenv("CONFIG_JSON_FILE_PATH") ?: "/../..";
    $this->filePath = __DIR__ . $envPath . "/config.json";
  }

  public function getVersion(): string
  {
    $config = $this->read();
    $version = $config['version'];

    return $version;
  }

}
