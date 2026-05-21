<?php

function getRequestMethod(): RequestMethod {
  $method = $_SERVER['REQUEST_METHOD'];
  return RequestMethod::tryFrom($method) ?? RequestMethod::NOT_DETERMINED;
}
