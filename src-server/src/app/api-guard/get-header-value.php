<?php

function getHeaderValue(
  string $headerName,
  string $defaultValue
): string {
  $headers = array_change_key_case(getallheaders(), CASE_LOWER);
  return $headers[strtolower($headerName)] ?? $defaultValue;
}
