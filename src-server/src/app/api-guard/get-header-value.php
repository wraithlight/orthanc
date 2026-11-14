<?php

function getHeaderValue(
  string $headerName,
  string $defaultValue
): string {
  $headers = array_change_key_case(getallheaders(), CASE_UPPER);
  return $headers[strtoupper($headerName)] ?? $defaultValue;
}
