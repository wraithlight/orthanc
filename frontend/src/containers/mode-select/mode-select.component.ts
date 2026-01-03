import { Subscribable, subscribable } from "knockout";

interface ModeSelectContainerParams {
  onBack: Subscribable;
  onModeSelected: Subscribable<"KEYBOARD_ONLY" | "KEYBOARD_WITH_MOUSE">;
}

export class ModeSelectContainer implements ModeSelectContainerParams {
  public onKeyboardOnly = new subscribable();
  public onKeyboardWithMouse = new subscribable();
  public onBack: Subscribable;
  public onModeSelected: Subscribable<"KEYBOARD_ONLY" | "KEYBOARD_WITH_MOUSE">;

  constructor(params: ModeSelectContainerParams) {
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
