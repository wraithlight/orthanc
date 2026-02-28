import { observable, subscribable } from "knockout";

import { LoginAsGuestEvent, LoginAsMemberEvent } from "../../model/login-events";
import { State } from "../../state";

import { LoginClient } from "./login.client";

interface LoginContainerParams { }

export class LoginContainer implements LoginContainerParams {
  public loginAsGuestEvent = new subscribable<LoginAsGuestEvent>();
  public loginAsMemberEvent = new subscribable<LoginAsMemberEvent>();
  public hasLoginError = observable(false);

  private readonly _loginClient = new LoginClient(State.config()!.apiUrl);

  constructor() {
    this.loginAsGuestEvent.subscribe((m) => this.loginAsGuestEventHandler(m));
  }

  private async loginAsGuestEventHandler(m: LoginAsGuestEvent): Promise<void> {
    await this._loginClient.loginGuest(m.gameMode);
    State.events.loginSuccess.notifySubscribers();
  }

}
