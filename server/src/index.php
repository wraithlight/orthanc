<?php
session_start();

require_once("./phpapi/api.php");
require_once("./app/utils/math/divide.php");
require_once("./app/utils/dice/d6.php");
require_once("./app/utils/dice/d8.php");
require_once("./app/utils/dice/d10_000.php");
require_once("./app/controllers/login.controller.php");
require_once("./app/controllers/character-creation.controller.php");
require_once("./app/controllers/game.controller.php");

use PhpApi2\PhpAPI2Wrapper as Wrapper;

$loginControllerFactory = function() {
  return new LoginController();
};
$characterCreationControllerFactory = function() {
  return new CharacterCreationController();
};
$gameControllerFactory = function() {
  return new GameController();
};

Wrapper::RegisterPath("POST", "/api/v1/login/guest", $loginControllerFactory, "loginGuest");
Wrapper::RegisterPath("POST", "/api/v1/character-creation/generate", $characterCreationControllerFactory, "generate");
Wrapper::RegisterPath("GET", "/api/v1/game/start", $gameControllerFactory, "startGame");

Wrapper::Listen(true, true);

?>
