<?php

function createSuccessResponse(
  object $payload
): object {
  $result = new stdClass();
  $result->correlationId = _getCorrelationId();
  $result->payload = $payload;
  return $result;
}

function createFailResponse(
  ErrorCode $errorCode,
  string $errorMessage,
): object {
  $result = new stdClass();
  $result->correlationId = _getCorrelationId();
  $result->payload = null;
  $result->errorCode = $errorCode;
  $result->message = $errorMessage;
  return $result;
}

function _getCorrelationId(): string {
  $data = random_bytes(16);
  $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
  $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
  return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}
