<?php

class SessionService {

  private $_sessionState;

  public function __construct() {
    $this->_sessionState = new SessionState();
  }

  public function setPlayerName(string $name): string {
    return $this->_sessionState->setPlayerName($name);
  }
  public function getPlayerName(): string {
    return $this->_sessionState->getPlayerName();
  }
  public function setLastChatMessageId(int $id): int {
    return $this->_sessionState->setLastChatMessageId($id);
  }
  public function getLastChatMessageId(): int {
    return $this->_sessionState->getLastChatMessageId();
  }

}
