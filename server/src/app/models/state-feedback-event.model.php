<?php

class State_FeedbackEvent
{
  public string $key;
  public string $label;

  public function __construct(
    int $key,
    int $label
  ) {
    $this->key = $key;
    $this->label = $label;
  }
}
