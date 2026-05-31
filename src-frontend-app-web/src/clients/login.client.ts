import { GameMode } from "../domain";
import { Nullable } from "../framework";
import { Credentials, OrthancHttpClient } from "./internal";

export class LoginClient extends OrthancHttpClient {

  protected getCredentialsMode(): Nullable<Credentials> {
    return "include";
  }

  public async loginGuest(
    gameMode: GameMode
  ) {
    const path = "/api/v1/login/guest";
    const result = await super.postOrthanc<
      { gameMode: GameMode },
      { username: string }
    >(path, { gameMode: gameMode });
    return result;
  }

}
