import { Subscribable } from "knockout";

interface ModeSelectComponentParams {
  onKeyboardOnly: Subscribable;
  onKeyboardWithMouse: Subscribable;
  onBack: Subscribable;
}

export class ModeSelectComponent implements ModeSelectComponentParams {
  public readonly onKeyboardOnly: Subscribable;
  public readonly onKeyboardWithMouse: Subscribable;
  public readonly onBack: Subscribable;

  constructor(params: ModeSelectComponentParams) {
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
