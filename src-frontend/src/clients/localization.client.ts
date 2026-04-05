import { HeaderNames, HeaderValueAccept } from "../domain";
import { Environment } from "../environment";
import { newGuid } from "../framework";
import { InterceptorCache } from "../http";
import { RuntimeContext } from "../runtime-context";

export class LocalizationClient {

  constructor(
    private readonly _baseUrl: string
  ) { }

  public async getLocalization(locale: string): Promise<Record<string, string>> {
    const response = await fetch(
      `${this._baseUrl}/api/v1/localization/${locale}`,
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
