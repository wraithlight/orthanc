<?php
namespace PhpAPI2 {
  class Json
  {
    public static function Enable()
    {
      header('Content-Type: application/json');
    }
  }
}
?>