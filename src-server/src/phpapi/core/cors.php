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
      header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    }
  }
}
?>