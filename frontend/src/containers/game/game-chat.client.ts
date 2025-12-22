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
        })
      }
    );
  }

  public async poll(): Promise<any> {
    const response = await fetch(
      `${this._baseUrl}/api/v1/chat/poll`,
      {
        method: "GET",
        credentials: "include"
      }
    );

    const content = JSON.parse(await response.text());
    return content;
  }

}
