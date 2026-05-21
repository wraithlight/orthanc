import { GameMode } from "../domain";

export interface LoginAsMemberEvent {
  username: string;
  password: string;
  gameMode: GameMode;
}

export interface LoginAsGuestEvent {
  gameMode: GameMode;
}
