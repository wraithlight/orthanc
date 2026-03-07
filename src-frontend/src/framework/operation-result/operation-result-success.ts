import { OperationResultBase } from "./operation-result-base";

export class OperationResultSuccess<T = undefined>
  extends OperationResultBase {
  public readonly isError = false;
  public readonly isSuccess = true;
  public readonly payload: T;

  constructor(
    _payload: T
  ) {
    super();
    this.payload = _payload;
  }
}
