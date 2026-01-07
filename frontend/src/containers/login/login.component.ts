import { observable, subscribable } from "knockout";
import { LoginAsGuestEvent, LoginAsMemberEvent } from "../../model/login-events";
import { LoginClient } from "./login.client";
import { getConfig } from "../../state";

interface LoginContainerParams { }

export class LoginContainer implements LoginContainerParams {
  public loginAsGuestEvent = new subscribable<LoginAsGuestEvent>();
  public loginAsMemberEvent = new subscribable<LoginAsMemberEvent>();
  public hasLoginError = observable(false);

  private readonly _loginClient = new LoginClient(getConfig().apiUrl);

  constructor(params: LoginContainerParams) {
    this.loginAsGuestEvent.subscribe((_m) => this.loginAsGuestEventHandler());
  }

  private async loginAsGuestEventHandler(): Promise<void> {
    const result = await this._loginClient.loginGuest();
    window.dispatchEvent(new CustomEvent("LOGIN_SUCCESS", { detail: { playerName: result.username } }));
  }

}
