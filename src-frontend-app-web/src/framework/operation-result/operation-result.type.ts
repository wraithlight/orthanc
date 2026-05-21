import { OperationResultError } from "./operation-result-error";
import { OperationResultSuccess } from "./operation-result-success";
import { OperationResultWarning } from "./operation-result-warning";

export type OperationResult<T> =
  OperationResultSuccess<T>
  | OperationResultError
  | OperationResultWarning
;
