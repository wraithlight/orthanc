export interface CharacterGameStats {
  experience: number,
  money: number,
  nextLevelInXp: number,
  weight: number,
  playerLevel: number,
  spellUnits: {
    current: number,
    maximum: number
  },
  xpPercentageFromKills: number
}
