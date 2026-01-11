import { observable, observableArray, Subscribable, subscribable } from "knockout";

import { GameChatClient } from "../game/game-chat.client";
import { State } from "../../state";

import { CharacterCreationClient } from "./character-creation.client";

interface CharacterCreationContainerParams { }

export class CharacterCreationContainer implements CharacterCreationContainerParams {
  public onGenerate: Subscribable;
  public readonly stats = observable<{ int: number; dex: number; str: number; con: number; maxHits: number; }>({
    int: 0,
    dex: 0,
    str: 0,
    con: 0,
    maxHits: 0
  });

  public readonly onBack = new subscribable();
  public readonly onNext = new subscribable();
  public readonly onChatPoll = new subscribable();
  public readonly onSendChatMessage = new subscribable<string>();

  public readonly playerName = State.name;
  public readonly chatMembers = observableArray([]);
  public readonly chatMessages = observableArray([]);

  private readonly _gameChatClient = new GameChatClient(State.config()!.apiUrl);
  private readonly _characterCreationClient = new CharacterCreationClient(State.config()!.apiUrl);

  constructor() {
    this.onGenerate = new subscribable();
    this.onGenerate.subscribe(() => this.onGenerateHandler());
    this.stats
    this._characterCreationClient.generateStats().then(m => {
      this.stats({
        int: m.stats.int,
        dex: m.stats.dex,
        str: m.stats.str,
        con: m.stats.con,
        maxHits: m.maxHits
      });
    });
    this.onChatPoll.subscribe(() => this.pollChat());
    this.onSendChatMessage.subscribe(m => this.sendChatMessage(m));

    this.onBack.subscribe(() => State.events.backFromCharacterCreation());
    this.onNext.subscribe(() => State.events.nextFromCharacterCreation());
  }

  public async onGenerateHandler(): Promise<void> {
    await this._characterCreationClient.generateStats().then(m => {
      this.stats({
        int: m.stats.int,
        dex: m.stats.dex,
        str: m.stats.str,
        con: m.stats.con,
        maxHits: m.maxHits
      });
    });
  }

  private async pollChat(): Promise<void> {
    const result = await this._gameChatClient.poll();
    this.chatMembers(result.members);
    this.chatMessages(result.messages);
  }

  private async sendChatMessage(
    message: string
  ): Promise<void> {
    await this._gameChatClient.sendMessage(message);
  }

}
