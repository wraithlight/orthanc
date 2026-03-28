import type { OperationResultError } from "./operation-result-error";
import type { OperationResultSuccess } from "./operation-result-success";
import type { OperationResultWarning } from "./operation-result-warning";

type OperationResultSeverity = "SUCCESS" | "ERROR" | "WARNING";

export abstract class OperationResultBase {
  protected abstract severity: OperationResultSeverity;

  public isErrorTC(): this is OperationResultError {
    return this.severity === "ERROR";
  }

  public isWarnTC<T>(): this is OperationResultWarning<T> {
    return this.severity === "WARNING";
  }

  public isSuccessTC<T>(): this is OperationResultSuccess<T> {
    return this.severity === "SUCCESS";
  }
}
