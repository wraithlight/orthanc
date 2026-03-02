import { ConfiguartionClient } from "../clients";
import { ApplicationConfiguration } from "../domain";
import { Environment } from "../environment";

export class ConfigurationService {

  private readonly _client = new ConfiguartionClient(Environment.apiBaseUrl);

  public async fetchConfiguration(): Promise<ApplicationConfiguration> {
    return this._client.getConfiguration();
  }

}
