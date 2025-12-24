import { observable, Observable, ObservableArray, Subscribable } from "knockout";

import { KeyboardEventService } from "../../services";

interface GameChatComponentParams {
  toPoll: Subscribable;
  toSendMessage: Subscribable;
  members: ObservableArray;
  messages: ObservableArray;
  playerName: Observable;
}

export class GameChatComponent implements GameChatComponentParams {
  public playerName: Observable;
  public toPoll: Subscribable<any>;
  public members: ObservableArray<any>;
  public messages: ObservableArray<any>;
  public toSendMessage: Subscribable<any>;
  public inputHasFocus = observable(false);
  private readonly _keyboardEventService = KeyboardEventService.getInstance();

  public message = observable();

  constructor(params: GameChatComponentParams) {
    this.toPoll = params.toPoll;
    this.toSendMessage = params.toSendMessage;
    this.members = params.members;
    this.messages = params.messages;
    this.playerName = params.playerName;

    this.inputHasFocus.subscribe((hasFocus) => {
      hasFocus
        ? this._keyboardEventService.disableEmit()
        : this._keyboardEventService.enableEmit()
        ;
    });

    setInterval(() => this.toPoll.notifySubscribers(), 1_000);
  }

  public onSendClick(): void {
    if (!this.message().trim()) return;
    this.toSendMessage.notifySubscribers(this.message());
    this.message(undefined);
  }

}
