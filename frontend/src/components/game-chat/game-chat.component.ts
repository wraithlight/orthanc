import { observable, Observable, ObservableArray, Subscribable } from "knockout";

interface GameChatComponentParams {
  toPoll: Subscribable;
  toSendMessage: Subscribable;
  members: ObservableArray;
  messages: ObservableArray;
  playerName: Observable;
}

export class GameChatComponent implements GameChatComponentParams {
  public toPoll: Subscribable<any>;
  public toSendMessage: Subscribable<any>;
  public members: ObservableArray<any>;
  public messages: ObservableArray<any>;
  public playerName: Observable;

  public message = observable();
  
  constructor(params: GameChatComponentParams) {
    this.toPoll = params.toPoll;
    this.toSendMessage = params.toSendMessage;
    this.members = params.members;
    this.messages = params.messages;
    this.playerName = params.playerName;

    setInterval(() => this.toPoll.notifySubscribers(), 1_000);
  }

  public onSendClick(): void {
    if (!this.message().trim()) return;
    this.toSendMessage.notifySubscribers(this.message());
    this.message(undefined);
  }

}
