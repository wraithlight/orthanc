<?php

class LoginController
{
  public function loginGuest()
  {
    session_start([
      "use_strict_mode" => 1,
    ]);
    session_regenerate_id(true);

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