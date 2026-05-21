import { subscribable } from "knockout";

import { LoginClient } from "../../clients";
import { LoginAsGuestEvent, LoginAsMemberEvent } from "../../model/login-events";
import { State } from "../../state";
import { Environment } from "../../environment";


interface LoginContainerParams { }

export class LoginContainer implements LoginContainerParams {
  public loginAsGuestEvent = new subscribable<LoginAsGuestEvent>();
  public loginAsMemberEvent = new subscribable<LoginAsMemberEvent>();
  public openOptionsDialogEvent = new subscribable();
  public openHallOfFameEvent = new subscribable();

  private readonly _loginClient = new LoginClient(Environment.apiBaseUrl);

  constructor() {
    this.loginAsGuestEvent.subscribe((m) => this.loginAsGuestEventHandler(m));
    this.openHallOfFameEvent.subscribe(() => this.openHallOfFame());
    this.openOptionsDialogEvent.subscribe(() => this.openOptionsDialog());
  }

  private async loginAsGuestEventHandler(m: LoginAsGuestEvent): Promise<void> {
    await this._loginClient.loginGuest(m.gameMode);
    State.events.loginSuccess.notifySubscribers();
  }

  private openHallOfFame(): void {
    State.events.openHallOfFame.notifySubscribers();
  }

  private openOptionsDialog(): void {
    State.events.openOptionsDialog.notifySubscribers();
  }

}
