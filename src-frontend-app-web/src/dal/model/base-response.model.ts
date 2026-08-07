import { Guid } from "../../framework";

export interface IBaseResponse<TPayload> {
  correlationId: Guid;
  payload: TPayload;
}
