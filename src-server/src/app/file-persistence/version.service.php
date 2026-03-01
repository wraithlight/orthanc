<?php

class VersionService extends BaseIOService {

  protected string $filePath;

  public function __construct()
  {
    $envPath = getenv("VERSION_JSON_FILE_PATH") ?: "/../..";
    $this->filePath = __DIR__ . $envPath . "/version.json";
  }

  public function getVersion(): object
  {
    $readResult = $this->read();
    $versionInfo = new stdClass();
    $versionInfo->version = $readResult["version"];
    $versionInfo->gitHash = $readResult["gitHash"];
    $versionInfo->releaseCutDateUtc = $readResult["releaseCutDateUtc"];

    return $versionInfo;
  }

}
