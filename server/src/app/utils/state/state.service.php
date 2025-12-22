<?php
class StateService {

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

  // const $EQUIPMENT_SWORD = "EQUIPMENT_SWORD";
  // const $EQUIPMENT_SHIELD = "EQUIPMENT_SHIELD";
  // const $EQUIPMENT_ARROWS = "EQUIPMENT_ARROWS";
  // const $EQUIPMENT_ARMOR = "EQUIPMENT_ARMOR";
  // const $CHARACTER_STATS_EXPERIENCE = "CHARACTER_STATS_EXPERIENCE";
  // const $CHARACTER_STATS_EXPERIENCE_FROM_KILLS = "CHARACTER_STATS_EXPERIENCE_FROM_KILLS";
  // const $CHARACTER_STATS_MONEY = "CHARACTER_STATS_MONEY";
  // const $CHARACTER_STATS_LEVEL = "CHARACTER_STATS_LEVEL";
  // const $CHARACTER_STATS_WEIGHT = "CHARACTER_STATS_WEIGHT";
  // const $CHARACTER_STATS_SPELL_UNITS_MAX = "CHARACTER_STATS_SPELL_UNITS_MAX";
  // const $CHARACTER_STATS_SPELL_UNITS_CUR = "CHARACTER_STATS_SPELL_UNITS_CUR";
  // const $CHARACTER_SPELLS_ON = "CHARACTER_SPELLS_ON";

  public function getPlayerMaxHits() {
    return $this->readFromSessionState(self::HITS_MAX);
  }

  public function getPlayerCurHits() {
    return $this->readFromSessionState(self::HITS_CUR);
  }

  public function getChatLastMessageId() {
    return $this->readFromSessionState(self::CHAT_LAST_MESSAGE_ID);
  }

  public function setPlayerMaxHits($value) {
    $this->writeToSessionState(self::HITS_MAX, $value);
  }

  public function setPlayerCurHits($value) {
    $this->writeToSessionState(self::HITS_CUR, $value);
  }

  public function setChatLastMessageId($value) {
    $this->writeToSessionState(self::CHAT_LAST_MESSAGE_ID, $value);
  }

  public function getPlayerDexterity() {
    return $this->readFromSessionState(self::STAT_DEX);
  }

  public function getPlayerIntelligence() {
    return $this->readFromSessionState(self::STAT_INT);
  }

  public function getPlayerStrength() {
    return $this->readFromSessionState(self::STAT_STR);
  }

  public function getPlayerConstitution() {
    return $this->readFromSessionState(self::STAT_CON);
  }

  public function setPlayerDexterity($value) {
    return $this->writeToSessionState(self::STAT_DEX, $value);
  }

  public function setPlayerIntelligence($value) {
    return $this->writeToSessionState(self::STAT_INT, $value);
  }

  public function setPlayerStrength($value) {
    return $this->writeToSessionState(self::STAT_STR, $value);
  }

  public function setPlayerConstitution($value) {
    return $this->writeToSessionState(self::STAT_CON, $value);
  }

  public function setPlayerName($name) {
    $this->writeToSessionState(self::PLAYER_NAME, $name);
  }

  public function getPlayerName() {
    return $this->readFromSessionState(self::PLAYER_NAME);
  }

  public function moveNorth() {
    $positionX = $this->readFromSessionState(self::POSITION_KEY_X) + 1;
    $this->writeToSessionState(self::POSITION_KEY_X, $positionX);
  }

  public function moveEast() {
    $positionY = $this->readFromSessionState(self::POSITION_KEY_Y) + 1;
    $this->writeToSessionState(self::POSITION_KEY_Y, $positionY);
  }

  public function moveSouth() {
    $positionX = $this->readFromSessionState(self::POSITION_KEY_X) - 1;
    $this->writeToSessionState(self::POSITION_KEY_X, $positionX);
  }

  public function moveWest() {
    $positionY = $this->readFromSessionState(self::POSITION_KEY_Y) - 1;
    $this->writeToSessionState(self::POSITION_KEY_Y, $positionY);
  }

  public function setPlayerPosition($x, $y) {
    $this->writeToSessionState(self::POSITION_KEY_X, $x);
    $this->writeToSessionState(self::POSITION_KEY_Y, $y);
  }

  private function readFromSessionState($key) {
    return $_SESSION[$key];
  }

  private function writeToSessionState($key, $value) {
    $_SESSION[$key] = $value;
  }
}

?>
