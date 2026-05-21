<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/device.service.php';
require_once("./src/app/header/header-values.enum.php");

class Device extends TestCase
{
  protected function tearDown(): void
  {
    unset($_SERVER['HTTP_X_ORTHANC_DEVICE']);
  }

  public function testIsMobileReturnsTrue()
  {
    $_SERVER['HTTP_X_ORTHANC_DEVICE'] = HeaderValueDevice::Mobile->value;

    $service = new DeviceService();

    $this->assertTrue($service->isMobile());
    $this->assertFalse($service->isDesktop());
  }

  public function testIsDesktopReturnsTrue()
  {
    $_SERVER['HTTP_X_ORTHANC_DEVICE'] = HeaderValueDevice::Desktop->value;

    $service = new DeviceService();

    $this->assertTrue($service->isDesktop());
    $this->assertFalse($service->isMobile());
  }

  public function testIsMobileReturnsFalseForOtherValues()
  {
    $_SERVER['HTTP_X_ORTHANC_DEVICE'] = 'tablet';

    $service = new DeviceService();

    $this->assertFalse($service->isMobile());
    $this->assertFalse($service->isDesktop());
  }
}