import { HallOfFameClient } from "../clients";
import { GameMode, HallOfFameListModel } from "../domain";
import { Environment } from "../environment";

export class HallOfFameService {

  private readonly _client = new HallOfFameClient(Environment.apiBaseUrl);

  public async fetchHallOfFame(gameMode: GameMode): Promise<HallOfFameListModel> {
    return this._client.getHallOfFame(gameMode);
  }

}
