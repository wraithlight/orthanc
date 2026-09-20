import { HallOfFameClient } from "../clients";
import { GetHallOfFameResponsePayload } from "../dal-generated";
import { GameMode } from "../domain";
import { Environment } from "../environment";

export class HallOfFameService {

  private readonly _client = new HallOfFameClient(Environment.apiBaseUrl);

  public async fetchHallOfFame(gameMode: GameMode): Promise<GetHallOfFameResponsePayload> {
    return this._client.getHallOfFame(gameMode);
  }

}
