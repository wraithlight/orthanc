<?php

class PlayerService {

  private $_playerState;

  public function __construct() {
    $this->_playerState = new PlayerState();
  }

  public function decrementCurrentHitsBy(int $amount): int {
    $currentHits = $this->_playerState->getCurrentHits();
    return $this->_playerState->setCurrentHits($currentHits - $amount);
  }

  public function setCurrentHitsToMax(): int {
    $maxHits = $this->_playerState->getMaxHits();
    return $this->_playerState->setCurrentHits($maxHits);
  }

  public function isAlive(): bool {
    return $this->_playerState->getCurrentHits() > 0;
  }

  public function isDead(): bool {
    return $this->_playerState->getCurrentHits() <= 0;
  }

  public function getCurrentHits(): int {
    return $this->_playerState->getCurrentHits();
  }

  public function setMaxHits(int $hits): int {
    return $this->_playerState->setMaxHits($hits);
  }

  public function getMaxHits(): int {
    return $this->_playerState->getMaxHits();
  }

  public function decrementCurrentSpellUnityBy(int $amount): int {
    $currentSpellUnits = $this->_playerState->getCurrentSpellUnits();
    return $this->_playerState->setCurrentSpellUnits($currentSpellUnits - $amount);
  }

  public function setCurrentSpellUnits(int $spellUnits): int {
    return $this->_playerState->setCurrentSpellUnits($spellUnits);
  }

  public function hasSpellUnits(): bool {
    return $this->_playerState->getCurrentSpellUnits() > 0;
  }

  public function getCurrentSpellUnits(): int {
    return $this->_playerState->getCurrentSpellUnits();
  }

}
