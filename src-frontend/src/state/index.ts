import { ApplicationConfiguration } from "../domain";
import { Predicate } from "../framework";

import { ApplicationConfigurationState } from "./application-configuration";
import {
  EVENT_BACK_FROM_CHARACTER_CREATION,
  EVENT_CLOSE_HALL_OF_FAME,
  EVENT_LOGIN_SUCCESS,
  EVENT_NEXT_FROM_CHARACTER_CREATION,
  EVENT_OPEN_HALL_OF_FAME
} from "./events";
import { STATE_NAME } from "./player-name";

export * from "./application-configuration";

export const State = {
  name: STATE_NAME,
  events: {
    loginSuccess: EVENT_LOGIN_SUCCESS,
    openHallOfFame: EVENT_OPEN_HALL_OF_FAME,
    closeHallOfFame: EVENT_CLOSE_HALL_OF_FAME,
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
) => _config.getOrDefault<T, U>(predicate, defaultValue);
