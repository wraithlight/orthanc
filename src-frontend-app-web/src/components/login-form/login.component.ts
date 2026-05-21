import { observable, Subscribable } from "knockout";

import { LoginAsGuestEvent, LoginAsMemberEvent } from "../../model/login-events";
import { RuntimeContext } from "../../runtime-context";


interface LoginFormComponentParams {
  loginAsGuestEvent: Subscribable;
  loginAsMemberEvent: Subscribable;
  openOptionsDialog: Subscribable;
  openHallOfFame: Subscribable;
}

export class LoginFormComponent implements LoginFormComponentParams {
  public username = observable<string>();
  public password = observable<string>();
  public loginAsGuestEvent: Subscribable<LoginAsGuestEvent>;
  public loginAsMemberEvent: Subscribable<LoginAsMemberEvent>;
  public openHallOfFame: Subscribable;
  public openOptionsDialog: Subscribable;

  constructor(params: LoginFormComponentParams) {
    this.loginAsGuestEvent = params.loginAsGuestEvent;
    this.loginAsMemberEvent = params.loginAsMemberEvent;
    this.openHallOfFame = params.openHallOfFame;
    this.openOptionsDialog = params.openOptionsDialog;
  }

  public onMemberLogin(): void {
    this.loginAsMemberEvent.notifySubscribers({
      username: this.username() ?? "",
      password: this.password() ?? "",
      // TODO: Move these to the consumer side.
      gameMode: RuntimeContext.gameMode
    });
  }

  public onOptionsClick(): void {
    this.openOptionsDialog.notifySubscribers();
  }

  public onGuestLogin(): void {
    this.loginAsGuestEvent.notifySubscribers({
      // TODO: Move these to the consumer side.
      gameMode: RuntimeContext.gameMode
    });
  }

  public onHallOfFameClick(): void {
    this.openHallOfFame.notifySubscribers();
  }

}
