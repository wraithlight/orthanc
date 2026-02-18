<?php

class LevelState extends BaseState {

  private const XP_ALL_CURRENT = "LEVEL__XP_ALL_CURRENT";
  private const XP_KILLS_CURRENT = "LEVEL__XP_KILLS_CURRENT";

  public function setXpCurrent(int $xp): int {
    $this->writeState(self::XP_ALL_CURRENT, $xp);
    return $this->getXpCurrentCore();
  }

  public function getXpCurrent(): int {
    return $this->getXpCurrentCore();
  }

  public function setXpKillsCurrent(int $xp): int {
    $this->writeState(self::XP_KILLS_CURRENT, $xp);
    return $this->getXpKillsCurrentCore();
  }

  public function getXpKillsCurrent(): int {
    return $this->getXpKillsCurrentCore();
  }

  private function getXpCurrentCore(): int {
    return $this->readState(self::XP_ALL_CURRENT);
  }

  private function getXpKillsCurrentCore(): int {
    return $this->readState(self::XP_KILLS_CURRENT);
  }

}
