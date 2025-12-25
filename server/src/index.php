<?php
require_once("./phpapi/api.php");
require_once("./app/utils/math/divide.php");
require_once("./app/utils/dice/d6.php");
require_once("./app/utils/dice/d8.php");
require_once("./app/utils/dice/d10_000.php");
require_once("./app/utils/state/state.service.php");
require_once("./app/file-persistence/io-service.php");
require_once("./app/file-persistence/chat-members.service.php");
require_once("./app/file-persistence/chat-messages.service.php");
require_once("./app/file-persistence/maze.service.php");
require_once("./app/utils/maze.php");
require_once("./app/controllers/login.controller.php");
require_once("./app/controllers/character-creation.controller.php");
require_once("./app/controllers/game.controller.php");
require_once("./app/controllers/chat.controller.php");

use PhpApi2\PhpAPI2Wrapper as Wrapper;

$loginControllerFactory = function () {
  return new LoginController();
};
$characterCreationControllerFactory = function () {
  return new CharacterCreationController();
};
$gameControllerFactory = function () {
  return new GameController();
};
$chatControllerFactory = function () {
  return new ChatController();
};

Wrapper::RegisterPath("POST", "/api/v1/login/guest", $loginControllerFactory, "loginGuest");
Wrapper::RegisterPath("POST", "/api/v1/character-creation/generate", $characterCreationControllerFactory, "generate");
Wrapper::RegisterPath("GET", "/api/v1/game/start", $gameControllerFactory, "startGame");
Wrapper::RegisterPath("POST", "/api/v1/chat/send", $chatControllerFactory, "sendMessage");
Wrapper::RegisterPath("GET", "/api/v1/chat/poll", $chatControllerFactory, "getMessages");
Wrapper::RegisterPath("POST", "/api/v1/game/action", $gameControllerFactory, "onAction");
// TODO: Remove GET
Wrapper::RegisterPath("GET", "/api/v1/game/action", $gameControllerFactory, "onAction");

Wrapper::Listen(true, true);

?>