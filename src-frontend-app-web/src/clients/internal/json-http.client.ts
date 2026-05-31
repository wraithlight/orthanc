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

  protected async get<TS, TW>(
    path: string
  ): Promise<OperationResult<TS, TW>> {
    const opRes = await super.get(path);
    return this.handleResult(opRes);
  }

  protected async post<T, US, UW>(
    path: string,
    payload?: T
  ): Promise<OperationResult<US, UW>> {
    const opRes = await super.post(path, JSON.stringify(payload));
    return this.handleResult(opRes);
  }

  protected async patch<T, US, UW>(
    path: string,
    payload?: T
  ): Promise<OperationResult<US, UW>> {
    const opRes = await super.patch(path, JSON.stringify(payload));
    return this.handleResult(opRes);
  }

  protected async put<T, US, UW>(
    path: string,
    payload?: T
  ): Promise<OperationResult<US, UW>> {
    const opRes = await super.patch(path, JSON.stringify(payload));
    return this.handleResult(opRes);
  }

  protected async delete<TS, TW>(
    path: string
  ): Promise<OperationResult<TS, TW>> {
    const opRes = await super.delete(path);
    return this.handleResult(opRes);
  }

  protected getJsonHeaders(): HeadersInit {
    return {
      [HeaderNames.Accept]: HeaderValueAccept.ApplicationJson,
      [HeaderNames.ContentType]: HeaderValuesContentType.ApplicationJson,
    };
  }

  private handleResult<TS, TW>(
    opRes: OperationResult<string>
  ): OperationResult<TS, TW> {
    if (opRes.isErrorTC()) {
      return opRes;
    }
    // TODO: fw-level cast<T>(a: unknown): T
    const result = JSON.parse(opRes.payload);

    if (opRes.isWarnTC()) {
      return OperationResultFactory.warning(result as TW, ...opRes.errors);
    }

    return OperationResultFactory.success(result as TS);
  }

}
