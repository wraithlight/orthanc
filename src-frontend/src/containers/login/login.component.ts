import { observable, subscribable } from "knockout";
import { LoginAsGuestEvent, LoginAsMemberEvent } from "../../model/login-events";
import { LoginClient } from "./login.client";
import { State } from "../../state";
import { Environment } from "../../environment";

interface LoginContainerParams { }

export class LoginContainer implements LoginContainerParams {
  public loginAsGuestEvent = new subscribable<LoginAsGuestEvent>();
  public loginAsMemberEvent = new subscribable<LoginAsMemberEvent>();
  public hasLoginError = observable(false);

  private readonly _loginClient = new LoginClient(Environment.apiBaseUrl);

  constructor() {
    this.loginAsGuestEvent.subscribe((_m) => this.loginAsGuestEventHandler());
  }

  private async loginAsGuestEventHandler(): Promise<void> {
    await this._loginClient.loginGuest();
    State.events.loginSuccess.notifySubscribers();
  }

}
