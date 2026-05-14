import { HeaderNames, HeaderValueAccept, HeaderValuesContentType } from "../../domain";
import { OperationResult, OperationResultFactory } from "../../framework";
import { BaseHttpClient } from "./base-http.client";

export abstract class JsonHttpClient extends BaseHttpClient {

  constructor(
    protected readonly _baseUrl: string
  ) {
    super(_baseUrl);
  }

  protected getHttpHeaders(): HeadersInit {
    return this.getJsonHeaders();
  }

  protected async get<T>(
    path: string
  ): Promise<OperationResult<T>> {
    const opRes = await super.get(path);
    return this.handleResult(opRes);
  }

  protected async post<T, U>(
    path: string,
    payload?: T
  ): Promise<OperationResult<U>> {
    const opRes = await super.post(path, JSON.stringify(payload));
    return this.handleResult(opRes);
  }

  protected async patch<T, U>(
    path: string,
    payload?: T
  ): Promise<OperationResult<U>> {
    const opRes = await super.patch(path, JSON.stringify(payload));
    return this.handleResult(opRes);
  }

  protected async put<T, U>(
    path: string,
    payload?: T
  ): Promise<OperationResult<U>> {
    const opRes = await super.patch(path, JSON.stringify(payload));
    return this.handleResult(opRes);
  }

  protected async delete<T>(
    path: string
  ): Promise<OperationResult<T>> {
    const opRes = await super.delete(path);
    return this.handleResult(opRes);
  }

  protected getJsonHeaders(): HeadersInit {
    return {
      [HeaderNames.Accept]: HeaderValueAccept.ApplicationJson,
      [HeaderNames.ContentType]: HeaderValuesContentType.ApplicationJson,
    };
  }

  private handleResult<T>(
    opRes: OperationResult<string>
  ): OperationResult<T> {
    if (opRes.isErrorTC()) {
      return opRes;
    }
    // TODO: fw-level cast<T>(a: unknown): T
    const result = JSON.parse(opRes.payload) as T;

    if (opRes.isWarnTC()) {
      return OperationResultFactory.warning(result, ...opRes.errors);
    }

    return OperationResultFactory.success(result);
  }

}
