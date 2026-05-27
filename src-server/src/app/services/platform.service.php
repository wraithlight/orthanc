<?php

class PlatformService
{
  public function getPlatform(): Platform
  {
    $defaultPlatform = Platform::Web;

    $headerKey = 'HTTP_X_ORTHANC_PLATFORM';
    $platform = isset($_SERVER[$headerKey])
      ? $_SERVER[$headerKey]
      : $defaultPlatform
    ;

    return Platform::tryFrom($platform) ?? $defaultPlatform;
  }
}
