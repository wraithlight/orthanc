import { CharacterGameStats } from "../domain";

export const INITIAL_GAME_STATISTICS: CharacterGameStats = {
  experience: 0,
  money: 0,
  nextLevelInXp: 0,
  weight: 0,
  playerLevel: 0,
  spellUnits: {
    current: 0,
    maximmum: 0
  },
  xpPercentageFromKills: 0
};
