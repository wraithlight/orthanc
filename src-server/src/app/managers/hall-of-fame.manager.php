<?php

class HallOfFameManager
{

  private $_hallOfFameService;

  public function __construct()
  {
    $this->_hallOfFameService = new HallOfFameService();
  }

  public function listHallOfFame(): array {
    return $this->_hallOfFameService->list(10);
  }

}
