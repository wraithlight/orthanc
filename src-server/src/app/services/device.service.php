<?php

class DeviceService
{
  private string $_device;

  public function __construct()
  {
    $this->_device = $_SERVER[HeaderNames::XOrthancDevice->value];
  }

  public function isMobile(): bool
  {
    return $this->_device === XOrthancDeviceValues::Mobile->value;
  }

  public function isDesktop(): bool
  {
    return $this->_device === XOrthancDeviceValues::Desktop->value;
  }
}