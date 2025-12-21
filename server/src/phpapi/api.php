<?php

namespace PhpAPI2 {
    require_once("utility/segment.php");
    require_once("utility/url.php");
    require_once("utility/params.php");
    require_once("utility/reflection.php");
    require_once("utility/path-cache.php");
    require_once("core/cors.php");
    require_once("core/json.php");
    require_once("core/listener.php");
    require_once("core/handler.php");

    class PhpAPI2Wrapper {
        const GET_HTTP_KEY = "GET";
        const POST_HTTP_KEY = "POST";
        const PUT_HTTP_KEY = "PUT";
        const PATCH_HTTP_KEY = "PATCH";
        const DELETE_HTTP_KEY = "DELETE";

        public static function RegisterPath(
            $requestType,
            $relativeUri,
            $controllerFactory,
            $method
        ) {
            self::RegisterPathInternal($requestType, $relativeUri, $controllerFactory, $method);
        }

        public static function RegisterGETPath(
            $relativeUri,
            $controllerFactory,
            $method
        ) {
            self::RegisterPathInternal(GET_HTTP_KEY, $relativeUri, $controllerFactory, $method);
        }

        public static function RegisterPOSTPath(
            $relativeUri,
            $controllerFactory,
            $method
        ) {
            self::RegisterPathInternal(POST_HTTP_KEY, $relativeUri, $controllerFactory, $method);
        }

        public static function RegisterPUTPath(
            $relativeUri,
            $controllerFactory,
            $method
        ) {
            self::RegisterPathInternal(PUT_HTTP_KEY, $relativeUri, $controllerFactory, $method);
        }

        public static function RegisterPATCHpath(
            $relativeUri,
            $controllerFactory,
            $method
        ) {
            self::RegisterPathInternal(PATCH_HTTP_KEY, $relativeUri, $controllerFactory, $method);
        }

        public static function RegisterDELETEPath(
            $relativeUri,
            $controllerFactory,
            $method
        ) {
            self::RegisterPathInternal(DELETE_HTTP_KEY, $relativeUri, $controllerFactory, $method);
        }

        public static function Listen(
            $enableCors = false,
            $enableJson = false
        ) {
            if($enableCors) {
                Cors::Enable();
            }

            if($enableJson) {
                Json::Enable();
            }
            $ref = Listener::Listen();
            $response = Handler::Handle($ref);
            echo $response;
        }

        private static function RegisterPathInternal(
            $requestType,
            $relativeUri,
            $controllerFactory,
            $method
        ) {            
            PathCache::Register($requestType, $relativeUri, $controllerFactory, $method);
        }
    }
}
?>
