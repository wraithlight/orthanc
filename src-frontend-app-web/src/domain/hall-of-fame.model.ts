import { GameMode } from "./game-mode.enum";

interface HallOfFameItemModel {
  name: string;
  level: number;
  sessionStartAtUtc: number;        // TODO: #316
  sessionEndAtUtc: number;          // TODO: #316
  sessionLengthInMs: number;
  experiencePoints: number;
  experienceFromKillsPercentage: number;
  gameVersion: string;
  numberOfMoves: number;
  numberOfActions: number;
  gameMode: GameMode;
}

export interface HallOfFameListModel {
  items: ReadonlyArray<HallOfFameItemModel>;
}
