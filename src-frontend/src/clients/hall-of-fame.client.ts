import { GameMode, HallOfFameListModel, HeaderNames } from "../domain";
import { Environment } from "../environment";
import { newGuid } from "../framework";

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
          [HeaderNames.RequestId]: newGuid(),
        }
      }
    );

    const content = JSON.parse(await response.text());
    return content.payload;
  }

}
