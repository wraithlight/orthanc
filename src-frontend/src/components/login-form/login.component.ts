import { Observable, observable, Subscribable } from "knockout";
import { LoginAsGuestEvent, LoginAsMemberEvent } from "../../model/login-events";

interface LoginFormComponentParams {
  loginAsGuestEvent: Subscribable;
  loginAsMemberEvent: Subscribable;
  hasLoginError: Observable<boolean>;
}

export class LoginFormComponent implements LoginFormComponentParams {
  public username = observable<string>();
  public password = observable<string>();
  public hasLoginError: Observable<boolean>;
  public loginAsGuestEvent: Subscribable<LoginAsGuestEvent>;
  public loginAsMemberEvent: Subscribable<LoginAsMemberEvent>;

  constructor(params: LoginFormComponentParams) {
    this.loginAsGuestEvent = params.loginAsGuestEvent;
    this.loginAsMemberEvent = params.loginAsMemberEvent;
    this.hasLoginError = params.hasLoginError;
  }

  public onMemberLogin(): void {
    this.loginAsMemberEvent.notifySubscribers({
      username: this.username() ?? "",
      password: this.password() ?? ""
    });
  }

  public onGuestLogin(): void {
    this.loginAsGuestEvent.notifySubscribers();
  }

}
