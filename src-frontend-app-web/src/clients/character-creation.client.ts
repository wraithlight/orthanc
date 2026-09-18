import { Environment } from "../environment";
import { newGuid } from "../framework";
import { InterceptorCache } from "../http";
import { RuntimeContext } from "../runtime-context";

import {
  API_POST_GENERATE_CHARACTER_PATH,
  PostGenerateCharacterResponsePayload,
  HeaderNames,
  AcceptValues,
} from "../dal-generated";

export class CharacterCreationClient {

  constructor(
    private readonly _baseUrl: string
  ) { }

  public async generateStats(): Promise<PostGenerateCharacterResponsePayload> {
    const response = await fetch(
      `${this._baseUrl}${API_POST_GENERATE_CHARACTER_PATH()}`,
      {
        method: "POST",
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
