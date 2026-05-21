export interface IEnvironment {
  environmentName: "local-development" | "local" | "canary" | "development" | "production";
  apiBaseUrl: string;
  platform: "web" | "mobile" | "application";
}
