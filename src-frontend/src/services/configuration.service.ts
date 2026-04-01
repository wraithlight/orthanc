import { ConfigurationClient } from "../clients";
import { ApplicationConfiguration } from "../domain";
import { Environment } from "../environment";

export class ConfigurationService {

  private readonly _client = new ConfigurationClient(Environment.apiBaseUrl);

  public async fetchConfiguration(): Promise<ApplicationConfiguration> {
    return this._client.getConfiguration();
  }

}
