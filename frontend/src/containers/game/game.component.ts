import { observable, observableArray, subscribable } from "knockout";

import { KeyboardEventService } from "../../services";
import { getConfig } from "../../state";

import { GameChatClient } from "./game-chat.client";
import { GameActionClient } from "./game-action.client";

export class GameContainer {
  public readonly onChatPoll = new subscribable();
  public readonly onSendChatMessage = new subscribable<string>();

  public readonly chatMembers = observableArray([]);
  public readonly chatMessages = observableArray([]);

  public readonly tiles = observableArray<unknown>();

  public readonly playerName = observable();
  public readonly hasPlayerWon = observable(false);
  public readonly shouldOpenWinDialog = observable(true);
  public readonly maxHits = observable(0);
  public readonly curHits = observable(0);
  public readonly events = observableArray([]);

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
  public readonly activeSpells = observableArray([]);

  private readonly _keyboardEventService = KeyboardEventService.getInstance();
  private readonly _gameChatClient = new GameChatClient(getConfig().apiUrl);
  private readonly _gameActionClient = new GameActionClient(getConfig().apiUrl);

  constructor() {
    this.onChatPoll.subscribe(() => this.pollChat());
    this.onSendChatMessage.subscribe(m => this.sendChatMessage(m));
    this.actionHandler("INITIAL", null);

    this._keyboardEventService.subscribe("ArrowLeft", () => this.onActionItemClick("MOVE", "DIRECTION_WEST"));
    this._keyboardEventService.subscribe("ArrowDown", () => this.onActionItemClick("MOVE", "DIRECTION_SOUTH"));
    this._keyboardEventService.subscribe("ArrowUp", () => this.onActionItemClick("MOVE", "DIRECTION_NORTH"));
    this._keyboardEventService.subscribe("ArrowRight", () => this.onActionItemClick("MOVE", "DIRECTION_EAST"));
  }

  public onActionItemClick(
    action: string,
    payload: string | null
  ): void {
    this.actionHandler(action, payload);
  }

  private actionHandler(
    action: string,
    payload: string | null
  ): void {
    this._gameActionClient.onAction(action, payload).then(m => {
      this.tiles(m.mapState);
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
      this.playerName(m.playerName);
      this.activeSpells(m.activeSpells);
      this.hasPlayerWon(m.hasPlayerWon);
      this.events(m.events);
    });
  }

  public closeWinDialog() {
    this.shouldOpenWinDialog(false);
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
