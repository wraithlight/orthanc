<?php

class HallOfFameController
{

  private $_hallOfFameManager;

  public function __construct()
  {
    $this->_hallOfFameManager = new HallOfFameManager();
  }


  public function getRecords()
  {
    $result = $this->_hallOfFameManager->listHallOfFame();

    echo json_encode($result, JSON_UNESCAPED_UNICODE);
  }
}
