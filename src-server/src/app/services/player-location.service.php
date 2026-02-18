<?php

class PlayerLocationService {

  private $_playerLocationState;

  public function __construct()
  {
    $this->_playerLocationState = new PlayerLocationState();
  }

  /**
   * @return State_Location
   */
  public function getPlayerInitialLocation() {
    return $this->_playerLocationState->getPlayerInitialLocation();
  }

  /**
   * @param int $x
   * @param int $y
   * @return State_Location
   */
  public function setPlayerInitialLocation(int $x, int $y) {
    return $this->_playerLocationState->setPlayerInitialLocation(new State_Location($x, $y));
  }

  /**
   * @return State_Location
   */
  public function getPlayerCurrentLocation(): object {
    return $this->_playerLocationState->getPlayerCurrentLocation();
  }

  /**
   * @param int $x
   * @param int $y
   * @return State_Location
   */
  public function setPlayerCurrentLocation(int $x, int $y): object {
    return $this->_playerLocationState->setPlayerCurrentLocation(new State_Location($x, $y));
  }

  /**
   * @return State_Location
   */
  public function getPlayerPreviousLocation(): object {
    return $this->_playerLocationState->getPlayerPreviousLocation();
  }

  /**
   * @param int $x
   * @param int $y
   * @return State_Location
   */
  public function setPlayerPreviousLocation(int $x, int $y) {
    return $this->_playerLocationState->setPlayerPreviousLocation(new State_Location($x, $y));
  }

  /**
   * @return void
   */
  public function movePlayerNorth() {
    $location = $this->_playerLocationState->getPlayerCurrentLocation();
    $this->_playerLocationState->setPlayerCurrentLocation(new State_Location($location->coordX, $location->coordY - 1));
    $this->_playerLocationState->setPlayerPreviousLocation(new State_Location($location->coordX, $location->coordY));
  }

  /**
   * @return void
   */
  public function movePlayerEast() {
    $location = $this->_playerLocationState->getPlayerCurrentLocation();
    $this->_playerLocationState->setPlayerCurrentLocation(new State_Location($location->coordX + 1, $location->coordY));
    $this->_playerLocationState->setPlayerPreviousLocation(new State_Location($location->coordX, $location->coordY));
  }

  /**
   * @return void
   */
  public function movePlayerSouth() {
    $location = $this->_playerLocationState->getPlayerCurrentLocation();
    $this->_playerLocationState->setPlayerCurrentLocation(new State_Location($location->coordX, $location->coordY + 1));
    $this->_playerLocationState->setPlayerPreviousLocation(new State_Location($location->coordX, $location->coordY));
  }

  /**
   * @return void
   */
  public function movePlayerWest() {
    $location = $this->_playerLocationState->getPlayerCurrentLocation();
    $this->_playerLocationState->setPlayerCurrentLocation(new State_Location($location->coordX - 1, $location->coordY));
    $this->_playerLocationState->setPlayerPreviousLocation(new State_Location($location->coordX, $location->coordY));
  }

  /**
   * @return bool
   */
  public function isAtInitialLocation(): bool {
    $currentLocation = $this->_playerLocationState->getPlayerCurrentLocation();
    $initialLocation = $this->_playerLocationState->getPlayerInitialLocation();

    $sameX = $currentLocation->coordX === $initialLocation->coordX;
    $sameY = $currentLocation->coordY === $initialLocation->coordY;

    return $sameX && $sameY;
  }

  /**
   * @return void
   */
  public function moveToPreviousPosition(): void {
    $currentLocation = $this->_playerLocationState->getPlayerCurrentLocation();
    $previousLocation = $this->_playerLocationState->getPlayerInitialLocation();

    $this->_playerLocationState->setPlayerPreviousLocation($currentLocation);
    $this->_playerLocationState->setPlayerCurrentLocation($previousLocation);
  }

  /**
   * @return bool
   */
  public function isPreviousAndCurrentSame(): bool {
    $currentLocation = $this->_playerLocationState->getPlayerCurrentLocation();
    $previousLocation = $this->_playerLocationState->getPlayerPreviousLocation();
    return $currentLocation === $previousLocation;
  }

}
