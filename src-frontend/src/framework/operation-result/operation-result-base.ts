import type { OperationResultSuccess } from "./operation-result-success";
import type { OperationResultError } from "./operation-result-error";

export abstract class OperationResultBase {
  public abstract isSuccess: boolean;
  public abstract isError: boolean;

  public isErrorTC(): this is OperationResultError {
    return this.isError;
  }

  public isSuccessTC<T>(): this is OperationResultSuccess<T> {
    return this.isSuccess;
  }
}
