import { observable, observableArray, Subscribable, unwrap } from "knockout";

import { RuntimeContext } from "../../runtime-context";

import { GAME_MODE_OPTIONS } from "./dialog-options-game-mode.const";

interface OrthancOptionsDialogLanguageComponentProps {
  saveSubscription: Subscribable;
}

export class OrthancOptionsDialogLanguageComponent implements OrthancOptionsDialogLanguageComponentProps {
  public readonly gameMode = observable(RuntimeContext.gameMode);
  public readonly gameModeOptions = observableArray(GAME_MODE_OPTIONS);
  public readonly saveSubscription: Subscribable;

  constructor(params: OrthancOptionsDialogLanguageComponentProps) {
    this.saveSubscription = params.saveSubscription;
    this.saveSubscription.subscribe(() => this.onSave());
  }

  private onSave(): void {
    RuntimeContext.gameMode = unwrap(this.gameMode);
  }

}
