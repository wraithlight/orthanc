<?php
namespace PhpAPI2 {
  class Cors
  {
    public static function Enable()
    {
      header('Access-Control-Allow-Origin: http://localhost:3000');
      header("Access-Control-Allow-Credentials: true");
      header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    }
  }
}
?>