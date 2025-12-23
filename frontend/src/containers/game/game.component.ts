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

  public readonly maxHits = observable(0);
  public readonly curHits = observable(0);

  public readonly characterDexterity = observable(0);
  public readonly characterIntelligence = observable(0);
  public readonly characterStrength = observable(0);
  public readonly characterConstitution = observable(0);

  public readonly equipmentSword = observable("???");
  public readonly equipmentShield = observable("???");
  public readonly equipmentArmor = observable("???");
  public readonly equipmentArrows = observable(0);

  public readonly statisticsExperience = observable(0);
  public readonly statisticsMoney = observable(0);
  public readonly statisticsNextLevelInXp = observable(0);
  public readonly statisticsWeight = observable(0);
  public readonly statisticsPlayerLevel = observable(0);
  public readonly statisticsSpellUnitsCur = observable(0);
  public readonly statisticsSpellUnitsMax = observable(0);
  public readonly statisticsXpPercentageFromKills = observable(0);
  public readonly actions = observableArray([]);

  private readonly _gameChatClient = new GameChatClient(getConfig().apiUrl);
  private readonly _gameActionClient = new GameActionClient(getConfig().apiUrl);

  constructor() {
    this.onChatPoll.subscribe(() => this.pollChat());
    this.onSendChatMessage.subscribe(m => this.sendChatMessage(m));
    this.actionHandler("INITIAL");
  }

  public onActionItemClick(action: string): void {
    this.actionHandler(action);
  }

  private actionHandler(action: string): void {
    this._gameActionClient.onAction(action).then(m => {
      this.tile00(m.mapState.tile00);
      this.tile01(m.mapState.tile01);
      this.tile02(m.mapState.tile02);
      this.tile10(m.mapState.tile10);
      this.tile11(m.mapState.tile11);
      this.tile12(m.mapState.tile12);
      this.tile20(m.mapState.tile20);
      this.tile21(m.mapState.tile21);
      this.tile22(m.mapState.tile22);
      this.maxHits(m.maxHits);
      this.curHits(m.hits);
      this.characterDexterity(m.character.dexterity);
      this.characterIntelligence(m.character.intelligence);
      this.characterStrength(m.character.strength);
      this.characterConstitution(m.character.constitution);
      this.equipmentSword(m.equipment.sword);
      this.equipmentShield(m.equipment.shield);
      this.equipmentArmor(m.equipment.armor);
      this.equipmentArrows(m.equipment.arrows);
      this.statisticsExperience(m.statistics.experience);
      this.statisticsMoney(m.statistics.money);
      this.statisticsNextLevelInXp(m.statistics.nextLevelInXp);
      this.statisticsWeight(m.statistics.weight);
      this.statisticsPlayerLevel(m.statistics.playerLevel);
      this.statisticsSpellUnitsCur(m.statistics.spellUnits.current);
      this.statisticsSpellUnitsMax(m.statistics.spellUnits.maximum);
      this.statisticsXpPercentageFromKills(m.statistics.xpPercentageFromKills);
      this.actions(m.possibleActions);
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
