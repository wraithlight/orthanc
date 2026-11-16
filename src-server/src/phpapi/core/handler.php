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

          if ($paramName === $fnParam['Name']) {
            if ($fnParam['Type'] instanceof \ReflectionNamedType && !$fnParam['Type']->isBuiltin()) {
              $value = self::Hydrate($allParam->Value ?? null, $fnParam['Type']->getName());
            }
            array_push($result, $value ?? $allParam->Value ?? null);
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

    private static function Hydrate($data, string $className)
    {
        $object = new $className();
        $reflection = new \ReflectionClass($object);

        foreach ($data as $key => $value) {
          if (!$reflection->hasProperty($key)) {
            continue;
          }

          $property = $reflection->getProperty($key);
          $type = $property->getType();

          if ($type instanceof \ReflectionNamedType && !$type->isBuiltin() && $value !== null) {
            $propertyClass = $type->getName();

            if (enum_exists($propertyClass)) {
              $value = is_subclass_of($propertyClass, \BackedEnum::class)
                ? $propertyClass::from($value)
                : constant($propertyClass . '::' . $value);
              } else {
                $value = self::Hydrate($value, $propertyClass);
              }
          }
          $object->$key = $value;
        }

        return $object;
    }
  }
}
?>