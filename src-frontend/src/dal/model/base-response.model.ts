import { Guid } from "../../framework";

export interface IBaseResponse<TPayload = unknown> {
  requestId: Guid;
  payload: TPayload;
}
