<?php
class StateService
{

  const POSITION_KEY_X = "PLAYER_POSITION_X";
  const POSITION_KEY_Y = "PLAYER_POSITION_Y";
  const PLAYER_NAME = "PLAYER_NAME";
  const STAT_DEX = "PLAYER_STAT_DEX";
  const STAT_INT = "PLAYER_STAT_INT";
  const STAT_STR = "PLAYER_STAT_STR";
  const STAT_CON = "PLAYER_STAT_CON";
  const HITS_CUR = "PLAYER_HITS_CUR";
  const HITS_MAX = "PLAYER_HITS_MAX";
  const CHAT_LAST_MESSAGE_ID = "CHAT_LAST_MESSAGE_ID";
  const EQUIPMENT_SWORD = "EQUIPMENT_SWORD";
  const EQUIPMENT_SHIELD = "EQUIPMENT_SHIELD";
  const EQUIPMENT_ARROWS = "EQUIPMENT_ARROWS";
  const EQUIPMENT_ARMOR = "EQUIPMENT_ARMOR";
  const CHARACTER_STATS_EXPERIENCE = "CHARACTER_STATS_EXPERIENCE";
  const CHARACTER_STATS_EXPERIENCE_FROM_KILLS = "CHARACTER_STATS_EXPERIENCE_FROM_KILLS";
  const CHARACTER_STATS_MONEY = "CHARACTER_STATS_MONEY";
  const CHARACTER_STATS_WEIGHT = "CHARACTER_STATS_WEIGHT";
  const CHARACTER_STATS_SPELL_UNITS_MAX = "CHARACTER_STATS_SPELL_UNITS_MAX";
  const CHARACTER_STATS_SPELL_UNITS_CUR = "CHARACTER_STATS_SPELL_UNITS_CUR";
  const CHARACTER_SPELLS_ON = "CHARACTER_SPELLS_ON";
  const MAP_ITEMS = "MAP_ITEMS";
  const MAP_NPCS = "MAP_NPCS";
  const MAP_FULL = "MAP_FULL";
  const INVENTORY_ORB = "INVENTORY_ORB";
  const GAME_START_TIME = "GAME_START_TIME";
  const PREVIOUS_POSITION_X = "PREVIOUS_POSITION_X";
  const PREVIOUS_POSITION_Y = "PREVIOUS_POSITION_Y";

  public function getPlayerPreviousPosition(): array
  {
    $x = $this->readFromSessionState(self::PREVIOUS_POSITION_X);
    $y = $this->readFromSessionState(self::PREVIOUS_POSITION_Y);
    return [
      "x" => $x,
      "y" => $y
    ];
  }

  public function setPlayerPreviousPosition($x, $y)
  {
    $this->writeToSessionState(self::PREVIOUS_POSITION_X, $x);
    $this->writeToSessionState(self::PREVIOUS_POSITION_Y, $y);
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

  public function setCharacterXp($value)
  {
    $this->writeToSessionState(self::CHARACTER_STATS_EXPERIENCE, $value);
  }

  public function setCharacterXpFromKills($value)
  {
    $this->writeToSessionState(self::CHARACTER_STATS_EXPERIENCE_FROM_KILLS, $value);
  }

  public function setCharacterStatsMoney($value)
  {
    $this->writeToSessionState(self::CHARACTER_STATS_MONEY, $value);
  }

  public function setCharacterStatsWeight($value)
  {
    $this->writeToSessionState(self::CHARACTER_STATS_WEIGHT, $value);
  }

  public function setCharacterSpellUnitsMax($value)
  {
    $this->writeToSessionState(self::CHARACTER_STATS_SPELL_UNITS_MAX, $value);
  }

  public function setCharacterSpellUnitsCur($value)
  {
    $this->writeToSessionState(self::CHARACTER_STATS_SPELL_UNITS_CUR, $value);
  }

  public function setCharacterSpellsOn($value)
  {
    $this->writeToSessionState(self::CHARACTER_SPELLS_ON, $value);
  }

  public function getCharacterXp()
  {
    return $this->readFromSessionState(self::CHARACTER_STATS_EXPERIENCE);
  }

  public function getCharacterXpFromKills()
  {
    return $this->readFromSessionState(self::CHARACTER_STATS_EXPERIENCE_FROM_KILLS);
  }

  public function getCharacterStatsMoney()
  {
    return $this->readFromSessionState(self::CHARACTER_STATS_MONEY);
  }

  public function getCharacterStatsWeight()
  {
    return $this->readFromSessionState(self::CHARACTER_STATS_WEIGHT);
  }

  public function getCharacterSpellUnitsMax()
  {
    return $this->readFromSessionState(self::CHARACTER_STATS_SPELL_UNITS_MAX);
  }

  public function getCharacterSpellUnitsCur()
  {
    return $this->readFromSessionState(self::CHARACTER_STATS_SPELL_UNITS_CUR);
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

  public function getPlayerMaxHits()
  {
    return $this->readFromSessionState(self::HITS_MAX);
  }

  public function getPlayerCurHits()
  {
    return $this->readFromSessionState(self::HITS_CUR);
  }

  public function getChatLastMessageId()
  {
    return $this->readFromSessionState(self::CHAT_LAST_MESSAGE_ID);
  }

  public function setPlayerMaxHits($value)
  {
    $this->writeToSessionState(self::HITS_MAX, $value);
  }

  public function setPlayerCurHits($value)
  {
    $this->writeToSessionState(self::HITS_CUR, $value);
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

  public function moveNorth()
  {
    $positionX = $this->readFromSessionState(self::POSITION_KEY_Y) - 1;
    $this->writeToSessionState(self::POSITION_KEY_Y, $positionX);
  }

  public function moveEast()
  {
    $positionY = $this->readFromSessionState(self::POSITION_KEY_X) + 1;
    $this->writeToSessionState(self::POSITION_KEY_X, $positionY);
  }

  public function moveSouth()
  {
    $positionX = $this->readFromSessionState(self::POSITION_KEY_Y) + 1;
    $this->writeToSessionState(self::POSITION_KEY_Y, $positionX);
  }

  public function moveWest()
  {
    $positionY = $this->readFromSessionState(self::POSITION_KEY_X) - 1;
    $this->writeToSessionState(self::POSITION_KEY_X, $positionY);
  }

  public function getHasOrb(): bool
  {
    return $this->readFromSessionState(self::INVENTORY_ORB);
  }

  public function setHasOrb(bool $hasOrb)
  {
    return $this->writeToSessionState(self::INVENTORY_ORB, $hasOrb);
  }

  /**
   * @return Item[]
   */
  public function getItems(): array
  {
    return $this->readFromSessionState(self::MAP_ITEMS);
  }

  /**
   * @param Item[] $items
   */
  public function setItems(array $items)
  {
    return $this->writeToSessionState(self::MAP_ITEMS, $items);
  }

  public function getNpcs(): array
  {
    return $this->readFromSessionState(self::MAP_NPCS);
  }

  public function setNpcs(array $npcs)
  {
    return $this->writeToSessionState(self::MAP_NPCS, $npcs);
  }

  public function getPlayerPosition(): array
  {
    $x = $this->readFromSessionState(self::POSITION_KEY_X);
    $y = $this->readFromSessionState(self::POSITION_KEY_Y);
    return [
      "x" => $x,
      "y" => $y
    ];
  }

  public function setPlayerPosition($x, $y)
  {
    $this->writeToSessionState(self::POSITION_KEY_X, $x);
    $this->writeToSessionState(self::POSITION_KEY_Y, $y);
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