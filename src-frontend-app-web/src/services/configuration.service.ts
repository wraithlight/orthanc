import { ConfigurationClient } from "../clients";
import { Environment } from "../environment";
import { Nullable } from "../framework";

import { GetConfigurationResponsePayload } from "../dal-generated";

export class ConfigurationService {

  private readonly _client = new ConfigurationClient(Environment.apiBaseUrl);

  public async fetchConfiguration(
  ): Promise<[Nullable<string>, GetConfigurationResponsePayload]> {
    return this._client.getConfiguration();
  }

}
