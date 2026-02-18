<?php

class UserInteractionsService {

  private $_userInteractionsState;

  public function __construct() {
    $this->_userInteractionsState = new UserInteractionsState();
  }

  public function reset(): void {
    $this->_userInteractionsState->setMoves(0);
    $this->_userInteractionsState->setActions(0);
  }

  public function incrementMoves(): void {
    $this->_userInteractionsState->setMoves($this->_userInteractionsState->getMoves() + 1);
  }

  public function getMoves(): int {
    return $this->_userInteractionsState->getMoves();
  }

  public function incrementActions(): void {
    $this->_userInteractionsState->setActions($this->_userInteractionsState->getActions() + 1);
  }

  public function getActions(): int {
    return $this->_userInteractionsState->getActions();
  }

}
