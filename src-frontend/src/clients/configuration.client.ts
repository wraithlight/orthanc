import { ApplicationConfiguration, HeaderNames } from "../domain";
import { Environment } from "../environment";

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
          [HeaderNames.Platform]: Environment.platform
        }
      }
    );

    const content = JSON.parse(await response.text());
    return content.payload;
  }

}
