<?php

class LoginController
{
  private $sessionManager;

  public function __construct()
  {
    $this->sessionManager = new SessionManager();
  }

  public function loginGuest()
  {
    $this->sessionManager->createNewSession();
    $stateService = new StateService();

    $username = 'guest_' . roll_d10k();
    $stateService->setPlayerName($username);
    echo json_encode([
      "payload" => [
        'username' => $username
      ]
    ], JSON_PRETTY_PRINT);
  }
}
?>