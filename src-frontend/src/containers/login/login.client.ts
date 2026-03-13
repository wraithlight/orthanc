import { GameMode, HeaderNames } from "../../domain";
import { Environment } from "../../environment";
import { newGuid } from "../../framework";

export class LoginClient {

  constructor(
    private readonly _baseUrl: string
  ) { }

  public async loginGuest(
    gameMode: GameMode
  ): Promise<{ username: string }> {
    const response = await fetch(
      `${this._baseUrl}/api/v1/login/guest`,
      {
        method: "POST",
        credentials: "include",
        body: JSON.stringify({
          gameMode: gameMode
        }),
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
