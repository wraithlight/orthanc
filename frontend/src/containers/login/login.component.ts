import { observable, Subscribable, subscribable } from "knockout";
import { LoginAsGuestEvent, LoginAsMemberEvent } from "../../model/login-events";
import { LoginClient } from "./login.client";
import { getConfig } from "../../state";

interface LoginContainerParams {
  isMemberLoginEnabled: boolean;
  onLoginSuccess: Subscribable
}

export class LoginContainer implements LoginContainerParams {
  public loginAsGuestEvent = new subscribable<LoginAsGuestEvent>();
  public loginAsMemberEvent = new subscribable<LoginAsMemberEvent>();
  public hasLoginError = observable(false);
  public isMemberLoginEnabled: boolean;
  public onLoginSuccess: Subscribable;

  private readonly _loginClient = new LoginClient(getConfig().apiUrl);

  constructor(params: LoginContainerParams) {
    this.isMemberLoginEnabled = params.isMemberLoginEnabled;
    this.onLoginSuccess = params.onLoginSuccess;
    this.loginAsGuestEvent.subscribe((_m) => this.loginAsGuestEventHandler());
  }

  private async loginAsGuestEventHandler(): Promise<void> {
    await this._loginClient.loginGuest();
    this.onLoginSuccess.notifySubscribers()
  }

}
