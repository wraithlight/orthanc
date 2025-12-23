export class GameActionClient {
  constructor(
    private readonly _baseUrl: string
  ) {
  }

  public async onAction(
    action: string
  ): Promise<any> {
    const result = await fetch(
      `${this._baseUrl}/api/v1/game/action`,
      {
        method: "POST",
        credentials: "include",
        body: JSON.stringify({
          action: action
        })
      }
    );

    const content = await result.text();
    return JSON.parse(content);
  }
}
