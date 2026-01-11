import { STATE_CONFIG } from "./config";
import {
  EVENT_BACK_FROM_CHARACTER_CREATION,
  EVENT_LOGIN_SUCCESS,
  EVENT_NEXT_FROM_CHARACTER_CREATION
} from "./events";
import { STATE_NAME } from "./player-name";

export const State = {
  name: STATE_NAME,
  config: STATE_CONFIG,
  events: {
    loginSuccess: EVENT_LOGIN_SUCCESS,
    nextFromCharacterCreation: EVENT_BACK_FROM_CHARACTER_CREATION,
    backFromCharacterCreation: EVENT_NEXT_FROM_CHARACTER_CREATION,
  }
};
