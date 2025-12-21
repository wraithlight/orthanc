<?php
namespace PhpAPI2 {
    class Handler {
        public static function Handle($ref) {
            $controllerFactory = $ref->PathRef->ControllerFactory;
            $controller = $controllerFactory();
            $methodRef = $ref->PathRef->Method;
            $args = self::CreateArgsList(
                $controller,
                $methodRef,
                $ref->UriParams,
                $ref->BodyParams,
                $ref->QueryParams
            );
            return $controller->$methodRef(...$args);
        }
        private static function CreateArgsList(
            $controller,
            $methodRef,
            $uriParams,
            $bodyParams,
            $queryParams
        ) {
            $result = array();
            $fnParams = Reflection::GetFnParams($controller, $methodRef);
            $allParams = array_merge($uriParams, $bodyParams, $queryParams);

            foreach($fnParams as $fnParam) {
                $isAdded = false;
                foreach($allParams as $allParam) {
                    if($allParam->Name === $fnParam) {
                        array_push($result, $allParam->Value);
                        $isAdded = true;
                        break;
                    }
                }
                if(!$isAdded) {
                    array_push($result, NULL);
                }
            }

            return $result;
        }
    }
}
?>
