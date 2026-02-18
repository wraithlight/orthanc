<?php

class UserInteractionsState extends BaseState
{

  private const NUMBER_OF_ACTIONS_KEY = "USER_INTERACTIONS__NUMBER_OF_ACTIONS";
  private const NUMBER_OF_MOVES_KEY = "USER_INTERACTIONS__NUMBER_OF_MOVES";

  public function setMoves(int $num): int
  {
    $this->writeState(self::NUMBER_OF_MOVES_KEY, $num);
    return $this->readState(self::NUMBER_OF_MOVES_KEY);
  }

  public function getMoves(): int
  {
    return $this->readState(self::NUMBER_OF_MOVES_KEY);
  }

  public function setActions(int $num): int
  {
    $this->writeState(self::NUMBER_OF_ACTIONS_KEY, $num);
    return $this->readState(self::NUMBER_OF_ACTIONS_KEY);
  }

  public function getActions(): int
  {
    return $this->readState(self::NUMBER_OF_ACTIONS_KEY);
  }

}
