import { Observable, observable, observableArray, Subscribable } from "knockout";

import { LoginAsGuestEvent, LoginAsMemberEvent } from "../../model/login-events";

import { DEFAULT_GAME_MODE, GAME_MODE_OPTIONS } from "./login.const";

interface LoginFormComponentParams {
  loginAsGuestEvent: Subscribable;
  loginAsMemberEvent: Subscribable;
  openHallOfFame: Subscribable;
  hasLoginError: Observable<boolean>;
}

export class LoginFormComponent implements LoginFormComponentParams {
  public username = observable<string>();
  public password = observable<string>();
  public hasLoginError: Observable<boolean>;
  public loginAsGuestEvent: Subscribable<LoginAsGuestEvent>;
  public loginAsMemberEvent: Subscribable<LoginAsMemberEvent>;
  public openHallOfFame: Subscribable;
  public readonly gameMode = observable(DEFAULT_GAME_MODE);
  public readonly gameModeOptions = observableArray(GAME_MODE_OPTIONS);

  constructor(params: LoginFormComponentParams) {
    this.loginAsGuestEvent = params.loginAsGuestEvent;
    this.loginAsMemberEvent = params.loginAsMemberEvent;
    this.hasLoginError = params.hasLoginError;
    this.openHallOfFame = params.openHallOfFame;
  }

  public onMemberLogin(): void {
    this.loginAsMemberEvent.notifySubscribers({
      username: this.username() ?? "",
      password: this.password() ?? "",
      gameMode: this.gameMode()
    });
  }

  public onGuestLogin(): void {
    this.loginAsGuestEvent.notifySubscribers({
      gameMode: this.gameMode()
    });
  }

  public onHallOfFameClick(): void {
    this.openHallOfFame.notifySubscribers();
  }

}
