<?php
require_once("./phpapi/api.php");

require_once("./app/enums/game-state.enum.php");
require_once("./app/enums/movement-direction.enum.php");

// TODO: Cleanup range start.

require_once("./app/file-persistence/io-service.php");
require_once("./app/file-persistence/chat-members.service.php");
require_once("./app/file-persistence/chat-messages.service.php");
require_once("./app/file-persistence/maze.service.php");
require_once("./app/file-persistence/hall-of-fame.service.php");
require_once("./app/file-persistence/config.service.php");
require_once("./app/file-persistence/swadoc.service.php");

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

// TODO: Cleanup range end.

require_once("./app/constants/action-run-fail.const.php");
require_once("./app/constants/action-run-success.const.php");

require_once("./app/models/state-feedback-event.model.php");
require_once("./app/models/state-item.model.php");
require_once("./app/models/state-location.model.php");

require_once("./app/states/_base.state.php");
require_once("./app/states/feedback-events.state.php");
require_once("./app/states/items.state.php");
require_once("./app/states/level.state.php");
require_once("./app/states/player-location.state.php");
require_once("./app/states/player.state.php");
require_once("./app/states/session.state.php");
require_once("./app/states/user-interactions.state.php");

require_once("./app/services/feedback-events.service.php");
require_once("./app/services/items.service.php");
require_once("./app/services/level.service.php");
require_once("./app/services/player-location.service.php");
require_once("./app/services/player.service.php");
require_once("./app/services/session.service.php");
require_once("./app/services/user-interactions.service.php");

require_once("./app/managers/actions.manager.php");
require_once("./app/managers/chat.manager.php");
require_once("./app/managers/items-on-map.manager.php");
require_once("./app/managers/npcs-on-map.manager.php");
require_once("./app/managers/session.manager.php");
require_once("./app/managers/swadoc.manager.php");

require_once("./app/controllers/login.controller.php");
require_once("./app/controllers/character-creation.controller.php");
require_once("./app/controllers/game.controller.php");
require_once("./app/controllers/chat.controller.php");
require_once("./app/controllers/swadoc.controller.php");

$isSwadocEnabled = getenv("SWAGGER_ENABLED");

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
$swadocControllerFactory = function () {
  return new SwadocController();
};

Wrapper::RegisterPath("GET", "/api/v1/login/guest", $loginControllerFactory, "loginGuest");
Wrapper::RegisterPath("POST", "/api/v1/login/guest", $loginControllerFactory, "loginGuest");
Wrapper::RegisterPath("POST", "/api/v1/character-creation/generate", $characterCreationControllerFactory, "generate");
Wrapper::RegisterPath("POST", "/api/v1/game/start", $gameControllerFactory, "startGame");
Wrapper::RegisterPath("POST", "/api/v1/chat/send", $chatControllerFactory, "sendMessage");
Wrapper::RegisterPath("GET", "/api/v1/chat/poll", $chatControllerFactory, "getMessages");
Wrapper::RegisterPath("POST", "/api/v1/game/action", $gameControllerFactory, "onAction");
strtolower($isSwadocEnabled) === "true" && Wrapper::RegisterPath("GET", "/api/v1/swadoc", $swadocControllerFactory, "getSwadoc");

Wrapper::Listen(true);
