import { observable, observableArray, subscribable } from "knockout";

import { getConfig } from "../../state";

import { GameChatClient } from "./game-chat.client";
import { GameActionClient } from "./game-action.client";

export class GameContainer {
  public readonly onChatPoll = new subscribable();
  public readonly onSendChatMessage = new subscribable<string>();

  public readonly chatMembers = observableArray([]);
  public readonly chatMessages = observableArray([]);

  public readonly tile00 = observable({ top: 'TILE_OPEN', right: 'TILE_OPEN', bottom: 'TILE_OPEN', left: 'TILE_OPEN' });
  public readonly tile01 = observable({ top: 'TILE_OPEN', right: 'TILE_OPEN', bottom: 'TILE_OPEN', left: 'TILE_OPEN' });
  public readonly tile02 = observable({ top: 'TILE_OPEN', right: 'TILE_OPEN', bottom: 'TILE_OPEN', left: 'TILE_OPEN' });
  public readonly tile10 = observable({ top: 'TILE_OPEN', right: 'TILE_OPEN', bottom: 'TILE_OPEN', left: 'TILE_OPEN' });
  public readonly tile11 = observable({ top: 'TILE_WALL', right: 'TILE_OPEN', bottom: 'TILE_OPEN', left: 'TILE_OPEN' });
  public readonly tile12 = observable({ top: 'TILE_OPEN', right: 'TILE_OPEN', bottom: 'TILE_OPEN', left: 'TILE_OPEN' });
  public readonly tile20 = observable({ top: 'TILE_OPEN', right: 'TILE_OPEN', bottom: 'TILE_OPEN', left: 'TILE_OPEN' });
  public readonly tile21 = observable({ top: 'TILE_OPEN', right: 'TILE_OPEN', bottom: 'TILE_OPEN', left: 'TILE_OPEN' });
  public readonly tile22 = observable({ top: 'TILE_OPEN', right: 'TILE_OPEN', bottom: 'TILE_OPEN', left: 'TILE_OPEN' });

  private readonly _gameChatClient = new GameChatClient(getConfig().apiUrl);
  private readonly _gameActionClient = new GameActionClient(getConfig().apiUrl);

  constructor() {
    this.onChatPoll.subscribe(() => this.pollChat());
    this.onSendChatMessage.subscribe(m => this.sendChatMessage(m));

    this._gameActionClient.onAction().then(m => {
      this.tile00(m.mapState.tile00);
      this.tile01(m.mapState.tile01);
      this.tile02(m.mapState.tile02);
      this.tile10(m.mapState.tile10);
      this.tile11(m.mapState.tile11);
      this.tile12(m.mapState.tile12);
      this.tile20(m.mapState.tile20);
      this.tile21(m.mapState.tile21);
      this.tile22(m.mapState.tile22);
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
