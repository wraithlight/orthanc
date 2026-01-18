<?php
class StateService
{
  const STAT_DEX = "PLAYER_STAT_DEX"; // stats
  const STAT_INT = "PLAYER_STAT_INT"; // stats
  const STAT_STR = "PLAYER_STAT_STR"; // stats
  const STAT_CON = "PLAYER_STAT_CON"; // stats
  const EQUIPMENT_SWORD = "EQUIPMENT_SWORD"; // equipment
  const EQUIPMENT_SHIELD = "EQUIPMENT_SHIELD"; // equipment
  const EQUIPMENT_ARROWS = "EQUIPMENT_ARROWS"; // equipment
  const EQUIPMENT_ARMOR = "EQUIPMENT_ARMOR"; // equipment
  const EQUIPMENT_BOW = "EQUIPMENT_BOW"; // equipment
  const CHARACTER_STATS_MONEY = "CHARACTER_STATS_MONEY"; // iventory
  const CHARACTER_STATS_WEIGHT = "CHARACTER_STATS_WEIGHT"; // on-demand calculation (inventory + equipment)
  const CHARACTER_SPELLS_ON = "CHARACTER_SPELLS_ON"; // spells
  const MAP_FULL = "MAP_FULL"; // map
  const INVENTORY_ORB = "INVENTORY_ORB"; // inventory
  const INVENTORY_GRAIL = "INVENTORY_GRAIL"; // inventory
  const GAME_START_TIME = "GAME_START_TIME"; // session
  const PREVIOUS_GAME_STATE_KEY = "GAME_STATE_PREVIOUS"; // game state


  public function setPreviousGameState(GameState $state) {
    $this->writeToSessionState(self::PREVIOUS_GAME_STATE_KEY, $state);
  }

  public function getPreviousGameState(): GameState {
    return $this->readFromSessionState(self::PREVIOUS_GAME_STATE_KEY);
  }

  public function setStartTime(int $startTime) {
    $this->writeToSessionState(self::GAME_START_TIME, $startTime);
  }

  public function getStartTime(): int {
    return $this->readFromSessionState(self::GAME_START_TIME);
  }

  public function setMapFull(array $value) {
    $this->writeToSessionState(self::MAP_FULL, $value);
  }

  public function getMapFull(): array {
    return $this->readFromSessionState(self::MAP_FULL);
  }

  public function setCharacterStatsMoney($value)
  {
    $this->writeToSessionState(self::CHARACTER_STATS_MONEY, $value);
  }

  public function setCharacterStatsWeight($value)
  {
    $this->writeToSessionState(self::CHARACTER_STATS_WEIGHT, $value);
  }

  public function setCharacterSpellsOn($value)
  {
    $this->writeToSessionState(self::CHARACTER_SPELLS_ON, $value);
  }

  public function getCharacterStatsMoney()
  {
    return $this->readFromSessionState(self::CHARACTER_STATS_MONEY);
  }

  public function getCharacterStatsWeight()
  {
    return $this->readFromSessionState(self::CHARACTER_STATS_WEIGHT);
  }

  public function getCharacterSpellsOn()
  {
    return $this->readFromSessionState(self::CHARACTER_SPELLS_ON);
  }

  public function setEquipmentSword($value)
  {
    $this->writeToSessionState(self::EQUIPMENT_SWORD, $value);
  }

  public function getEquipmentSword()
  {
    return $this->readFromSessionState(self::EQUIPMENT_SWORD);
  }

  public function setEquipmentShield($value)
  {
    $this->writeToSessionState(self::EQUIPMENT_SHIELD, $value);
  }

  public function getEquipmentShield()
  {
    return $this->readFromSessionState(self::EQUIPMENT_SHIELD);
  }

  public function setEquipmentArrows($value)
  {
    $this->writeToSessionState(self::EQUIPMENT_ARROWS, $value);
  }

  public function getEquipmentArrows()
  {
    return $this->readFromSessionState(self::EQUIPMENT_ARROWS);
  }

  public function setEquipmentArmor($value)
  {
    $this->writeToSessionState(self::EQUIPMENT_ARMOR, $value);
  }

  public function getEquipmentArmor()
  {
    return $this->readFromSessionState(self::EQUIPMENT_ARMOR);
  }

  public function setEquipmentBow($value)
  {
    $this->writeToSessionState(self::EQUIPMENT_BOW, $value);
  }

  public function getEquipmentBow()
  {
    return $this->readFromSessionState(self::EQUIPMENT_BOW);
  }

  public function getPlayerDexterity()
  {
    return $this->readFromSessionState(self::STAT_DEX);
  }

  public function getPlayerIntelligence()
  {
    return $this->readFromSessionState(self::STAT_INT);
  }

  public function getPlayerStrength()
  {
    return $this->readFromSessionState(self::STAT_STR);
  }

  public function getPlayerConstitution()
  {
    return $this->readFromSessionState(self::STAT_CON);
  }

  public function setPlayerDexterity($value)
  {
    return $this->writeToSessionState(self::STAT_DEX, $value);
  }

  public function setPlayerIntelligence($value)
  {
    return $this->writeToSessionState(self::STAT_INT, $value);
  }

  public function setPlayerStrength($value)
  {
    return $this->writeToSessionState(self::STAT_STR, $value);
  }

  public function setPlayerConstitution($value)
  {
    return $this->writeToSessionState(self::STAT_CON, $value);
  }

  public function getHasOrb(): bool
  {
    return $this->readFromSessionState(self::INVENTORY_ORB);
  }

  public function setHasOrb(bool $hasOrb)
  {
    return $this->writeToSessionState(self::INVENTORY_ORB, $hasOrb);
  }

  public function getHasGrail(): bool
  {
    return $this->readFromSessionState(self::INVENTORY_GRAIL);
  }

  public function setHasGrail(bool $hasGrail)
  {
    return $this->writeToSessionState(self::INVENTORY_GRAIL, $hasGrail);
  }

  private function readFromSessionState($key)
  {
    return $_SESSION[$key];
  }

  private function writeToSessionState($key, $value)
  {
    $_SESSION[$key] = $value;
  }
}

?>