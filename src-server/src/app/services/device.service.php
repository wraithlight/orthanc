<?php

class DeviceService
{
  private string $_device;

  public function __construct()
  {
    $this->_device = $_SERVER['HTTP_X_ORTHANC_DEVICE'];
  }

  public function isMobile(): bool
  {
    return $this->_device === HeaderValueDevice::Mobile->value;
  }

  public function isDesktop(): bool
  {
    return $this->_device === HeaderValueDevice::Desktop->value;
  }
}