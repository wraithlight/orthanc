import { Environment } from "../environment";
import { newGuid } from "../framework";
import { InterceptorCache } from "../http";
import { RuntimeContext } from "../runtime-context";

import {
  API_POST_SEND_CHAT_PATH,
  API_GET_POLL_CHAT_PATH,
  HeaderNames,
  AcceptValues,
  GetPollChatResponsePayload,
  PostSendChatResponsePayload,
} from '../dal-generated';

export class GameChatClient {
  constructor(
    private readonly _baseUrl: string
  ) {
  }

  public async sendMessage(message: string): Promise<PostSendChatResponsePayload> {
    const result = await fetch(
      `${this._baseUrl}${API_POST_SEND_CHAT_PATH()}`,
      {
        method: "POST",
        credentials: "include",
        body: JSON.stringify({
          message: message
        }),
        headers: {
          [HeaderNames.PLATFORM]: Environment.platform,
          [HeaderNames.DEVICE]: RuntimeContext.device,
          [HeaderNames.REQUESTID]: newGuid(),
          [HeaderNames.ACCEPTSAPPJSON]: AcceptValues.ApplicationJson,
        }
      }
    );

    const interceptors = InterceptorCache.getInstance().getAfterInterceptors();
    interceptors.forEach(m => m(result));

    const content = JSON.parse(await result.text());
    return content.payload;
  }

  public async poll(): Promise<GetPollChatResponsePayload> {
    const response = await fetch(
      `${this._baseUrl}${API_GET_POLL_CHAT_PATH()}`,
      {
        method: "GET",
        credentials: "include",
        headers: {
          [HeaderNames.PLATFORM]: Environment.platform,
          [HeaderNames.DEVICE]: RuntimeContext.device,
          [HeaderNames.REQUESTID]: newGuid(),
          [HeaderNames.ACCEPTSAPPJSON]: AcceptValues.ApplicationJson,
        }
      }
    );

    const content = JSON.parse(await response.text());

    const interceptors = InterceptorCache.getInstance().getAfterInterceptors();
    interceptors.forEach(m => m(response));

    return content.payload;
  }

}
