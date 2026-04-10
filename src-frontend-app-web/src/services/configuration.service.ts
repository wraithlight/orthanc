import { ConfigurationClient } from "../clients";
import { ApplicationConfiguration } from "../domain";
import { Environment } from "../environment";
import { Nullable } from "../framework";

export class ConfigurationService {

  private readonly _client = new ConfigurationClient(Environment.apiBaseUrl);

  public async fetchConfiguration(): Promise<[Nullable<string>, ApplicationConfiguration]> {
    return this._client.getConfiguration();
  }

}
