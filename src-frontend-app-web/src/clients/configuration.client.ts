import { ApplicationConfiguration } from "../domain";
import { Environment } from "../environment";
import { newGuid, Nullable } from "../framework";
import { InterceptorCache } from "../http";
import { RuntimeContext } from "../runtime-context";

import {
  API_GET_CONFIGURATION_PATH,
  HeaderNames,
  AcceptValues,
} from '../dal-generated';

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
          [HeaderNames.PLATFORM]: Environment.platform,
          [HeaderNames.DEVICE]: RuntimeContext.device,
          [HeaderNames.REQUESTID]: newGuid(),
          [HeaderNames.ACCEPTSAPPJSON]: AcceptValues.ApplicationJson,
        }
      }
    );

    const content = JSON.parse(await response.text());

    const interceptors = InterceptorCache.getInstance().getAfterInterceptors();
    interceptors.forEach(m => m(response));

    return [
      response.headers.get(HeaderNames.X_ORTHANC_PLATFORM_VERSION),
      content.payload
    ];
  }

}
