import { ErrorCode } from "../enum";
import { EmptyArray, EmptyString } from "../../framework";

import { IBaseResponse } from "./base-response.model";

export interface ISuccessResponse<T> extends IBaseResponse<T> {
  errorCode: EmptyArray<ErrorCode>;
  message: EmptyString;
}
