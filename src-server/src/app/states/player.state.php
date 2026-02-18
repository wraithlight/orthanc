<?php

class PlayerState extends BaseState {

  private const HITS_CURRENT = "PLAYER__CURRENT_HITS";
  private const HITS_MAX = "PLAYER__MAX_HITS";
  private const SPELL_UNITS_CURRENT = "PLAYER__CURRENT_SPELL_UNITS";

  public function setCurrentHits(int $hits): int {
    return $this->writeState(self::HITS_CURRENT, $hits);
  }

  public function getCurrentHits(): int {
    return $this->readState(self::HITS_CURRENT);
  }

  public function setMaxHits(int $hits): int {
    return $this->writeState(self::HITS_MAX, $hits);
  }

  public function getMaxHits(): int {
    return $this->readState(self::HITS_MAX);
  }

  public function setCurrentSpellUnits(int $spellUnits): int {
    return $this->writeState(self::SPELL_UNITS_CURRENT, $spellUnits);
  }

  public function getCurrentSpellUnits(): int {
    return $this->readState(self::SPELL_UNITS_CURRENT);
  }

}
