<?php

function isGuid(string $guidLike): bool
{
  $regex = '/^([a-fA-F0-9]{8}-[a-fA-F0-9]{4}-([1-9][a-fA-F0-9]{3})-([a-bA-B8-9][a-fA-F0-9]{3})-[a-fA-F0-9]{12})$/';

  return preg_match($regex, $guidLike) === 1;
}
