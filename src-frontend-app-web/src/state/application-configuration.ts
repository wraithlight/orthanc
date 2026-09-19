import { Predicate } from "../framework";

import { GetConfigurationResponsePayload } from "../dal-generated";

export class ApplicationConfigurationState {

  constructor(
    private readonly _config: GetConfigurationResponsePayload
  ) { }

  public getOrDefault<T, U extends T>(
    predicate: Predicate<GetConfigurationResponsePayload, T>, defaultValue: U
  ): T {
    const result = predicate(this._config);
    return result ?? defaultValue;
  }

}
