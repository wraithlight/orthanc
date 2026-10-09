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

    $featureStatesLoginScreenLanguageSwitchDefault = new GetConfigurationResponsePayloadFeatureStatesLoginScreenLanguageSwitchDefault();
    $featureStatesLoginScreenLanguageSwitchDefaultRaw = $configuration->featureStates['loginScreenLanguageSwitchDefault'];
    $featureStatesLoginScreenLanguageSwitchDefault->value = $featureStatesLoginScreenLanguageSwitchDefaultRaw['value'];
    $featureStatesLoginScreenLanguageSwitchDefault->type = GetConfigurationResponsePayloadFeatureStatesLoginScreenLanguageSwitchDefaultType::from($featureStatesLoginScreenLanguageSwitchDefaultRaw['type']);
    
    $featureStatesApplicationDefaultLanguageDefault = new GetConfigurationResponsePayloadFeatureStatesApplicationDefaultLanguageDefault();
    $featureStatesApplicationDefaultLanguageDefaultRaw = $configuration->featureStates['applicationDefaultLanguageDefault'];
    $featureStatesApplicationDefaultLanguageDefault->value = GetConfigurationResponsePayloadFeatureStatesApplicationDefaultLanguageDefaultValue::from($featureStatesApplicationDefaultLanguageDefaultRaw['value']);
    $featureStatesApplicationDefaultLanguageDefault->type = GetConfigurationResponsePayloadFeatureStatesApplicationDefaultLanguageDefaultType::from($featureStatesApplicationDefaultLanguageDefaultRaw['type']);

    $featureStatesLoginScreenLanguageSwitchEnabled = new GetConfigurationResponsePayloadFeatureStatesLoginScreenLanguageSwitchEnabled();
    $featureStatesLoginScreenLanguageSwitchEnabledRaw = $configuration->featureStates['loginScreenLanguageSwitchEnabled'];
    $featureStatesLoginScreenLanguageSwitchEnabled->value = $featureStatesLoginScreenLanguageSwitchEnabledRaw['value'] ? 'true' : 'false';
    $featureStatesLoginScreenLanguageSwitchEnabled->type = GetConfigurationResponsePayloadFeatureStatesLoginScreenLanguageSwitchEnabledType::from($featureStatesLoginScreenLanguageSwitchEnabledRaw['type']);

    $featureStatesLoginScreenModeScreenDefault = new GetConfigurationResponsePayloadFeatureStatesLoginScreenModeScreenDefault();
    $featureStatesLoginScreenModeScreenDefaultRaw = $configuration->featureStates['loginScreenModeScreenDefault'];
    $featureStatesLoginScreenModeScreenDefault->value = $featureStatesLoginScreenModeScreenDefaultRaw['value'];
    $featureStatesLoginScreenModeScreenDefault->type = GetConfigurationResponsePayloadFeatureStatesLoginScreenModeScreenDefaultType::from($featureStatesLoginScreenModeScreenDefaultRaw['type']);

    $featureStatesLoginScreenModeScreenEnabled = new GetConfigurationResponsePayloadFeatureStatesLoginScreenModeScreenEnabled();
    $featureStatesLoginScreenModeScreenEnabledRaw = $configuration->featureStates['loginScreenModeScreenEnabled'];
    $featureStatesLoginScreenModeScreenEnabled->value = $featureStatesLoginScreenModeScreenEnabledRaw['value'] ? 'true' : 'false';
    $featureStatesLoginScreenModeScreenEnabled->type = GetConfigurationResponsePayloadFeatureStatesLoginScreenModeScreenEnabledType::from($featureStatesLoginScreenModeScreenEnabledRaw['type']);


    $featureStatesResult = new GetConfigurationResponsePayloadFeatureStates();
    $featureStatesResult->loginScreenLanguageSwitchDefault = $featureStatesLoginScreenLanguageSwitchDefault;
    $featureStatesResult->applicationDefaultLanguageDefault = $featureStatesApplicationDefaultLanguageDefault;
    $featureStatesResult->loginScreenLanguageSwitchEnabled = $featureStatesLoginScreenLanguageSwitchEnabled;
    $featureStatesResult->loginScreenModeScreenDefault = $featureStatesLoginScreenModeScreenDefault;
    $featureStatesResult->loginScreenModeScreenEnabled = $featureStatesLoginScreenModeScreenEnabled;

    $result = new GetConfigurationResponsePayload();
    $result->availableLocales = $configuration->availableLocales;
    $result->featureStates = $featureStatesResult;
    $result->version = $versionInfo->version;

    return $result;
  }

}
