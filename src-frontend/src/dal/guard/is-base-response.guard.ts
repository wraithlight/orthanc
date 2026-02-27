import { hasProperty } from "../../framework";
import { IBaseResponse } from "../model";

export function isBaseResponse(responseLike: unknown): responseLike is IBaseResponse {
  const hasRequestId = hasProperty(responseLike as IBaseResponse, "requestId");
  const hasPayload = hasProperty(responseLike as IBaseResponse, "payload");
  return hasRequestId && hasPayload;
}
