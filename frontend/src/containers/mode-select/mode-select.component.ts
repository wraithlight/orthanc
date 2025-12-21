import { Subscribable, subscribable } from "knockout";

interface ModeSelectContainerParams {
  isKeyboardOnlyEnabled: boolean;
  isKeyboardWithMouseEnabled: boolean;
  onBack: Subscribable;
  onModeSelected: Subscribable<"KEYBOARD_ONLY" | "KEYBOARD_WITH_MOUSE">;
}

export class ModeSelectContainer implements ModeSelectContainerParams {
  public isKeyboardOnlyEnabled: boolean;
  public isKeyboardWithMouseEnabled: boolean;
  public onKeyboardOnly = new subscribable();
  public onKeyboardWithMouse = new subscribable();
  public onBack: Subscribable;
  public onModeSelected: Subscribable<"KEYBOARD_ONLY" | "KEYBOARD_WITH_MOUSE">;

  constructor(params: ModeSelectContainerParams) {
    this.isKeyboardOnlyEnabled = params.isKeyboardOnlyEnabled;
    this.isKeyboardWithMouseEnabled = params.isKeyboardWithMouseEnabled;
    this.onBack = params.onBack;
    this.onModeSelected = params.onModeSelected;

    this.onKeyboardOnly.subscribe(() => this.onModeSelectedHandler("KEYBOARD_ONLY"));
    this.onKeyboardWithMouse.subscribe(() => this.onModeSelectedHandler("KEYBOARD_WITH_MOUSE"));
  }

  private async onModeSelectedHandler(
    mode: "KEYBOARD_ONLY" | "KEYBOARD_WITH_MOUSE"
  ): Promise<void> {
    this.onModeSelected.notifySubscribers(mode);
  }

}
