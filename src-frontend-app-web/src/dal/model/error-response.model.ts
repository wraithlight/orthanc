import { ErrorCode } from "../enum";
import { NonEmptyArray } from "../../framework";

import { IBaseResponse } from "./base-response.model";

export interface IErrorResponse extends IBaseResponse<null | undefined> {
  errorCode: NonEmptyArray<ErrorCode>;
  message: string;
}
