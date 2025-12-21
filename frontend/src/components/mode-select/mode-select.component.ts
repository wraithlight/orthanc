import { Observable, observable, Subscribable } from "knockout";

interface ModeSelectComponentParams {
  isKeyboardOnlyEnabled: boolean;
  isKeyboardWithMouseEnabled: boolean;
  onKeyboardOnly: Subscribable;
  onKeyboardWithMouse: Subscribable;
  onBack: Subscribable;
}

export class ModeSelectComponent implements ModeSelectComponentParams {
  public isKeyboardOnlyEnabled: boolean;
  public isKeyboardWithMouseEnabled: boolean;
  public readonly onKeyboardOnly: Subscribable;
  public readonly onKeyboardWithMouse: Subscribable;
  public readonly onBack: Subscribable;

  constructor(params: ModeSelectComponentParams) {
    this.isKeyboardOnlyEnabled = params.isKeyboardOnlyEnabled;
    this.isKeyboardWithMouseEnabled = params.isKeyboardWithMouseEnabled;
    this.onKeyboardOnly = params.onKeyboardOnly;
    this.onKeyboardWithMouse = params.onKeyboardWithMouse;
    this.onBack = params.onBack;
  }

  public onKeyboardOnlyClick(): void {
    this.onKeyboardOnly.notifySubscribers();
  }

  public onKeyboardWithMouseClick(): void {
    this.onKeyboardWithMouse.notifySubscribers();
  }

  public onBackClick(): void {
    this.onBack.notifySubscribers();
  }

}
