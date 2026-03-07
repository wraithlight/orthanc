import { ApplicationConfiguration } from "../domain";

export class ConfiugartionClient {

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
