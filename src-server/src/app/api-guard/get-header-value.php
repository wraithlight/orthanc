<?php

function getHeaderValue(
  string $headerName,
  string $defaultValue
): string {
  return $_SERVER[$headerName] ?? $defaultValue;
}
