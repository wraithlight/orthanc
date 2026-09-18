import { GameMode, HallOfFameListModel } from "../domain";
import { Environment } from "../environment";
import { newGuid } from "../framework";
import { InterceptorCache } from "../http";
import { RuntimeContext } from "../runtime-context";

import {
  API_GET_HALL_OF_FAME_PATH,
  HeaderNames,
  AcceptValues,
} from '../dal-generated';

export class HallOfFameClient {

  constructor(
    private readonly _baseUrl: string
  ) { }

  public async getHallOfFame(gameMode: GameMode): Promise<HallOfFameListModel> {
    const baseUrl = `${this._baseUrl}${API_GET_HALL_OF_FAME_PATH()}`;
    const queryParams = `?game-mode=${gameMode}`
    const response = await fetch(
      `${baseUrl}${queryParams}`,
      {
        method: "GET",
        headers: {
          [HeaderNames.PLATFORM]: Environment.platform,
          [HeaderNames.DEVICE]: RuntimeContext.device,
          [HeaderNames.REQUESTID]: newGuid(),
          [HeaderNames.ACCEPTSAPPJSON]: AcceptValues.ApplicationJson,
        }
      }
    );

    const content = JSON.parse(await response.text());

    const interceptors = InterceptorCache.getInstance().getAfterInterceptors();
    interceptors.forEach(m => m(response));
    
    return content.payload;
  }

}
