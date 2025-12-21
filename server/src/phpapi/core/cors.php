<?php
namespace PhpAPI2 {
    class Cors {
        public static function Enable() {
            header('Access-Control-Allow-Origin: *');
        }
    }
}
?>