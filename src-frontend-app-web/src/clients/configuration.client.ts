import { ApplicationConfiguration, HeaderNames, HeaderValueAccept } from "../domain";
import { Environment } from "../environment";
import { newGuid, Nullable } from "../framework";
import { InterceptorCache } from "../http";
import { RuntimeContext } from "../runtime-context";

import { API_GET_CONFIGURATION_PATH } from '../dal-generated';

export class ConfigurationClient {

  constructor(
    private readonly _baseUrl: string
  ) { }

  public async getConfiguration(): Promise<[Nullable<string>, ApplicationConfiguration]> {
    const response = await fetch(
      `${this._baseUrl}${API_GET_CONFIGURATION_PATH()}`,
      {
        method: "GET",
        headers: {
          [HeaderNames.Platform]: Environment.platform,
          [HeaderNames.Device]: RuntimeContext.device,
          [HeaderNames.RequestId]: newGuid(),
          [HeaderNames.Accept]: HeaderValueAccept.ApplicationJson,
        }
      }
    );

    const content = JSON.parse(await response.text());

    const interceptors = InterceptorCache.getInstance().getAfterInterceptors();
    interceptors.forEach(m => m(response));

    return [
      response.headers.get(HeaderNames.PlatformVersion),
      content.payload
    ];
  }

}
