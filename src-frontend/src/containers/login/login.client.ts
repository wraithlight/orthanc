import { GameMode } from "../../domain";

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
        })
      }
    );
    const content = JSON.parse(await response.text());
    return content.payload;
  }

}
