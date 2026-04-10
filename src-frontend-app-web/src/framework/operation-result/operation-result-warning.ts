import { OperationResultBase } from "./operation-result-base";

export class OperationResultWarning<T = undefined> extends OperationResultBase {
  protected readonly severity = "WARNING";
  public readonly errors: ReadonlyArray<string>;
  public readonly payload: T;

  constructor(
    _payload: T,
    ..._errors: ReadonlyArray<string>
  ) {
    super();
    this.errors = _errors;
    this.payload = _payload;
  }
}
