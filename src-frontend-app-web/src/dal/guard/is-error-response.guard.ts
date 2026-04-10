import { hasProperty } from "../../framework";
import { IErrorResponse } from "../model";

import { isBaseResponse as _isBaseResponse} from "./is-base-response.guard";

export function isErrorResponse(responseLike: unknown): responseLike is IErrorResponse {
  const isBaseResponse = _isBaseResponse(responseLike);
  const hasErrorCode = hasProperty(responseLike as IErrorResponse, "errorCode");
  const hasMessage = hasProperty(responseLike as IErrorResponse, "message");

  const isErrorCodeValid = true;
  const isMessageValid = true;

  return isBaseResponse && hasErrorCode && hasMessage && isErrorCodeValid && isMessageValid;
}
