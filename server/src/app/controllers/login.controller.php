<?php

class LoginController
{
  private $_chatManager;
  private $_sessionManager;

  public function __construct()
  {
    $this->_chatManager = new ChatManager();
    $this->_sessionManager = new SessionManager();
  }

  public function loginGuest()
  {
    $id = $this->_sessionManager->createNewSession();
    $stateService = new StateService();

    $username = 'guest_' . roll_d10k();
    $stateService->setPlayerName($username);
    $this->_chatManager->addMember($id);
    echo json_encode([
      "payload" => [
        'username' => $username
      ]
    ], JSON_PRETTY_PRINT);
  }
}
