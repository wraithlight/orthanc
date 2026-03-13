<?php
class HallOfFameItemDto {
  public string $name;
  public string $id;
  public string $started;
  public string $finished;
  public int $duration;
  public int $sumXp;
  public int $xpFromKillsPercentage;
  public int $numberOfMoves;
  public int $numberOfActions;
  public int $characterLevel;
  public string $gameVersion;
  public GameMode $gameMode;
}
