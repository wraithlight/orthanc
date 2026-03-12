<?php

class DeviceService
{
  private string $_userAgent;

  public function __construct(?string $userAgent = null)
  {
    $this->_userAgent = $userAgent ?? ($_SERVER['HTTP_USER_AGENT'] ?? '');
  }

  public function isMobile(): bool
  {
    $mobileAgents = [
      'android',
      'webos',
      'iphone',
      'ipad',
      'ipod',
      'blackberry',
      'windows phone',
      'opera mini',
      'mobile'
    ];

    $ua = strtolower($this->_userAgent);

    foreach ($mobileAgents as $agent) {
      if (strpos($ua, $agent) !== false) {
        return true;
      }
    }

    return false;
  }

  public function isDesktop(): bool
  {
    return !$this->isMobile();
  }
}