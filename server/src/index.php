<?php


require_once("./phpapi/api.php");
require_once("./app/utils/math/divide.php");
require_once("./app/utils/dice/d6.php");
require_once("./app/utils/dice/d8.php");
require_once("./app/controllers/character-creation.controller.php");

use PhpApi2\PhpAPI2Wrapper as Wrapper;

$characterCreationControllerFactory = function() {
  return new CharacterCreationController();
};

Wrapper::RegisterPath("POST", "/api/v1/character-creation/generate", $characterCreationControllerFactory, "generate");
Wrapper::RegisterPath("GET", "/api/character-creation", $characterCreationControllerFactory, "generate");

Wrapper::Listen(true, true);

?>