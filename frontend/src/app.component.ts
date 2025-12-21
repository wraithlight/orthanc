import { observable, subscribable } from "knockout";
import { AppState } from "./model";
import { getConfig } from "./state";

export class Application {
  public state = observable<AppState>(AppState.LOGIN);
  public onLoginSuccess = new subscribable();
  public onBackFromModeSelect = new subscribable();
  public readonly config = getConfig();

  constructor() {
    this.onLoginSuccess.subscribe(() => this.onLoginSuccessHandler());
    this.onBackFromModeSelect.subscribe(() => this.onBackFromModeSelectHandler());
  }

  public async onLoginSuccessHandler(): Promise<void> {
    setTimeout(() => {
      this.state(AppState.MODE_SELECT);
    }, 500);
  }

  public async onBackFromModeSelectHandler(): Promise<void> {
    setTimeout(() => {
      this.state(AppState.LOGIN);
    }, 500)
  }
}
