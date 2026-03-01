import { ConfiguartionClient } from "../clients";
import { ApplicationConfiguration } from "../domain";
import { State } from "../state";

export class ConfigurationService {

  private readonly _client = new ConfiguartionClient(State.config()!.apiUrl);

  public async fetchConfiguration(): Promise<ApplicationConfiguration> {
    return this._client.getConfiguration();
  }

}
