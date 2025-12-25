<?php

class LoginController
{
  public function loginGuest()
  {
    session_start();
    $stateService = new StateService();

    $username = 'guest_' . roll_d10k();
    $stateService->setPlayerName($username);
    echo json_encode([
      'username' => $username
    ], JSON_PRETTY_PRINT);
  }
}
?>