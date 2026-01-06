<?php

class UserInteractionsService {

  private $_userInteractionsState;

  public function __construct() {
    $this->_userInteractionsState = new UserInteractionsState();
  }

  public function reset(): void {
    $this->_userInteractionsState->setMoves(0);
  }

  public function incrementMoves(): void {
    $this->_userInteractionsState->setMoves($this->_userInteractionsState->getMoves() + 1);
  }

  public function getMoves(): int {
    return $this->_userInteractionsState->getMoves();
  }

}
