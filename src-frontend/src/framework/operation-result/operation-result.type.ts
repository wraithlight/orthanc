import { OperationResultError } from "./operation-result-error";
import { OperationResultSuccess } from "./operation-result-success";

export type OperationResult<T> =
  OperationResultSuccess<T>
  | OperationResultError;
