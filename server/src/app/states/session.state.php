<?php

class SessionState extends BaseState {

  private const PLAYER_NAME = "SESSION__PLAYER_NAME";
  private const CHAT_LAST_MESSAGE_ID = "SESSION__CHAT_LAST_MESSAGE_ID";

  public function setPlayerName(string $name): string {
    $this->writeState(self::PLAYER_NAME, $name);
    return $this->readState(self::PLAYER_NAME);
  }

  public function getPlayerName(string $name): string {
    return $this->readState(self::PLAYER_NAME);
  }

  public function setLastChatMessageId(int $id): int {
    $this->writeState(self::CHAT_LAST_MESSAGE_ID, $id);
    return $this->readState(self::CHAT_LAST_MESSAGE_ID);
  }

  public function getLastChatMessageId(): int {
    return $this->readState(self::CHAT_LAST_MESSAGE_ID);
  }

}
