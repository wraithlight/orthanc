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
    $payload = $this->_hallOfFameManager->listHallOfFame($validGameMode);

    $result = new stdClass();
    $result->items = $payload;

    echo json_encode(createSuccessResponse($result));
  }

}
