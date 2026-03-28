import { OperationResultError } from "./operation-result-error";
import { OperationResultSuccess } from "./operation-result-success";
import { OperationResultWarning } from "./operation-result-warning";

export class OperationResultFactory {

  public static success<T = undefined>(
    payload: T
  ): OperationResultSuccess<T> {
    return new OperationResultSuccess(payload);
  }

  public static warning<T = undefined>(
    payload: T,
    ...errors: ReadonlyArray<string>
  ): OperationResultWarning<T> {
    return new OperationResultWarning(payload, ...errors);
  }

  public static error(
    ...errors: ReadonlyArray<string>
  ): OperationResultError {
    return new OperationResultError(...errors);
  }

}
