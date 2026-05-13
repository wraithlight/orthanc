import { isNil, Nullable, OperationResult, OperationResultFactory } from "../../framework";
import { Credentials } from "./base-http.model";

export abstract class BaseHttpClient {

  protected abstract getHttpHeaders(): HeadersInit;
  protected abstract beforeRequest(
    url: string,
    init: RequestInit
  ): Promise<boolean>;
  protected abstract afterRequest(
    response: Response
  ): Promise<boolean>;
  protected abstract getCredentialsMode(): Nullable<Credentials>;

  constructor(
    protected readonly _baseUrl: string
  ) { }

  protected async get(
    path: string
  ): Promise<OperationResult<string>> {
    return this.doRequest(path, "GET");
  }

  protected async post(
    path: string,
    payload?: string
  ): Promise<OperationResult<string>> {
    return this.doRequest(path, "POST", payload);
  }

  protected async patch(
    path: string,
    payload?: string
  ): Promise<OperationResult<string>> {
    return this.doRequest(path, "PATCH", payload);
  }

  protected async put(
    path: string,
    payload?: string
  ): Promise<OperationResult<string>> {
    return this.doRequest(path, "PUT", payload);
  }

  protected async delete(
    path: string
  ): Promise<OperationResult<string>> {
    return this.doRequest(path, "DELETE", undefined);
  }

  private async doRequest(
    path: string,
    method: "GET" | "POST" | "PATCH" | "PUT" | "DELETE",
    payload?: string
  ): Promise<OperationResult<string>> {
    const headers = this.getHttpHeaders();
    const credentials = this.getCredentialsMode();

    try {
      const url = `${this._baseUrl}${path}`;
      const init = {
        method: method,
        headers: headers,
        body: isNil(payload) ? undefined : payload,
        credentials: isNil(credentials) ? undefined : credentials,
      };

      const beforeCheckResult = await this.beforeRequest(url, init).catch(m => {
        console.warn("Request before check failed!", m);
        return true;
      });

      if (!beforeCheckResult) {
        return OperationResultFactory.warning("", "W_CANCELED_BEFORE");
      }

      const result = await fetch(url, init);

      const afterCheckResult = await this.afterRequest(result).catch(m => {
        console.warn("Request after check failed!", m);
        return true;
      });

      if (!afterCheckResult) {
        return OperationResultFactory.warning("", "W_CANCELED_AFTER");
      }

      const responseText = await result.text();

      if (result.status < 400) {
        return OperationResultFactory.success(responseText);
      }

      return OperationResultFactory.warning(responseText, result.status.toString());
    } catch {
      return OperationResultFactory.error("E_FETCH_FAILED");
    }
  }

}
