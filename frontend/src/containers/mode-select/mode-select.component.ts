import { observable, Subscribable, subscribable } from "knockout";
import { LoginAsGuestEvent, LoginAsMemberEvent } from "../../model/login-events";

interface ModeSelectContainerParams {
  isKeyboardOnlyEnabled: boolean;
  isKeyboardWithMouseEnabled: boolean;
  onBack: Subscribable;
}

export class ModeSelectContainer implements ModeSelectContainerParams {
  public isKeyboardOnlyEnabled: boolean;
  public isKeyboardWithMouseEnabled: boolean;
  public onKeyboardOnly = new subscribable();
  public onKeyboardWithMouse = new subscribable();
  public onBack: Subscribable;

  constructor(params: ModeSelectContainerParams) {
    this.isKeyboardOnlyEnabled = params.isKeyboardOnlyEnabled;
    this.isKeyboardWithMouseEnabled = params.isKeyboardWithMouseEnabled;
    this.onBack = params.onBack;
   }

}
