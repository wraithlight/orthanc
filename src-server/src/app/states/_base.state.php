<?php

abstract class BaseState {

  protected function writeState(
    string $key,
    $value
  ) {
    $_SESSION[$key] = $value;
    return $this->readStateCore($key);
  }

  protected function readState(
    $key
  ) {
    return $this->readStateCore($key);
  }

  private function readStateCore($key) {
    return $_SESSION[$key];
  }

}
