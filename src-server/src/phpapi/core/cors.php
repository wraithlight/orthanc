<?php
namespace PhpAPI2 {
  class Cors
  {
    public static function Use()
    {
      $isCorsEnabled = getenv("SERVER_CORS_ENABLED");
      $corsAllowedOrigins = getenv("SERVER_CORS_ALLOWED_ORIGINS");

      if (strtolower($isCorsEnabled) !== "true") return;

      header("Access-Control-Allow-Origin: $corsAllowedOrigins");
      header("Access-Control-Allow-Credentials: true");
      header("Access-Control-Expose-Headers: X-ORTHANC-PLATFORM-VERSION");
      header("Access-Control-Allow-Headers: X-Orthanc-Request-Id, X-Orthanc-Platform, Content-Type, Accept");
      header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

      if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { 
          http_response_code(200);
          exit;
      }
    }
  }
}
