<?php

function isHeaderValid(
  $value,
  array $allowedValues
): bool {
  return in_array($value, $allowedValues);
}
