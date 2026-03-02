<?php

use function PHPUnit\Framework\returnArgument;

class LoginController
{
  private $_chatManager;
  private $_configurationManager;
  private $_sessionManager;

  public function __construct()
  {
    $this->_chatManager = new ChatManager();
    $this->_configurationManager = new ConfigurationManager();
    $this->_sessionManager = new SessionManager();
  }

  public function loginGuest()
  {
    $id = $this->_sessionManager->createNewSession();
    $sessionService = new SessionService();

    $username = 'guest_' . roll_d10k();
    $gameMode = $this->getGameMode();
    $sessionService->setPlayerName($username);
    $sessionService->setGameMode($gameMode);
    $this->_chatManager->addMember($id);
    echo json_encode([
      "payload" => [
        'username' => $username
      ]
    ], JSON_PRETTY_PRINT);
  }

  private function getGameMode(): GameMode {
    $gameModeFromRequest = json_decode(file_get_contents('php://input'))->gameMode;

    if (GameMode::tryFrom($gameModeFromRequest)) {
      return GameMode::tryFrom($gameModeFromRequest);
    }
    $configuration = $this->_configurationManager->getConfiguration();

    $defaultGameMode = $configuration->featureStates['loginScreenModeScreenDefault']['value'];
    return GameMode::tryFrom($defaultGameMode);
  }
}
