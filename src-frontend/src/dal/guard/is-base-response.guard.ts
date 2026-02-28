import { hasProperty } from "../../framework";
import { IBaseResponse } from "../model";

export function isBaseResponse(responseLike: unknown): responseLike is IBaseResponse {
  const hasCorrelationId = hasProperty(responseLike as IBaseResponse, "correlationId");
  const hasPayload = hasProperty(responseLike as IBaseResponse, "payload");
  return hasCorrelationId && hasPayload;
}
