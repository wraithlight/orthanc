<?php

class State_Location
{
  public int $coordX;
  public int $coordY;

  public function __construct(
    int $x,
    int $y
  ) {
    $this->coordX = $x;
    $this->coordY = $y;
  }
}
