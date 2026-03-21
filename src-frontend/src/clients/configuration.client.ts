import { ApplicationConfiguration, HeaderNames, HeaderValueAccept } from "../domain";
import { Environment } from "../environment";
import { newGuid } from "../framework";

export class ConfiugartionClient {

  constructor(
    private readonly _baseUrl: string
  ) { }

  public async getConfiguration(): Promise<ApplicationConfiguration> {
    const response = await fetch(
      `${this._baseUrl}/api/v1/configuration`,
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
