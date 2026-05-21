<?php

function isOptionsRequest(): bool {
  return getRequestMethod() === RequestMethod::OPTIONS;
}
