import { Router } from "@profiscience/knockout-contrib-router";
import { observable } from "knockout";

import { SELECTOR as CHARACTER_CREATION_SELECTOR } from './containers/character-creation/character-creation.selector';
import { SELECTOR as GAME_SELECTOR } from './containers/game/game.selector';
import { SELECTOR as LOGIN_SELECTOR } from './containers/login/login.selector';

import { AppState } from "./model";
import { State } from "./state"

export class Application {
  public mode = observable<"KEYBOARD_ONLY" | "KEYBOARD_WITH_MOUSE">();
  public state = observable<AppState>(AppState.LOGIN);
  public readonly config = State.config();

  constructor() {
    State.events.loginSuccess.subscribe(() => this.onLoginSuccessHandler());
    State.events.nextFromCharacterCreation(() => this.onNextFromCharacterCreationHandler);
    State.events.backFromCharacterCreation(() => this.onBackFromCharacterCreationHandler);
  }

  public async onLoginSuccessHandler(): Promise<void> {
    setTimeout(() => {
      Router.update(`/${CHARACTER_CREATION_SELECTOR}`);
    }, 500);
  }

  public async onBackFromCharacterCreationHandler(): Promise<void> {
    setTimeout(() => {
      Router.update(`/${LOGIN_SELECTOR}`);
    }, 500);
  }

  public async onNextFromCharacterCreationHandler(): Promise<void> {
    this.config && await fetch(
      `${this.config.apiUrl}/api/v1/game/start`,
      {
        method: "POST",
        credentials: "include"
      }
    );
    Router.update(`/${GAME_SELECTOR}`);
  }
}
