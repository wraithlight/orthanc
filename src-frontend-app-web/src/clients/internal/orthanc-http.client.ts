import { IErrorResponse, ISuccessResponse } from "../../dal";
import { HeaderNames } from "../../domain";
import { Environment } from "../../environment";
import { newGuid } from "../../framework";
import { InterceptorCache } from "../../http";
import { RuntimeContext } from "../../runtime-context";
import { JsonHttpClient } from "./json-http.client";

export abstract class OrthancHttpClient extends JsonHttpClient {

  constructor(
    protected readonly _baseUrl: string
  ) {
    super(_baseUrl);
  }

  protected async beforeRequest(
    _url: string,
    _init: RequestInit
  ): Promise<boolean> {
    return true;
  }

  protected async afterRequest(response: Response): Promise<boolean> {
    const interceptors = InterceptorCache.getInstance().getAfterInterceptors();
    interceptors.forEach(m => m(response));
    return true;
  }

  protected getHttpHeaders(): HeadersInit {
    return {
      ...super.getJsonHeaders(),
      ...this.getOrthancHeaders()
    }
  }

  private getOrthancHeaders(): HeadersInit {
    return {
      [HeaderNames.Device]: RuntimeContext.device,
      [HeaderNames.Platform]: Environment.platform,
      [HeaderNames.RequestId]: newGuid(),
    };
  }

  protected async postOrthanc<U, TS>(
    path: string,
    payload?: U
  ) {
    return super.post<U, ISuccessResponse<TS>, IErrorResponse>(path, payload);
  }

}
