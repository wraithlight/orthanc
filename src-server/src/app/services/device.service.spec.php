<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/device.service.php';

class Device extends TestCase
{

  public function mobileUserAgentsProvider(): array
  {
    return [
      ['Mozilla/5.0 (iPhone; CPU iPhone OS 14_0 like Mac OS X) AppleWebKit/605.1.15', true],
      ['Mozilla/5.0 (Linux; Android 10; SM-G973F) AppleWebKit/537.36', true],
      ['Mozilla/5.0 (iPad; CPU OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15', true],
      ['Mozilla/5.0 (Windows Phone 10.0; Android 6.0; Microsoft; RM-1152)', true],
      ['Mozilla/5.0 (BlackBerry; U; BlackBerry 9900)', true],
    ];
  }

  public function desktopUserAgentsProvider(): array
  {
    return [
      ['Mozilla/5.0 (Windows NT 10.0; Win64; x64)', false],
      ['Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7)', false],
      ['Mozilla/5.0 (X11; Linux x86_64)', false],
      ['', false],
    ];
  }

  /**
   * @dataProvider mobileUserAgentsProvider
   */
  public function testIsMobileReturnsTrueForMobileAgents(string $userAgent, bool $expected)
  {
    $detector = new DeviceService($userAgent);
    $this->assertSame($expected, $detector->isMobile());
    $this->assertSame(!$expected, $detector->isDesktop());
  }

  /**
   * @dataProvider desktopUserAgentsProvider
   */
  public function testIsDesktopReturnsTrueForDesktopAgents(string $userAgent, bool $expected)
  {
    $detector = new DeviceService($userAgent);
    $this->assertSame($expected, $detector->isMobile());
    $this->assertSame(!$expected, $detector->isDesktop());
  }

  public function testCustomUserAgentCanBeOverridden()
  {
    $detector = new DeviceService('custom mobile agent android');
    $this->assertTrue($detector->isMobile());
    $this->assertFalse($detector->isDesktop());
  }

  public function testNoUserAgentDefaultsToDesktop()
  {
    $detector = new DeviceService('');
    $this->assertFalse($detector->isMobile());
    $this->assertTrue($detector->isDesktop());
  }
}