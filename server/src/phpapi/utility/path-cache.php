<?php
namespace PhpAPI2 {
    class CachedPath {
        function __construct(
            $requestType,
            $relativeUri,
            $controllerFactory,
            $method
        ) {
            $this->RequestType = $requestType;
            $this->RelativeUri = $relativeUri;
            $this->ControllerFactory = $controllerFactory;
            $this->Method = $method;
        }
    }

    class PathCache {
        private static $_cache = array();

        public static function Register($requestType, $relativeUri, $controllerFactory, $method) {
            array_push(self::$_cache, new CachedPath($requestType, $relativeUri, $controllerFactory, $method));
        }

        public static function GetAllFilteredByRequestType($requestType) {
            return array_filter(self::$_cache, function($m) use ($requestType) { return $m->RequestType === $requestType; });
        }
    }
}
?>
