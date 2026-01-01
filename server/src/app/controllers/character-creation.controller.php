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
    $stateService->setPlayerMaxHits($hits);

    echo json_encode([
      "payload" => [
        "maxHits" => $hits,
        "stats" => [
          "str" => $str,
          "int" => $int,
          "dex" => $dex,
          "con" => $con
        ]
      ]
    ]);
  }
}
?>