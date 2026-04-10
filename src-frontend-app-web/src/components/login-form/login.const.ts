import { GameMode } from "../../domain";

export const DEFAULT_GAME_MODE = GameMode.Vanilla;

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
