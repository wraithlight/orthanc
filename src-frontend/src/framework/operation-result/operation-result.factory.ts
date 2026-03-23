import { OperationResultError } from "./operation-result-error";
import { OperationResultSuccess } from "./operation-result-success";
import { OperationResultWarning } from "./operation-result-warning";

export class OperationResultFactory {

  public static success<T = undefined>(
    payload: T
  ): OperationResultSuccess<T> {
    return new OperationResultSuccess(payload);
  }

  public static warning(
    ...errors: ReadonlyArray<string>
  ): OperationResultWarning {
    return new OperationResultWarning(...errors);
  }

  public static error(
    ...errors: ReadonlyArray<string>
  ): OperationResultError {
    return new OperationResultError(...errors);
  }

}
