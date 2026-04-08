import { Router } from "@profiscience/knockout-contrib-router";
import { observable, subscribable } from "knockout";

import { SELECTOR as CHARACTER_CREATION_SELECTOR } from './containers/character-creation/character-creation.selector';
import { SELECTOR as GAME_SELECTOR } from './containers/game/game.selector';
import { SELECTOR as LOGIN_SELECTOR } from './containers/login/login.selector';

import { Environment } from "./environment";
import { GameMode, HeaderNames, HeaderValueAccept } from "./domain";
import { newGuid } from "./framework";
import { ConfigurationService, DialogQueueService, HallOfFameService } from "./services";
import { State, createConfigState } from "./state"
import { createAfterInterceptor } from "./http";
import { RuntimeContext } from "./runtime-context";

export class Application {
  public readonly isLoading = observable(true);

  private readonly _dialogQueueService = DialogQueueService.getInstance();
  private readonly _configurationService = new ConfigurationService();
  private readonly _hallOfFameService = new HallOfFameService();

  constructor() {
    State.events.loginSuccess.subscribe(() => this.onLoginSuccessHandler());
    State.events.openHallOfFame.subscribe(() => this.onOpenHallOfFameHandler());
    State.events.nextFromCharacterCreation.subscribe(() => this.onNextFromCharacterCreationHandler());
    State.events.backFromCharacterCreation.subscribe(() => this.onBackFromCharacterCreationHandler());

    Promise.all([
      this._configurationService.fetchConfiguration(),
      new Promise((resolve, _reject) => setTimeout(() => resolve(undefined), 1000))
    ])
      .then(([m, _]) => {
        createConfigState(m);
        let isVersionDialogVisible = false;
        createAfterInterceptor((res: Response) => {
          if (isVersionDialogVisible) {
            return;
          }
          const platformVersion = res.headers.get(HeaderNames.PlatformVersion.toLowerCase());
          const configVersion = m.version;

          if (platformVersion !== configVersion) {
            isVersionDialogVisible = true;
            const closeVersionMismatchDialog = new subscribable();
            closeVersionMismatchDialog.subscribe(() => location.reload());
            this._dialogQueueService.openDialog(
              "version-mismatch-dialog",
              "Warning!",
              "You have an outdated version of the game! Some features may not work, please refresh the window to avoid version mismatch issues.",
              [
                {
                  id: "cta-refresh",
                  label: "Refresh",
                  onClick: () => closeVersionMismatchDialog.notifySubscribers()
                }
              ],
              closeVersionMismatchDialog
            )
          }
        });
      })
      .catch(() => {
        const closeErrorDialog = new subscribable();
        closeErrorDialog.subscribe(() => location.reload());
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

  public async onOpenHallOfFameHandler(): Promise<void> {
    const retailResults = await this._hallOfFameService.fetchHallOfFame(GameMode.Retail);
    const vanillaResults = await this._hallOfFameService.fetchHallOfFame(GameMode.Vanilla);

    const retailRowHtml = retailResults.items.map(m => `<tr><td>${m.name}</td><td>${m.level.toString()}</td><td>${m.sessionStartAtUtc}</td><td>${m.sessionEndAtUtc}</td><td>${m.sessionLengthInMs}</td><td>${m.experiencePoints}</td><td>${m.experienceFromKillsPercentage}</td><td>${m.gameVersion}</td><td>${m.numberOfMoves}</td><td>${m.numberOfActions}</td></tr>`);
    const vanillaRowHtml = vanillaResults.items.map(m => `<tr><td>${m.name}</td><td>${m.level}</td><td>${m.sessionStartAtUtc}</td><td>${m.sessionEndAtUtc}</td><td>${m.sessionLengthInMs}</td><td>${m.experiencePoints}</td><td>${m.experienceFromKillsPercentage}</td><td>${m.gameVersion}</td><td>${m.numberOfMoves}</td><td>${m.numberOfActions}</td></tr>`);

    const commonHead = "<tr><td>Name</td><td>Level</td><td>Session start</td><td>Session end</td><td>Session length</td><td>Experience</td><td>Experience from kills</td><td>Game version</td><td>Number of moves</td><td>Number of actions</td></tr>";
    const retailHtml = `<table>${commonHead}${retailRowHtml}</table>`;
    const vanillaHtml = `<table>${commonHead}${vanillaRowHtml}</table>`;

    const html = `<h2>Retail</h2>${retailHtml}<h2>Vanilla</h2>${vanillaHtml}`;

    this._dialogQueueService.openDialog(
      "hall-of-fame",
      "Hall of Fame",
      html,
      [
        {
          id: "close-hall-of-fame",
          label: "Close",
          onClick: () => State.events.closeHallOfFame.notifySubscribers()
        }
      ],
      State.events.closeHallOfFame
    )
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
        credentials: "include",
        headers: {
          [HeaderNames.Platform]: Environment.platform,
          [HeaderNames.Device]: RuntimeContext.device,
          [HeaderNames.RequestId]: newGuid(),
          [HeaderNames.Accept]: HeaderValueAccept.ApplicationJson,
        }
      }
    );
    Router.update(`/${GAME_SELECTOR}`);
  }
}
