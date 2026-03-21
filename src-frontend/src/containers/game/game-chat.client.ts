import { HeaderNames, HeaderValueAccept } from "../../domain";
import { Environment } from "../../environment";
import { newGuid } from "../../framework";

export class GameChatClient {
  constructor(
    private readonly _baseUrl: string
  ) {
  }

  public async sendMessage(message: string): Promise<void> {
    await fetch(
      `${this._baseUrl}/api/v1/chat/send`,
      {
        method: "POST",
        credentials: "include",
        body: JSON.stringify({
          message: message
        }),
        headers: {
          [HeaderNames.Platform]: Environment.platform,
          [HeaderNames.RequestId]: newGuid(),
          [HeaderNames.Accept]: HeaderValueAccept.ApplicationJson,
        }
      }
    );
  }

  public async poll(): Promise<any> {
    const response = await fetch(
      `${this._baseUrl}/api/v1/chat/poll`,
      {
        method: "GET",
        credentials: "include",
        headers: {
          [HeaderNames.Platform]: Environment.platform,
          [HeaderNames.RequestId]: newGuid(),
          [HeaderNames.Accept]: HeaderValueAccept.ApplicationJson,
        }
      }
    );

    const content = JSON.parse(await response.text());
    return content.payload;
  }

}
