import { ApplicationConfiguration } from "../domain";
import { Predicate } from "../framework";

export class ApplicationConfigurationState {
  
  constructor(
    private readonly _config: ApplicationConfiguration
  ) { }

  public getOrDefault<T, U extends T>(predicate: Predicate<ApplicationConfiguration, T>, defaultValue: U): T {
    const result = predicate(this._config);
    return result ?? defaultValue;
  }

}
