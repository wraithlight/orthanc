import { GameMode } from "../../domain";

export const GAME_MODE_OPTIONS = [
  {
    value: GameMode.Retail,
    label: GameMode.Retail.toUpperCase(),
  },
  {
    value: GameMode.Vanilla,
    label: GameMode.Vanilla.toUpperCase(),
  }
];
