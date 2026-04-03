import { GameMode, HeaderNames, HeaderValueAccept } from "../../domain";
import { Environment } from "../../environment";
import { newGuid } from "../../framework";
import { InterceptorCache } from "../../http";

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
