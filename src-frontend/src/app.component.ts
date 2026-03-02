import { Router } from "@profiscience/knockout-contrib-router";
import { observable, subscribable } from "knockout";

import { SELECTOR as CHARACTER_CREATION_SELECTOR } from './containers/character-creation/character-creation.selector';
import { SELECTOR as GAME_SELECTOR } from './containers/game/game.selector';
import { SELECTOR as LOGIN_SELECTOR } from './containers/login/login.selector';

import { State, createConfigState } from "./state"
import { ConfigurationService, DialogQueueService } from "./services";
import { Environment } from "./environment";

export class Application {
  public readonly isLoading = observable(true);

  private readonly _dialogQueueService = DialogQueueService.getInstance();
  private readonly _configurationService = new ConfigurationService();

  constructor() {
    State.events.loginSuccess.subscribe(() => this.onLoginSuccessHandler());
    State.events.nextFromCharacterCreation.subscribe(() => this.onNextFromCharacterCreationHandler());
    State.events.backFromCharacterCreation.subscribe(() => this.onBackFromCharacterCreationHandler());

    Promise.all([
      this._configurationService.fetchConfiguration(),
      new Promise((resolve, _reject) => setTimeout(() => resolve(undefined), 1000))
    ])
      .then(([m, _]) => createConfigState(m))
      .catch(() => {
        const closeErrorDialog = new subscribable();
        this._dialogQueueService.openDialog(
          "config-fetch-failed-dialog",
          "Error!",
          "There was an error while fethcing the initial configuration!",
          [
            {
              id: "cta-ok",
              label: "Ok",
              onClick: () => closeErrorDialog.notifySubscribers()
            }
          ],
          closeErrorDialog
        )
      })
      .finally(() => this.isLoading(false));
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
    await fetch(
      `${Environment.apiBaseUrl}/api/v1/game/start`,
      {
        method: "POST",
        credentials: "include"
      }
    );
    Router.update(`/${GAME_SELECTOR}`);
  }
}
