import { OperationResultBase } from "./operation-result-base";

export class OperationResultSuccess<T = undefined>
  extends OperationResultBase {
  protected readonly severity = "SUCCESS";
  public readonly payload: T;

  constructor(
    _payload: T
  ) {
    super();
    this.payload = _payload;
  }
}
