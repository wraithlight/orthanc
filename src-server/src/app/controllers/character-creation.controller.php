<?php

class CharacterCreationController
{
  private $sessionManager;

  public function __construct()
  {
    $this->sessionManager = new SessionManager();
  }

  public function generate()
  {
    $this->sessionManager->authenticate();

    $playerService = new PlayerService();
    $stateService = new StateService();

    $str = roll_d8() + roll_d8();
    $int = roll_d8() + roll_d8();
    $dex = roll_d8() + roll_d8();
    $con = roll_d8() + roll_d8();

    $hits = divide($dex, 3) + roll_d8();

    $stateService->setPlayerStrength($str);
    $stateService->setPlayerIntelligence($int);
    $stateService->setPlayerDexterity($dex);
    $stateService->setPlayerConstitution($con);
    $playerService->setMaxHits($hits);

    $result = new stdClass();
    $result->maxHits = $hits;
    $result->stats = new stdClass();
    $result->stats->str = $str;
    $result->stats->int = $int;
    $result->stats->dex = $dex;
    $result->stats->con = $con;

    echo json_encode(createSuccessResponse($result));
  }
}
