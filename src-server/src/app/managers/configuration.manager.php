<?php
class ConfigurationManager
{

  private $_versionService;
  private $_configurationService;

  public function __construct()
  {
    $this->_versionService = new VersionService();
    $this->_configurationService = new ConfigurationService();
  }

  public function getConfiguration(): object {
    $versionInfo = $this->_versionService->getVersion();
    $configuration = $this->_configurationService->getConfiguration();

    $configuration->version = $versionInfo->version;

    return $configuration;
  }

}
