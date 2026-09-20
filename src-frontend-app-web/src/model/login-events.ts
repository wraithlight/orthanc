import { GameMode } from "../domain";
import { PostLoginGuestRequestGameMode } from "../dal-generated";

export interface LoginAsMemberEvent {
  username: string;
  password: string;
  gameMode: GameMode;
}

export interface LoginAsGuestEvent {
  gameMode: PostLoginGuestRequestGameMode;
}
