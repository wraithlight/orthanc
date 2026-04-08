import { GameMode, HallOfFameListModel, HeaderNames, HeaderValueAccept } from "../domain";
import { Environment } from "../environment";
import { newGuid } from "../framework";
import { InterceptorCache } from "../http";
import { RuntimeContext } from "../runtime-context";

export class HallOfFameClient {

  constructor(
    private readonly _baseUrl: string
  ) { }

  public async getHallOfFame(gameMode: GameMode): Promise<HallOfFameListModel> {
    const baseUrl = `${this._baseUrl}/api/v1/main/hall-of-fame`;
    const queryParams = `?game-mode=${gameMode}`
    const response = await fetch(
      `${baseUrl}${queryParams}`,
      {
        method: "GET",
        headers: {
          [HeaderNames.Platform]: Environment.platform,
          [HeaderNames.Device]: RuntimeContext.device,
          [HeaderNames.RequestId]: newGuid(),
          [HeaderNames.Accept]: HeaderValueAccept.ApplicationJson,
        }
      }
    );

    const content = JSON.parse(await response.text());

    const interceptors = InterceptorCache.getInstance().getAfterInterceptors();
    interceptors.forEach(m => m(response));
    
    return content.payload;
  }

}
