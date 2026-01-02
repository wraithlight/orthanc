<?php

class State_FeedbackEvent
{
  public string $key;
  public string $label;

  public function __construct(
    string $key,
    string $label
  ) {
    $this->key = $key;
    $this->label = $label;
  }
}
