<?php

class ConfigService extends BaseIOService {

  protected string $filePath;

  public function __construct()
  {
    $this->filePath = __DIR__ . getenv("CONFIG_JSON_FILE_PATH") . "/config.json";
  }

  public function getVersion(): string
  {
    $config = $this->read();
    $version = $config['version'];

    return $version;
  }

}
