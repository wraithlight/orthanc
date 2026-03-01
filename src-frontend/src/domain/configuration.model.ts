import { GameMode } from "./game-mode.enum";

interface ConfigurationItem {
  type: "VALUE" | "FLAG";
}

interface ConfigurationValueItem<T> extends ConfigurationItem {
  type: "VALUE";
  value: T;
}

interface ConfigurationFlagItem extends ConfigurationItem {
  type: "FLAG";
  value: "ENABLED" | "DISABLED";
}

export interface ApplicationConfiguration {
  availableLocales: ReadonlyArray<string>;
  featureStates: {
    applicationDefaultLanguageDefault: Omit<ConfigurationValueItem<"en">, "type">;
    loginScreenLanguageSwitchEnabled: Omit<ConfigurationFlagItem, "type">;
    loginScreenLanguageSwitchDefault: Omit<ConfigurationValueItem<"en">, "type">;
    loginScreenModeScreenEnabled: Omit<ConfigurationFlagItem, "type">;
    loginScreenModeScreenDefault: Omit<ConfigurationValueItem<GameMode>, "type">;
  }
}
