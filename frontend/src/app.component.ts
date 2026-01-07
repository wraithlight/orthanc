import { Router } from "@profiscience/knockout-contrib-router";
import { observable } from "knockout";

import { SELECTOR as CHARACTER_CREATION_SELECTOR } from './containers/character-creation/character-creation.selector';
import { SELECTOR as GAME_SELECTOR } from './containers/game/game.selector';
import { SELECTOR as LOGIN_SELECTOR } from './containers/login/login.selector';

import { AppState } from "./model";
import { getConfig } from "./state";

export class Application {
  public mode = observable<"KEYBOARD_ONLY" | "KEYBOARD_WITH_MOUSE">();
  public state = observable<AppState>(AppState.LOGIN);
  public readonly config = getConfig();

  public readonly playerName = observable("");

  constructor() {
    window.addEventListener("LOGIN_SUCCESS", ((event: CustomEvent) => { this.onLoginSuccessHandler(event.detail.playerName); }) as EventListener);
    window.addEventListener("ON_BACK_FROM_CHARACTER_CREATION", ((_event: CustomEvent) => { this.onBackFromCharacterCreationHandler(); }) as EventListener);
    window.addEventListener("ON_NEXT_FROM_CHARACTER_CREATION", ((_event: CustomEvent) => { this.onNextFromCharacterCreationHandler(); }) as EventListener);
  }

  public async onLoginSuccessHandler(playerName: string): Promise<void> {
    setTimeout(() => {
      this.playerName(playerName);
      Router.update(`/${CHARACTER_CREATION_SELECTOR}`);
    }, 500);
  }

  public async onBackFromCharacterCreationHandler(): Promise<void> {
    setTimeout(() => {
      this.playerName("");
      Router.update(`/${LOGIN_SELECTOR}`);
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
    this.playerName("");
    Router.update(`/${GAME_SELECTOR}`);
  }
}
