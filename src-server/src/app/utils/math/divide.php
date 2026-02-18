<?php

/**
 * Divide two numbers and return the floor-rounded result
 *
 * @param float|int $a
 * @param float|int $b
 * @return int|string Returns the floored result or an error string if division by zero
 */
function divide($a, $b)
{
  if ($b == 0) {
    return "Error: Division by zero";
  }
  return (int) floor($a / $b);
}

?>