<?php
class StateService
{
  const PLAYER_NAME = "PLAYER_NAME";
  const STAT_DEX = "PLAYER_STAT_DEX";
  const STAT_INT = "PLAYER_STAT_INT";
  const STAT_STR = "PLAYER_STAT_STR";
  const STAT_CON = "PLAYER_STAT_CON";
  const CHAT_LAST_MESSAGE_ID = "CHAT_LAST_MESSAGE_ID";
  const EQUIPMENT_SWORD = "EQUIPMENT_SWORD";
  const EQUIPMENT_SHIELD = "EQUIPMENT_SHIELD";
  const EQUIPMENT_ARROWS = "EQUIPMENT_ARROWS";
  const EQUIPMENT_ARMOR = "EQUIPMENT_ARMOR";
  const EQUIPMENT_BOW = "EQUIPMENT_BOW";
  const CHARACTER_STATS_MONEY = "CHARACTER_STATS_MONEY";
  const CHARACTER_STATS_WEIGHT = "CHARACTER_STATS_WEIGHT";
  const CHARACTER_SPELLS_ON = "CHARACTER_SPELLS_ON";
  const MAP_FULL = "MAP_FULL";
  const INVENTORY_ORB = "INVENTORY_ORB";
  const INVENTORY_GRAIL = "INVENTORY_GRAIL";
  const GAME_START_TIME = "GAME_START_TIME";
  const PREVIOUS_GAME_STATE_KEY = "GAME_STATE_PREVIOUS";

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

  public function getChatLastMessageId()
  {
    return $this->readFromSessionState(self::CHAT_LAST_MESSAGE_ID);
  }

  public function setChatLastMessageId($value)
  {
    $this->writeToSessionState(self::CHAT_LAST_MESSAGE_ID, $value);
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

  public function setPlayerName($name)
  {
    $this->writeToSessionState(self::PLAYER_NAME, $name);
  }

  public function getPlayerName()
  {
    return $this->readFromSessionState(self::PLAYER_NAME);
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