import { LocalizationClient } from "../../clients";
import { GetLocalizationLocaleResponsePayload } from "../../dal-generated";
import { Environment } from "../../environment";

export class LocalizationService {

  private readonly _localizationClient = new LocalizationClient(Environment.apiBaseUrl);

  public async getLocalization(locale: string): Promise<GetLocalizationLocaleResponsePayload> {
    const result = await this._localizationClient.getLocalization(locale);
    return result;
  }

}
