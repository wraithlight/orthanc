<?php

function getHeaderValue(
  string $headerName,
  string $defaultValue
): string {
  $headers = getallheaders();
  return $headers[$headerName] ?? $defaultValue;
}
