export class LoginClient {
  
  constructor(
    private readonly _baseUrl: string
  ) {}

  public async loginGuest(): Promise<{ username: string }> {
    const response = await fetch(
      `${this._baseUrl}/api/v1/login/guest`,
      {
        method: "POST",
        credentials: "include"
      }
    );
    const content = JSON.parse(await response.text());
    return content;
  }

}
