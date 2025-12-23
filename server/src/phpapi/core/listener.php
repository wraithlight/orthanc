<?php
namespace PhpAPI2 {
  class CallableReference
  {
    public function __construct(
      $pathRef,
      $uriParams,
      $bodyParams,
      $queryParams
    ) {
      $this->PathRef = $pathRef;
      $this->UriParams = $uriParams;
      $this->BodyParams = $bodyParams;
      $this->QueryParams = $queryParams;
    }
  }
  class Listener
  {
    public static function Listen()
    {
      $requestUrl = $_SERVER["REQUEST_URI"];
      $requestMethod = $_SERVER["REQUEST_METHOD"];

      error_log($requestUrl);
      error_log($requestMethod);

      $chopped = self::ChopQueryParams($requestUrl);

      $paths = PathCache::GetAllFilteredByRequestType($requestMethod);
      if (is_array($paths) && count($paths) === 0)
        throw new \Error("The request method was not found!");

      $requestedPathRef = Url::GetRequestedPath($chopped[0], $paths);
      if (is_null($requestedPathRef))
        throw new \Error("The requested path was not found!");

      $requestUriParams = Params::GetUriParams($chopped[0], $requestedPathRef->RelativeUri);
      $requestBodyParams = Params::GetBodyParams($_SERVER);
      $requestQueryParams = Params::GetQueryParams($chopped[1]);

      return new CallableReference($requestedPathRef, $requestUriParams, $requestBodyParams, $requestQueryParams);
    }

    private static function ChopQueryParams($uri)
    {
      $chopped = explode("?", $uri);
      count($chopped) === 1 && array_push($chopped, "");
      return $chopped;
    }
  }
}