import { observable, subscribable } from "knockout";

import { LoginClient } from "../../clients";
import { LoginAsGuestEvent, LoginAsMemberEvent } from "../../model/login-events";
import { State } from "../../state";
import { Environment } from "../../environment";


interface LoginContainerParams { }

export class LoginContainer implements LoginContainerParams {
  public loginAsGuestEvent = new subscribable<LoginAsGuestEvent>();
  public loginAsMemberEvent = new subscribable<LoginAsMemberEvent>();
  public openHallOfFameEvent = new subscribable();
  public hasLoginError = observable(false);

  private readonly _loginClient = new LoginClient(Environment.apiBaseUrl);

  constructor() {
    this.loginAsGuestEvent.subscribe((m) => this.loginAsGuestEventHandler(m));
    this.openHallOfFameEvent.subscribe(() => this.openHallOfFame());
  }

  private async loginAsGuestEventHandler(m: LoginAsGuestEvent): Promise<void> {
    await this._loginClient.loginGuest(m.gameMode);
    State.events.loginSuccess.notifySubscribers();
  }

  private openHallOfFame(): void {
    State.events.openHallOfFame.notifySubscribers();
  }

}
