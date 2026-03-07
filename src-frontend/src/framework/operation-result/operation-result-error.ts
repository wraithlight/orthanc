import { OperationResultBase } from "./operation-result-base";

export class OperationResultError extends OperationResultBase {
  public readonly isError = true;
  public readonly isSuccess = false;
  public readonly errors: ReadonlyArray<string>;


  constructor(
    ..._errors: ReadonlyArray<string>
  ) {
    super();
    this.errors = _errors;
  }
}
