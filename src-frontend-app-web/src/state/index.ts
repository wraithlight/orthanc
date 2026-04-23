import { ApplicationConfiguration } from "../domain";
import { Predicate } from "../framework";

import { ApplicationConfigurationState } from "./application-configuration";
import {
  EVENT_BACK_FROM_CHARACTER_CREATION,
  EVENT_CLOSE_HALL_OF_FAME,
  EVENT_CLOSE_OPTIONS_DIALOG,
  EVENT_LOGIN_SUCCESS,
  EVENT_NEXT_FROM_CHARACTER_CREATION,
  EVENT_OPEN_HALL_OF_FAME,
  EVENT_OPEN_OPTIONS_DIALOG
} from "./events";
import { STATE_NAME } from "./player-name";

export * from "./application-configuration";

export const State = {
  name: STATE_NAME,
  events: {
    loginSuccess: EVENT_LOGIN_SUCCESS,
    openHallOfFame: EVENT_OPEN_HALL_OF_FAME,
    closeHallOfFame: EVENT_CLOSE_HALL_OF_FAME,
    openOptionsDialog: EVENT_OPEN_OPTIONS_DIALOG,
    closeOptionsDialog: EVENT_CLOSE_OPTIONS_DIALOG,
    nextFromCharacterCreation: EVENT_BACK_FROM_CHARACTER_CREATION,
    backFromCharacterCreation: EVENT_NEXT_FROM_CHARACTER_CREATION,
  }
};

let _config: ApplicationConfigurationState;
export const createConfigState = (config: ApplicationConfiguration) => {
  _config = new ApplicationConfigurationState(config);
};
export const readFromConfigState = <T, U extends T>(
  predicate: Predicate<ApplicationConfiguration, T>,
  defaultValue: U
) => _config
  ? _config.getOrDefault<T, U>(predicate, defaultValue)
  : defaultValue
;
