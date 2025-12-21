<?php
namespace PhpAPI2 {
    const SEGMENT_SEPARATOR = "/";
    const PARAM_MARK = ":";
    class Segments {
        public static function ToNodes($uri) {
            return explode(SEGMENT_SEPARATOR, $uri);
        }
        public static function IsParameter($segment) {
            return substr($segment, 0, strlen(PARAM_MARK)) === PARAM_MARK;

        }
        public static function NormalizeParameter($segment) {
            $length = strlen($segment);
            return substr($segment, 1, $length - 1);           
        }
    }
}
?>
