<?php

class HallOfFameController
{

  private $_hallOfFameManager;

  public function __construct()
  {
    $this->_hallOfFameManager = new HallOfFameManager();
  }


  public function getRecords(string $gameMode)
  {
    $validGameMode = GameMode::tryFrom($gameMode) ?? GameMode::Vanilla;
    $result = $this->_hallOfFameManager->listHallOfFame($validGameMode);

    echo json_encode($result, JSON_UNESCAPED_UNICODE);
  }

}
