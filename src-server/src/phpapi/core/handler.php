<?php
namespace PhpAPI2 {
  class Handler
  {
    public static function Handle($ref)
    {
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
      $result = [];
      $fnParams = Reflection::GetFnParams($controller, $methodRef);
      $allParams = array_merge($uriParams, $bodyParams, $queryParams);

      foreach ($fnParams as $fnParam) {
        $isAdded = false;
        foreach ($allParams as $allParam) {
          $paramName = property_exists($allParam, 'Name')
            ? self::kebabToCamel($allParam->Name)
            : $allParam->Name ?? null;

          if ($paramName === $fnParam) {
            array_push($result, $allParam->Value ?? null);
            $isAdded = true;
            break;
          }
        }
        if (!$isAdded) {
          array_push($result, null);
        }
      }

      return $result;
    }

    private static function kebabToCamel(string $str): string
    {
      return lcfirst(str_replace(' ', '', ucwords(str_replace('-', ' ', $str))));
    }

  }
}
?>