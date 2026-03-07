import { OperationResultError } from "./operation-result-error";
import { OperationResultSuccess } from "./operation-result-success";

export class OperationResultFactory {

  public static success<T = undefined>(
    payload: T
  ): OperationResultSuccess<T> {
    return new OperationResultSuccess(payload);
  }

  public static error(
    ...errors: ReadonlyArray<string>
  ): OperationResultError {
    return new OperationResultError(...errors);
  }

}
