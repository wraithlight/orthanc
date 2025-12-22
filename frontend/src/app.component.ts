import { observable, subscribable } from "knockout";
import { AppState } from "./model";
import { getConfig } from "./state";

export class Application {
  public mode = observable<"KEYBOARD_ONLY" | "KEYBOARD_WITH_MOUSE">();
  public state = observable<AppState>(AppState.INGAME);
  public onLoginSuccess = new subscribable();
  public onModeSelected = new subscribable<"KEYBOARD_ONLY" | "KEYBOARD_WITH_MOUSE">();
  public onBackFromModeSelect = new subscribable();
  public onBackFromCharacterCreation = new subscribable();
  public onNextFromCharacterCreation = new subscribable();
  public readonly config = getConfig();

  constructor() {
    this.onLoginSuccess.subscribe(() => this.onLoginSuccessHandler());
    this.onModeSelected.subscribe((mode) => this.modeSelected(mode));
    this.onBackFromModeSelect.subscribe(() => this.onBackFromModeSelectHandler());
    this.onBackFromCharacterCreation.subscribe(() => this.onBackFromCharacterCreationHandler());
    this.onNextFromCharacterCreation.subscribe(() => this.onNextFromCharacterCreationHandler());
  }

  public async onLoginSuccessHandler(): Promise<void> {
    setTimeout(() => {
      this.state(AppState.MODE_SELECT);
    }, 500);
  }

  public async modeSelected(mode: "KEYBOARD_ONLY" | "KEYBOARD_WITH_MOUSE"): Promise<void> {
    this.mode(mode);
    setTimeout(() => {
      this.state(AppState.CHARACTER_CREATION);
    }, 500);
  }

  public async onBackFromModeSelectHandler(): Promise<void> {
    setTimeout(() => {
      this.state(AppState.LOGIN);
    }, 500);
  }

  public async onBackFromCharacterCreationHandler(): Promise<void> {
    setTimeout(() => {
      this.state(AppState.MODE_SELECT);
    }, 500);
  }

  public async onNextFromCharacterCreationHandler(): Promise<void> {
    setTimeout(() => {
      this.state(AppState.INGAME);
    }, 500);
  }
}
