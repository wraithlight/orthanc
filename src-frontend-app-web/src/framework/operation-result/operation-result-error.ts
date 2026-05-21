import { OperationResultBase } from "./operation-result-base";

export class OperationResultError extends OperationResultBase {
    protected readonly severity = "ERROR";
  public readonly errors: ReadonlyArray<string>;


  constructor(
    ..._errors: ReadonlyArray<string>
  ) {
    super();
    this.errors = _errors;
  }
}
