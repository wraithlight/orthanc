import { GameMode } from "./game-mode.enum";

interface HallOfFameItemModel {
  name: string;
  characterLevel: number;
  started: number;        // TODO: #316
  finished: number;          // TODO: #316
  duration: number;
  sumXp: number;
  xpFromKillsPercentage: number;
  gameVersion: string;
  numberOfMoves: number;
  numberOfActions: number;
  gameMode: GameMode;
}

export interface HallOfFameListModel {
  items: ReadonlyArray<HallOfFameItemModel>;
}
