<?php

class PlayerLocationState extends BaseState {

  private const LOCATION_CURRENT_X_KEY = "PLAYER_LOCATION__LOCATION_CURRENT_X_KEY";
  private const LOCATION_CURRENT_Y_KEY = "PLAYER_LOCATION__LOCATION_CURRENT_Y_KEY";
  private const LOCATION_INITIAL_X_KEY = "PLAYER_LOCATION__LOCATION_INITIAL_X_KEY";
  private const LOCATION_INITIAL_Y_KEY = "PLAYER_LOCATION__LOCATION_INITIAL_Y_KEY";
  private const LOCATION_PREVIOUS_X_KEY = "PLAYER_LOCATION__LOCATION_PREVOIUS_X_KEY";
  private const LOCATION_PREVIOUS_Y_KEY = "PLAYER_LOCATION__LOCATION_PREVOIUS_Y_KEY";

  /**
   * @param State_Location $location
   * @return State_Location
   */
  public function setPlayerCurrentLocation(
    object $location
  ): object {
    $this->writeState(self::LOCATION_CURRENT_X_KEY, $location->coordX);
    $this->writeState(self::LOCATION_CURRENT_Y_KEY, $location->coordY);
    return $this->getPlayerCurrentLocationCore();
  }

  /**
   * @return State_Location
   */
  public function getPlayerCurrentLocation(): object
  {
    return $this->getPlayerCurrentLocationCore();
  }

  /**
   * @param State_Location $location
   * @return State_Location
   */
  public function setPlayerInitialLocation(
    object $location
  ): object {
    $this->writeState(self::LOCATION_INITIAL_X_KEY, $location->coordX);
    $this->writeState(self::LOCATION_INITIAL_Y_KEY, $location->coordY);
    return $this->getPlayerCurrentLocationCore();
  }

  /**
   * @return State_Location
   */
  public function getPlayerInitialLocation(): object
  {
    return $this->getPlayerInitialLocationCore();
  }

  /**
   * @param State_Location $location
   * @return State_Location
   */
  public function setPlayerPreviousLocation(
    object $location
  ): object {
    $this->writeState(self::LOCATION_PREVIOUS_X_KEY, $location->coordX);
    $this->writeState(self::LOCATION_PREVIOUS_Y_KEY, $location->coordY);
    return $this->getPlayerPreviousLocationCore();
  }

  /**
   * @return State_Location
   */
  public function getPlayerPreviousLocation(): object
  {
    return $this->getPlayerPreviousLocationCore();
  }

  /**
   * @return State_Location
   */
  public function getPlayerCurrentLocationCore(): object
  {
    return new State_Location(
      $this->readState(self::LOCATION_CURRENT_X_KEY),
      $this->readState(self::LOCATION_CURRENT_Y_KEY),
    );
  }

  /**
   * @return State_Location
   */
  public function getPlayerInitialLocationCore(): object
  {
    return new State_Location(
      $this->readState(self::LOCATION_INITIAL_X_KEY),
      $this->readState(self::LOCATION_INITIAL_Y_KEY),
    );
  }

  /**
   * @return State_Location
   */
  public function getPlayerPreviousLocationCore(): object
  {
    return new State_Location(
      $this->readState(self::LOCATION_PREVIOUS_X_KEY),
      $this->readState(self::LOCATION_PREVIOUS_Y_KEY),
    );
  }

}
