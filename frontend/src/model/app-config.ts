interface AppAssetsConfig {
  readonly characterImageUrl: string;
}

export interface AppConfig {
  readonly apiUrl: string;
  readonly isMemberLoginEnabled: boolean;
  readonly isKeyboardOnlyEnabled: boolean;
  readonly isKeyboardWithMouseEnabled: boolean;
  readonly assets: AppAssetsConfig;
  readonly version: string;
  readonly environment: string;
}
