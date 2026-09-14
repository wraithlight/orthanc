import { Router } from "@profiscience/knockout-contrib-router";
import { observable, subscribable } from "knockout";

import { SELECTOR as CHARACTER_CREATION_SELECTOR } from './containers/character-creation/character-creation.selector';
import { SELECTOR as GAME_SELECTOR } from './containers/game/game.selector';
import { SELECTOR as LOGIN_SELECTOR } from './containers/login/login.selector';

import { Environment } from "./environment";
import { GameMode } from "./domain";
import { newGuid } from "./framework";
import { ConfigurationService, DialogQueueService, HallOfFameService, LocaleService, LocalizationService } from "./services";
import { State, createConfigState } from "./state"
import { RuntimeContext } from "./runtime-context";
import { createVersionCheckerInterceptor } from "./interceptors";
import { doVersionCheck } from "./version-check";
import { LocalizationRepository } from "./repository";

import { HeaderNames, AcceptValues, API_POST_START_GAME_PATH } from "./dal-generated";

export class Application {
  public readonly isLoading = observable(true);

  private readonly _dialogQueueService = DialogQueueService.getInstance();
  private readonly _configurationService = new ConfigurationService();
  private readonly _hallOfFameService = new HallOfFameService();
  private readonly _localizationService = new LocalizationService();
  private readonly _localeService = new LocaleService();
  private readonly _localizationRepository = LocalizationRepository.getInstance();

  constructor() {
    State.events.loginSuccess.subscribe(() => this.onLoginSuccessHandler());
    State.events.openHallOfFame.subscribe(() => this.onOpenHallOfFameHandler());
    State.events.openOptionsDialog.subscribe(() => this.onOpenOptionsDialogHandler());
    State.events.nextFromCharacterCreation.subscribe(() => this.onNextFromCharacterCreationHandler());
    State.events.backFromCharacterCreation.subscribe(() => this.onBackFromCharacterCreationHandler());

    createVersionCheckerInterceptor();

    const configAndLocale$ = async () => {
      try {
        const [version, config] = await this._configurationService.fetchConfiguration();
        createConfigState(config);
        doVersionCheck(version);

        const locale = config.featureStates.applicationDefaultLanguageDefault.value;
        const localization = await this._localizationService.getLocalization(locale);
        this._localeService.setCurrentLocale(locale);
        this._localizationRepository.setLocalization(localization);
      } catch {
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
      }
    };

    Promise.all([
      configAndLocale$(),
      new Promise((resolve, _reject) => setTimeout(() => resolve(undefined), 1_000))
    ]).finally(() => this.isLoading(false));
  }

  public async onLoginSuccessHandler(): Promise<void> {
    setTimeout(() => {
      Router.update(`/${CHARACTER_CREATION_SELECTOR}`);
    }, 500);
  }

  private onOpenOptionsDialogHandler(): void {
    this._dialogQueueService.openDialogWithComponent(
      "options-dialog",
      "Options",
      "orthanc-dialog-options",
      (_params: unknown) => {},
      State.events.closeOptionsDialog
    );
  }

  private async onOpenHallOfFameHandler(): Promise<void> {
    const retailResults = await this._hallOfFameService.fetchHallOfFame(GameMode.Retail);
    const vanillaResults = await this._hallOfFameService.fetchHallOfFame(GameMode.Vanilla);

    const retailRowHtml = retailResults.items.map(m => `<tr><td>${m.name}</td><td>${m.characterLevel.toString()}</td><td>${m.started}</td><td>${m.finished}</td><td>${m.duration}</td><td>${m.sumXp}</td><td>${m.xpFromKillsPercentage}</td><td>${m.gameVersion}</td><td>${m.numberOfMoves}</td><td>${m.numberOfActions}</td></tr>`);
    const vanillaRowHtml = vanillaResults.items.map(m => `<tr><td>${m.name}</td><td>${m.characterLevel.toString()}</td><td>${m.started}</td><td>${m.finished}</td><td>${m.duration}</td><td>${m.sumXp}</td><td>${m.xpFromKillsPercentage}</td><td>${m.gameVersion}</td><td>${m.numberOfMoves}</td><td>${m.numberOfActions}</td></tr>`);

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
      `${Environment.apiBaseUrl}${API_POST_START_GAME_PATH()}`,
      {
        method: "POST",
        credentials: "include",
        headers: {
          [HeaderNames.PLATFORM]: Environment.platform,
          [HeaderNames.DEVICE]: RuntimeContext.device,
          [HeaderNames.REQUESTID]: newGuid(),
          [HeaderNames.ACCEPTSAPPJSON]: AcceptValues.ApplicationJson,
        }
      }
    );
    Router.update(`/${GAME_SELECTOR}`);
  }
}
