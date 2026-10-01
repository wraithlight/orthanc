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

    $stats = new PostGenerateCharacterResponsePayloadStats();
    $stats->str = $str;
    $stats->int = $int;
    $stats->dex = $dex;
    $stats->con = $con;

    $result = new PostGenerateCharacterResponsePayload();
    $result->maxHits = $hits;
    $result->stats = $stats;

    echo json_encode(createSuccessResponse($result));
  }
}
