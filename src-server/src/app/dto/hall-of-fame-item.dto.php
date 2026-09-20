<?php
class HallOfFameItemDto {
  public string $name;
  public string $id;
  public string $sessionStartAtUtc;
  public string $sessionEndAtUtc;
  public int $sessionLengthInMs;
  public int $experiencePoints;
  public int $experienceFromKillsPercentage;
  public int $numberOfMoves;
  public int $numberOfActions;
  public int $characterLevel;
  public string $gameVersion;
  public GameMode $gameMode;
}
