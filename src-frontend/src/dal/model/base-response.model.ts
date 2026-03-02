import { Guid } from "../../framework";

export interface IBaseResponse<TPayload = unknown> {
  correlationId: Guid;
  payload: TPayload;
}
