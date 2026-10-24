<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/device.service.php';
require_once("./src/app/dal-generated/headers/names/header-names.enum.php");
require_once("./src/app/dal-generated/headers/values/header-values-x-orthanc-device.enum.php");

class Device extends TestCase
{
  protected function tearDown(): void
  {
    unset($_SERVER[HeaderNames::XOrthancDevice->value]);
  }

  public function testIsMobileReturnsTrue()
  {
    $_SERVER[HeaderNames::XOrthancDevice->value] = XOrthancDeviceValues::Mobile->value;

    $service = new DeviceService();

    $this->assertTrue($service->isMobile());
    $this->assertFalse($service->isDesktop());
  }

  public function testIsDesktopReturnsTrue()
  {
    $_SERVER[HeaderNames::XOrthancDevice->value] = XOrthancDeviceValues::Desktop->value;

    $service = new DeviceService();

    $this->assertTrue($service->isDesktop());
    $this->assertFalse($service->isMobile());
  }

  public function testIsMobileReturnsFalseForOtherValues()
  {
    $_SERVER[HeaderNames::XOrthancDevice->value] = 'tablet';

    $service = new DeviceService();

    $this->assertFalse($service->isMobile());
    $this->assertFalse($service->isDesktop());
  }
}