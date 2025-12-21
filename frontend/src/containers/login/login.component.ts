import { observable, Subscribable, subscribable } from "knockout";
import { LoginAsGuestEvent, LoginAsMemberEvent } from "../../model/login-events";

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

  constructor(params: LoginContainerParams) {
    this.isMemberLoginEnabled = params.isMemberLoginEnabled;
    this.onLoginSuccess = params.onLoginSuccess;
    this.loginAsGuestEvent.subscribe((_m) => this.onLoginSuccess.notifySubscribers());
    this.loginAsMemberEvent.subscribe((m) => console.log("loginAsMemberEvent", m));
   }

}
