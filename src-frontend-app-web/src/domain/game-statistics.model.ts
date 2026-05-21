export interface CharacterGameStats {
  experience: number,
  money: number,
  nextLevelInXp: number,
  weight: number,
  playerLevel: number,
  spellUnits: {
    current: number,
    maximmum: number
  },
  xpPercentageFromKills: number
}
