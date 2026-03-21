<?php

function createSuccessResponse(
  object $payload
): object {
  $result = new stdClass();
  $result->correlationId = _getCorrelationId();
  $result->payload = $payload;
  $result->requestId = _getRequestId();
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
  $result->requestId = _getRequestId();
  return $result;
}

function _getCorrelationId(): string {
  $data = random_bytes(16);
  $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
  $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
  return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

function _getRequestId(): string {
  // TODO: Remove mock logic as it is enforced to be a valid GUID on root API layer.
  $mockGuid = "07112ba2-1e54-45b0-8db1-69f8a4459af6";
  $header = getHeaderValue(HeaderName::RequestId->value, $mockGuid);
  
  return isGuid($header)
    ? $header
    : $mockGuid
  ;
}
