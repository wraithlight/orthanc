import { AppConfig } from "../model";

let _config: AppConfig;

export const setConfig = (config: AppConfig): void => { _config = config };
export const getConfig = (): AppConfig => _config;
