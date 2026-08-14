import { HeaderNames, HeaderValueAccept } from "../domain";
import { Environment } from "../environment";
import { newGuid } from "../framework";
import { InterceptorCache } from "../http";
import { RuntimeContext } from "../runtime-context";

import { API_POST_GAME_ACTION_PATH } from '../dal-generated';

export class GameActionClient {
  constructor(
    private readonly _baseUrl: string
  ) {
  }

  public async onAction(
    action: string,
    payload: string | null
  ): Promise<any> {
    const result = await fetch(
      `${this._baseUrl}${API_POST_GAME_ACTION_PATH()}`,
      {
        method: "POST",
        credentials: "include",
        body: JSON.stringify({
          action: action,
          payload: payload
        }),
        headers: {
          [HeaderNames.Platform]: Environment.platform,
          [HeaderNames.Device]: RuntimeContext.device,
          [HeaderNames.RequestId]: newGuid(),
          [HeaderNames.Accept]: HeaderValueAccept.ApplicationJson,
        }
      }
    );

    const content = await result.text();

    const interceptors = InterceptorCache.getInstance().getAfterInterceptors();
    interceptors.forEach(m => m(result));

    return JSON.parse(content).payload;
  }
}
