<?php

class LevelService {

  private const THRESHOLD_LEVEL = 8;
  private const THRESHOLD_XP = 128000;
  private const XP_RATIO = 500;

  private $_levelState;

  public function __construct() {
    $this->_levelState = new LevelState();
  }

  public function setCurrentAllXp(int $xp): int {
    return $this->_levelState->setXpCurrent($xp);
  }

  public function setCurrentKillsXp(int $xp): int {
    return $this->_levelState->setXpKillsCurrent($xp);
  }

  public function getCurrentXp(): int {
    return $this->getCurrentXpCore();
  }

  public function getXpPercentageFromKills(): int {
    return $this->getXpPercentageFromKillsCore();
  }

  public function getLevel(): int {
    return $this->getLevelCore();
  }

  public function getXpForNextLevel(): int {
    $currentLevel = $this->getLevelCore();
    $targetLevel = $currentLevel + 1;

    $xpForNextLevel = 0;
    if ($targetLevel > self::THRESHOLD_LEVEL) {
      $levelsAbove = $targetLevel - self::THRESHOLD_LEVEL;
      $xpForNextLevel = $levelsAbove * self::THRESHOLD_XP;
    } else {
      $xpForNextLevel = pow(2, $currentLevel) * self::XP_RATIO;
    }
    return $xpForNextLevel - $this->getCurrentXpCore();
  }

  public function getMaxSpellUnits(): int {
    $level = $this->getLevelCore();
    return ($level + 1) * 2;
  }

  public function addXp(int $xp): int {
    $xpCurrent = $this->getCurrentXpCore();
    return $this->_levelState->setXpCurrent($xpCurrent + $xp);
  }

  public function addXpFromKills(int $xp): int {
    $xpCurrent = $this->_levelState->getXpKillsCurrent();
    return $this->_levelState->setXpKillsCurrent($xpCurrent + $xp);
  }

  private function getLevelCore(): int {
    $xp = $this->getCurrentXpCore();

    if ($xp > self::THRESHOLD_XP) {
      $levelsAbove = (int) floor($xp / self::THRESHOLD_XP);
      return $levelsAbove + self::THRESHOLD_LEVEL;
    } else {
      $lvlBase = floor($xp / self::XP_RATIO);
      return $lvlBase < 1
        ? 1
        : floor(log($lvlBase, 2)) + 1
      ;
    }
  }

  private function getXpPercentageFromKillsCore(): int {
    $xpAll = $this->getCurrentXpCore();
    $xpKills = $this->_levelState->getXpKillsCurrent();
    return $xpKills === 0 ? 0 : ($xpKills / $xpAll) * 100;
  }

  private function getCurrentXpCore(): int {
    return $this->_levelState->getXpCurrent();
  }

}
