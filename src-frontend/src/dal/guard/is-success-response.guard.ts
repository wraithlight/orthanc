import { ISuccessResponse } from "../model";

import { isBaseResponse as _isBaseResponse} from "./is-base-response.guard";

export function isSuccessResponse(responseLike: unknown): responseLike is ISuccessResponse<unknown> {
  const isBaseResponse = _isBaseResponse(responseLike);

  const isPayloadValid = true;
  const isErrorCodeValid = true;
  const isMessageValid = true;

  return isBaseResponse && isPayloadValid && isErrorCodeValid && isMessageValid;
}
