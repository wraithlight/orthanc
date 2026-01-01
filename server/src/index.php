<?php
require_once("./phpapi/api.php");

require_once("./app/enums/game-state.enum.php");

require_once("./app/file-persistence/io-service.php");
require_once("./app/file-persistence/chat-members.service.php");
require_once("./app/file-persistence/chat-messages.service.php");
require_once("./app/file-persistence/maze.service.php");
require_once("./app/file-persistence/hall-of-fame.service.php");

require_once("./app/utils/guid.php");

require_once("./app/utils/factories/item/item.php");
require_once("./app/utils/factories/item/item.factory.php");
require_once("./app/utils/factories/npc/npc.php");
require_once("./app/utils/factories/npc/npc.factory.php");

require_once("./app/utils/math/divide.php");
require_once("./app/utils/dice/d6.php");
require_once("./app/utils/dice/d8.php");
require_once("./app/utils/dice/d150.php");
require_once("./app/utils/dice/d10_000.php");
require_once("./app/utils/state/state.service.php");
require_once("./app/utils/maze.php");


// TODO: Cleanup the imports above.
require_once("./app/models/state-item.model.php");
require_once("./app/models/state-location.model.php");

require_once("./app/states/_base.state.php");
require_once("./app/states/items.state.php");
require_once("./app/states/player-location.state.php");

require_once("./app/services/items.service.php");
require_once("./app/services/player-location.service.php");

require_once("./app/managers/actions.manager.php");
require_once("./app/managers/chat.manager.php");
require_once("./app/managers/items-on-map.manager.php");
require_once("./app/managers/npcs-on-map.manager.php");
require_once("./app/managers/session.manager.php");

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

Wrapper::RegisterPath("GET", "/api/v1/login/guest", $loginControllerFactory, "loginGuest");
Wrapper::RegisterPath("POST", "/api/v1/login/guest", $loginControllerFactory, "loginGuest");
Wrapper::RegisterPath("POST", "/api/v1/character-creation/generate", $characterCreationControllerFactory, "generate");
Wrapper::RegisterPath("POST", "/api/v1/game/start", $gameControllerFactory, "startGame");
Wrapper::RegisterPath("POST", "/api/v1/chat/send", $chatControllerFactory, "sendMessage");
Wrapper::RegisterPath("GET", "/api/v1/chat/poll", $chatControllerFactory, "getMessages");
Wrapper::RegisterPath("POST", "/api/v1/game/action", $gameControllerFactory, "onAction");
Wrapper::Listen(true, true);

?>