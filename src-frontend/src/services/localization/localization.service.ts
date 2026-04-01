import { LocalizationClient } from "../../clients";
import { Environment } from "../../environment";

export class LocalizationService {

  private readonly _localizationClient = new LocalizationClient(Environment.apiBaseUrl);

  public async getLocalization(locale: string): Promise<Record<string, string>> {
    const result = await this._localizationClient.getLocalization(locale);
    return result;
  }

}
