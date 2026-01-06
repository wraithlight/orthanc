import { observable, subscribable } from "knockout";
import { AppState } from "./model";
import { getConfig } from "./state";

export class Application {
  public mode = observable<"KEYBOARD_ONLY" | "KEYBOARD_WITH_MOUSE">();
  public state = observable<AppState>(AppState.LOGIN);
  public onLoginSuccess = new subscribable<{ playerName: string }>();
  public onModeSelected = new subscribable<"KEYBOARD_ONLY" | "KEYBOARD_WITH_MOUSE">();
  public onBackFromModeSelect = new subscribable();
  public onBackFromCharacterCreation = new subscribable();
  public onNextFromCharacterCreation = new subscribable();
  public readonly config = getConfig();

  public readonly playerName = observable("");

  constructor() {
    this.onLoginSuccess.subscribe((m) => this.onLoginSuccessHandler(m.playerName));
    this.onModeSelected.subscribe((mode) => this.modeSelected(mode));
    this.onBackFromModeSelect.subscribe(() => this.onBackFromModeSelectHandler());
    this.onBackFromCharacterCreation.subscribe(() => this.onBackFromCharacterCreationHandler());
    this.onNextFromCharacterCreation.subscribe(() => this.onNextFromCharacterCreationHandler());
  }

  public async onLoginSuccessHandler(playerName: string): Promise<void> {
    setTimeout(() => {
      this.playerName(playerName);
      this.state(AppState.CHARACTER_CREATION);
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
      this.playerName("");
      this.state(AppState.LOGIN);
    }, 500);
  }

  public async onNextFromCharacterCreationHandler(): Promise<void> {
    await fetch(
      `${getConfig().apiUrl}/api/v1/game/start`,
      {
        method: "POST",
        credentials: "include"
      }
    );
    this.state(AppState.INGAME);
  }
}
