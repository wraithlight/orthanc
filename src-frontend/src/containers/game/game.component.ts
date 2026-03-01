import { Router } from "@profiscience/knockout-contrib-router";
import { observable, observableArray, subscribable } from "knockout";

import { INITIAL_GAME_CHARACTER, INITIAL_GAME_EQUIPMENT, INITIAL_GAME_STATISTICS } from "../../constant";
import { KeyboardEventService } from "../../services";
import { State } from "../../state";
import { SELECTOR } from "../character-creation/character-creation.selector";

import { GameChatClient } from "./game-chat.client";
import { GameActionClient } from "./game-action.client";
import { CharacterGameStats, GameCharacter, GameEquipment } from "../../domain";
import { Environment } from "../../environment";

export class GameContainer {
  public readonly onChatPoll = new subscribable();
  public readonly onSendChatMessage = new subscribable<string>();
  public readonly onActionItemClickHandler = new subscribable<{ key: string, payload: string }>();

  public readonly chatMembers = observableArray([]);
  public readonly chatMessages = observableArray([]);

  public readonly shouldOpenEndDialog = observable(false);
  public readonly shouldOpenRetireDialog = observable(false);

  public readonly tiles = observableArray<unknown>();
  public readonly minimapState = observableArray<ReadonlyArray<unknown>>();

  public readonly playerName = observable();
  public readonly gameState = observable("GAME_RUNNING");
  public readonly maxHits = observable(0);
  public readonly curHits = observable(0);
  public readonly events = observableArray([]);
  public readonly endgameItemIcons = observableArray([]);

  public readonly character = observable<GameCharacter>(INITIAL_GAME_CHARACTER);
  public readonly equipment = observable<GameEquipment>(INITIAL_GAME_EQUIPMENT);
  public readonly stats = observable<CharacterGameStats>(INITIAL_GAME_STATISTICS);

  public readonly actions = observableArray([]);
  public readonly activeSpells = observableArray([]);

  private readonly _keyboardEventService = KeyboardEventService.getInstance();
  private readonly _gameChatClient = new GameChatClient(Environment.apiBaseUrl);
  private readonly _gameActionClient = new GameActionClient(Environment.apiBaseUrl);

  constructor() {
    this.onChatPoll.subscribe(() => this.pollChat());
    this.onSendChatMessage.subscribe(m => this.sendChatMessage(m));
    this.onActionItemClickHandler.subscribe(m => this.onActionItemClick(m.key, m.payload));
    this.actionHandler("INITIAL_IN_GAME", null);

    this._keyboardEventService.subscribe("ArrowLeft", () => this.onActionItemClick("MOVE", "DIRECTION_WEST"));
    this._keyboardEventService.subscribe("ArrowDown", () => this.onActionItemClick("MOVE", "DIRECTION_SOUTH"));
    this._keyboardEventService.subscribe("ArrowUp", () => this.onActionItemClick("MOVE", "DIRECTION_NORTH"));
    this._keyboardEventService.subscribe("ArrowRight", () => this.onActionItemClick("MOVE", "DIRECTION_EAST"));

    this._keyboardEventService.subscribe("KeyA", () => this.onActionItemClick("MOVE", "DIRECTION_WEST"));
    this._keyboardEventService.subscribe("KeyS", () => this.onActionItemClick("MOVE", "DIRECTION_SOUTH"));
    this._keyboardEventService.subscribe("KeyW", () => this.onActionItemClick("MOVE", "DIRECTION_NORTH"));
    this._keyboardEventService.subscribe("KeyD", () => this.onActionItemClick("MOVE", "DIRECTION_EAST"));

    this._keyboardEventService.subscribe("KeyH", () => this.onActionItemClick("MOVE", "DIRECTION_WEST"));
    this._keyboardEventService.subscribe("KeyJ", () => this.onActionItemClick("MOVE", "DIRECTION_SOUTH"));
    this._keyboardEventService.subscribe("KeyK", () => this.onActionItemClick("MOVE", "DIRECTION_NORTH"));
    this._keyboardEventService.subscribe("KeyL", () => this.onActionItemClick("MOVE", "DIRECTION_EAST"));
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
    if (this.shouldOpenRetireDialog() || this.shouldOpenEndDialog()) {
      return;
    }

    if (action === "EVENT_RETIRE") {
      this.shouldOpenRetireDialog(true);
      this._keyboardEventService.subscribe("Enter", () => this.restartGame());
      this._keyboardEventService.subscribe("KeyY", () => this.restartGame());
      this._keyboardEventService.subscribe("KeyN", () => this.closeRetireDialog());
      this._keyboardEventService.subscribe("Escape", () => this.closeRetireDialog());
      return;
    }

    this._gameActionClient.onAction(action, payload).then(m => {
      this.tiles(m.mapState);
      this.maxHits(m.maxHits);
      this.curHits(m.hits);
      this.equipment(m.equipment);
      this.character(m.character);
      this.stats(m.statistics);
      this.actions(m.possibleActions);
      this.playerName(m.playerName);
      this.activeSpells(m.activeSpells);
      this.gameState(m.gameState);
      this.events(m.events);
      this.endgameItemIcons(m.endgameItems.map((o: any) => o.iconName));
      this.minimapState(m.minimapState);

      if (this.gameState() !== "GAME_RUNNING") {
        this.shouldOpenEndDialog(true);
        this._keyboardEventService.subscribe("Enter", () => this.closeEndDialog());
        this._keyboardEventService.subscribe("KeyO", () => this.closeEndDialog());
        this._keyboardEventService.subscribe("Escape", () => this.closeEndDialog());
        this._keyboardEventService.subscribe("KeyR", () => this.restartGame());
      }
    });
  }

  public closeEndDialog() {
    this.shouldOpenEndDialog(false);
    this._keyboardEventService.unsubscribe("Enter");
    this._keyboardEventService.unsubscribe("KeyO");
    this._keyboardEventService.unsubscribe("Escape");
  }

  public restartGame() {
    this._keyboardEventService.unsubscribe("KeyR");
    this._keyboardEventService.unsubscribe("Enter");
    this._keyboardEventService.unsubscribe("KeyY");
    Router.update(`/${SELECTOR}`);
  }

  public closeRetireDialog() {
    this._keyboardEventService.unsubscribe("KeyN");
    this._keyboardEventService.unsubscribe("Escape");
    this.shouldOpenRetireDialog(false);
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
