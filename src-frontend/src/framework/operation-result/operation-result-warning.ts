import { OperationResultBase } from "./operation-result-base";

export class OperationResultWarning extends OperationResultBase {
  protected readonly severity = "WARNING";
  public readonly errors: ReadonlyArray<string>;


  constructor(
    ..._errors: ReadonlyArray<string>
  ) {
    super();
    this.errors = _errors;
  }
}
