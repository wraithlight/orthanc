import { GameMode, HallOfFameListModel } from "../domain";

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
        method: "GET"
      }
    );

    const content = JSON.parse(await response.text());
    return content.payload;
  }

}
