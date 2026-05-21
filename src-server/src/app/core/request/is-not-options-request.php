<?php

function isNotOptionsRequest(): bool {
  return getRequestMethod() !== RequestMethod::OPTIONS;
}
