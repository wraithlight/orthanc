<?php
require_once("./app/core/request/get-request-method.php");
require_once("./app/core/request/request-method.enum.php");
require_once("./app/core/request/is-options-request.php");
require_once("./app/core/request/is-not-options-request.php");

require_once("./phpapi/api.php");

// TODO: Move these to `domain` folder. (domain/enum).
require_once("./app/enums/game-state.enum.php");
require_once("./app/enums/movement-direction.enum.php");

// TODO: Cleanup range start.

require_once("./app/file-persistence/io-service.php");
require_once("./app/file-persistence/chat-members.service.php");
require_once("./app/file-persistence/chat-messages.service.php");
require_once("./app/file-persistence/maze.service.php");
require_once("./app/file-persistence/hall-of-fame.service.php");
require_once("./app/file-persistence/swadoc.service.php");
require_once("./app/file-persistence/version.service.php");
require_once("./app/file-persistence/configuration.service.php");
require_once("./app/file-persistence/localization.service.php");

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

require_once("./app/header/header-names.enum.php");
require_once("./app/header/header-values.enum.php");
// TODO: Cleanup range end.

require_once("./app/dal-generated/headers/names/header-names.enum.php");
require_once("./app/dal-generated/headers/values/header-values-x-orthanc-device.enum.php");

require_once("./app/api-guard/is-header-valid.php");
require_once("./app/api-guard/get-header-value.php");

require_once("./app/domain/game-mode.enum.php");
require_once("./app/domain/locale.enum.php");

require_once("./app/guards/is-valid-guid.php");

require_once("./app/dto/hall-of-fame-item.dto.php");

require_once("./app/core/error-code.enum.php");
require_once("./app/core/create-api-responses.php");

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

require_once("./app/services/device.service.php");
require_once("./app/services/feedback-events.service.php");
require_once("./app/services/items.service.php");
require_once("./app/services/level.service.php");
require_once("./app/services/player-location.service.php");
require_once("./app/services/player.service.php");
require_once("./app/services/session.service.php");
require_once("./app/services/user-interactions.service.php");

require_once("./app/mappers/_base.mapper.php");
require_once("./app/mappers/hall-of-fame.mapper.php");

require_once("./app/managers/actions.manager.php");
require_once("./app/managers/chat.manager.php");
require_once("./app/managers/items-on-map.manager.php");
require_once("./app/managers/npcs-on-map.manager.php");
require_once("./app/managers/session.manager.php");
require_once("./app/managers/swadoc.manager.php");
require_once("./app/managers/hall-of-fame.manager.php");
require_once("./app/managers/configuration.manager.php");
require_once("./app/managers/localization.manager.php");

require_once("./app/dal-generated/dtos/response/get-poll-chat.model.php");
require_once("./app/dal-generated/dtos/response/post-send-chat.model.php");
require_once("./app/dal-generated/dtos/response/post-generate-character.model.php");
require_once("./app/dal-generated/dtos/response/get-configuration.model.php");
require_once("./app/dal-generated/dtos/response/post-game-action.model.php");
require_once("./app/dal-generated/dtos/response/get-hall-of-fame.model.php");
require_once("./app/dal-generated/dtos/response/get-localization-locale.model.php");
require_once("./app/dal-generated/dtos/response/post-login-guest.model.php");

require_once("./app/dal-generated/paths/post-login-guest.path.const.php");
require_once("./app/dal-generated/paths/post-generate-character.path.const.php");
require_once("./app/dal-generated/paths/post-start-game.path.const.php");
require_once("./app/dal-generated/paths/post-game-action.path.const.php");
require_once("./app/dal-generated/paths/get-hall-of-fame.path.const.php");
require_once("./app/dal-generated/paths/get-configuration.path.const.php");
require_once("./app/dal-generated/paths/get-localization-locale.path.const.php");
require_once("./app/dal-generated/paths/get-poll-chat.path.const.php");
require_once("./app/dal-generated/paths/post-send-chat.path.const.php");

require_once("./app/controllers/login.controller.php");
require_once("./app/controllers/character-creation.controller.php");
require_once("./app/controllers/game.controller.php");
require_once("./app/controllers/chat.controller.php");
require_once("./app/controllers/swadoc.controller.php");
require_once("./app/controllers/hall-of-fame.controller.php");
require_once("./app/controllers/configuration.controller.php");
require_once("./app/controllers/localization.controller.php");

$isSwadocEnabled = getenv("SWAGGER_ENABLED");

if (isNotOptionsRequest()) { 
  if (!isHeaderValid(
    getHeaderValue(HeaderName::Accept->value, ""),
    [HeaderValueAccept::ApplicationJson->value])
  ) {
    http_response_code(400);
    echo json_encode(createFailResponse(ErrorCode::ERROR_0400_H, "Invalid header (" .HeaderName::Accept->value . ")"));
    return;
  }

  if (!isHeaderValid(
    getHeaderValue(HeaderName::Device->value, ""),
      [
        HeaderValueDevice::Desktop->value,
        HeaderValueDevice::Mobile->value
      ]
    )
  ) {
    http_response_code(400);
    echo json_encode(createFailResponse(ErrorCode::ERROR_0400_H, "Invalid header (" .HeaderName::Device->value . ")"));
    return;
  }

  if (!isGuid(getHeaderValue(HeaderName::RequestId->value, ""))) {
    http_response_code(400);
    echo json_encode(createFailResponse(ErrorCode::ERROR_0400_H, "Invalid header (" .HeaderName::RequestId->value . ")"));
    return;
  }
}

$configManager = new ConfigurationManager();
header(HeaderName::OrthancPlafromVersion->value . ": " . $configManager->getConfiguration()->version);

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
$hallOfFameControllerFactory = function () {
  return new HallOfFameController();
};
$configurationControllerFactory = function() {
  return new ConfigurationController();
};
$localizationControllerFactory = function() {
  return new LocalizationController();
};

Wrapper::RegisterPath("POST", API_POST_LOGIN_GUEST_PATH, $loginControllerFactory, "loginGuest");
Wrapper::RegisterPath("POST", API_POST_GENERATE_CHARACTER_PATH, $characterCreationControllerFactory, "generate");
Wrapper::RegisterPath("POST", API_POST_START_GAME_PATH, $gameControllerFactory, "startGame");
Wrapper::RegisterPath("POST", API_POST_SEND_CHAT_PATH, $chatControllerFactory, "sendMessage");
Wrapper::RegisterPath("GET", API_GET_POLL_CHAT_PATH, $chatControllerFactory, "getMessages");
Wrapper::RegisterPath("POST", API_POST_GAME_ACTION_PATH, $gameControllerFactory, "onAction");
Wrapper::RegisterPath("GET", API_GET_HALL_OF_FAME_PATH, $hallOfFameControllerFactory, "getRecords");
Wrapper::RegisterPath("GET", API_GET_CONFIGURATION_PATH, $configurationControllerFactory, "getConfiguration");
Wrapper::RegisterPath("GET", API_GET_LOCALIZATION_LOCALE_PATH, $localizationControllerFactory, "getLocalization");
strtolower($isSwadocEnabled) === "true" && Wrapper::RegisterPath("GET", "/api/v1/swadoc", $swadocControllerFactory, "getSwadoc");

Wrapper::Listen(true);
