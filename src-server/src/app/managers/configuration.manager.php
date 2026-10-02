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

  public function getConfiguration(): GetConfigurationResponsePayload {
    $versionInfo = $this->_versionService->getVersion();
    $configuration = $this->_configurationService->getConfiguration();

    $result = new GetConfigurationResponsePayload();
    $result->availableLocales = $configuration->availableLocales;
    $result->featureStates = $configuration->featureStates;
    $result->version = $versionInfo->version;

    return $result;
  }

}
