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
      `${this._baseUrl}/api/v1/game/action`,
      {
        method: "POST",
        credentials: "include",
        body: JSON.stringify({
          action: action,
          payload: payload
        })
      }
    );

    const content = await result.text();
    return JSON.parse(content).payload;
  }
}
