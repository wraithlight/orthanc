import { subscribable } from "knockout";

export const EVENT_LOGIN_SUCCESS = new subscribable();
export const EVENT_BACK_FROM_CHARACTER_CREATION = new subscribable();
export const EVENT_NEXT_FROM_CHARACTER_CREATION = new subscribable();

export const EVENT_OPEN_HALL_OF_FAME = new subscribable();
export const EVENT_CLOSE_HALL_OF_FAME = new subscribable();

export const EVENT_OPEN_OPTIONS_DIALOG = new subscribable();
export const EVENT_CLOSE_OPTIONS_DIALOG = new subscribable();
