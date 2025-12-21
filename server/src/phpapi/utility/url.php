<?php
namespace PhpAPI2 {
    class Url {
        public static function GetRequestedPath($request, $paths) {
            foreach($paths as $path) {
                if(self::IsPathMatch($request, $path->RelativeUri)) {
                    return $path;
                }
            }
            return NULL;
        }

        private static function IsPathMatch($request, $path) {
            $pathSegments = Segments::ToNodes($path);
            $requestSegments = Segments::ToNodes($request);
            if(count($pathSegments) !== count($requestSegments)) return false;
            for($i = 0; $i < count($pathSegments); $i++) {
                if(!Segments::IsParameter($pathSegments[$i]) && !self::IsSegmentsMatch($pathSegments[$i], $requestSegments[$i])) return false;
            }
            return true;
        }

        private static function IsSegmentsMatch($left, $right) {
            return $left === $right;
        }
    }
}
?>
