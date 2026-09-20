import { Environment } from "../environment";
import { newGuid } from "../framework";
import { InterceptorCache } from "../http";
import { RuntimeContext } from "../runtime-context";

import {
  API_POST_LOGIN_GUEST_PATH,
  HeaderNames,
  AcceptValues,
  PostLoginGuestRequest,
  PostLoginGuestResponsePayload,
} from '../dal-generated';

export class LoginClient {

  constructor(
    private readonly _baseUrl: string
  ) { }

  public async loginGuest(
    requestBody: PostLoginGuestRequest
  ): Promise<PostLoginGuestResponsePayload> {
    const response = await fetch(
      `${this._baseUrl}${API_POST_LOGIN_GUEST_PATH()}`,
      {
        method: "POST",
        credentials: "include",
        body: JSON.stringify(requestBody),
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
