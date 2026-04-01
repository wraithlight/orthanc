import { HeaderNames, HeaderValueAccept } from "../domain";
import { Environment } from "../environment";
import { newGuid } from "../framework";

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
          [HeaderNames.RequestId]: newGuid(),
          [HeaderNames.Accept]: HeaderValueAccept.ApplicationJson,
        }
      }
    );

    const content = JSON.parse(await response.text());
    return content.payload;
  }

}
