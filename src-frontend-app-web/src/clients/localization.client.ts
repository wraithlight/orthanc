import { HeaderNames, HeaderValueAccept } from "../domain";
import { Environment } from "../environment";
import { newGuid } from "../framework";
import { InterceptorCache } from "../http";
import { RuntimeContext } from "../runtime-context";

import { API_GET_LOCALIZATION_LOCALE_PATH } from '../dal-generated';

export class LocalizationClient {

  constructor(
    private readonly _baseUrl: string
  ) { }

  public async getLocalization(locale: string): Promise<Record<string, string>> {
    const response = await fetch(
      `${this._baseUrl}${API_GET_LOCALIZATION_LOCALE_PATH(locale)}`,
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

    return content.payload;
  }

}
