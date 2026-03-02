import { ApplicationConfiguration } from "../domain";

export class ConfiguartionClient {

  constructor(
    private readonly _baseUrl: string
  ) { }

  public async getConfiguration(): Promise<ApplicationConfiguration> {
    const response = await fetch(
      `${this._baseUrl}/api/v1/configuration`,
      {
        method: "GET"
      }
    );

    const content = JSON.parse(await response.text());
    return content.payload;
  }

}
