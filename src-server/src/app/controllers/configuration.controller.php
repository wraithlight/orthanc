<?php

class ConfigurationController
{

  private $_configurationManager;

  public function __construct()
  {
    $this->_configurationManager = new ConfigurationManager();
  }

  public function getConfiguration()
  {
    $payload = $this->_configurationManager->getConfiguration();
    echo json_encode(createSuccessResponse($payload));
  }

}
