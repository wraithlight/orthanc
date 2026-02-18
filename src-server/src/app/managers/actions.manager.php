<?php

class ActionsManager
{

  private $_feedbackEventsService;
  private $_itemsService;
  private $_playerLocationService;
  private $_playerService;
  private $_stateService;
  private $_userInteractionsService;

  public function __construct()
  {
    $this->_feedbackEventsService = new FeedbackEventsService();
    $this->_itemsService = new ItemsService();
    $this->_playerLocationService = new PlayerLocationService();
    $this->_playerService = new PlayerService();
    $this->_stateService = new StateService();
    $this->_userInteractionsService = new UserInteractionsService();
  }

  public function handleAction(
    string $action,
    string $target
  ): void {
    switch ($action) {
      case "MOVE": {
        $enum = MovementDirection::tryFrom($target);
        $enum && $this->handleMovement($enum);
        break;
      }
      case "RUN": {
        $this->handleRun();
        break;
      }
      case "PICKUP": {
        $this->handlePickup($target);
        break;
      }
      case "INITIAL_IN_GAME": {
        break;
      }
      default: {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode([
          'errorCode' => 'ERROR_0001',
        ]);
      exit;
      }
    }
    if ($action !== "MOVE") {
      $this->_userInteractionsService->incrementActions();
    }
  }

  public function getPossibleActions(
    $tiles,
    GameState $gameState
  ): array {
    $actions = [];
    if ($gameState !== GameState::Running) {
      return $actions;
    }
    $playerTile = $tiles[(int) floor(count($tiles) / 2)];
    $visibleNpcs = array_values(array_filter(array_map(fn($m) => $m['occupiedBy'], $tiles), fn($m) => isset($m) && $m->key !== "CHARACTER"));

    $canFight = count($visibleNpcs) === 1;
    $canRun = $canFight && $this->_playerLocationService->isPreviousAndCurrentSame();
    $canMove = count($visibleNpcs) === 0;
    $canCast = $this->_playerService->hasSpellUnits();
    $canPickup = !empty($playerTile["containsItems"]);
    $canRetire = $gameState === GameState::Running;

    $canMoveNorth = $playerTile["top"] === "TILE_OPEN";
    $canMoveEast = $playerTile["right"] === "TILE_OPEN";
    $canMoveSouth = $playerTile["bottom"] === "TILE_OPEN";
    $canMoveWest = $playerTile["left"] === "TILE_OPEN";

    // TODO: The NPCs cannot be next to each other!
    if (count($visibleNpcs) === 1) {
      $canMove = false;
      $canFight = true;
    }

    if ($canPickup) {
      foreach ($playerTile["containsItems"] as $item) {
        if ($item->canPickup) {
          array_push($actions, [
            "label" => $item->pickupLabel,
            "key" => "PICKUP",
            "payload" => $item->id
          ]);
        }
      }
    }

    $canRun && array_push($actions, ["label" => "[R]un", "key" => "RUN", "payload" => null]);
    $canFight && array_push($actions, ["label" => "[F]ight", "key" => "FIGHT", "payload" => null]);
    $canMove && $canMoveNorth && array_push($actions, ["label" => "[↑] North", "key" => "MOVE", "payload" => MovementDirection::North->value]);
    $canMove && $canMoveEast && array_push($actions, ["label" => "[→] East", "key" => "MOVE", "payload" => MovementDirection::East->value]);
    $canMove && $canMoveSouth && array_push($actions, ["label" => "[↓] South", "key" => "MOVE", "payload" => MovementDirection::South->value]);
    $canMove && $canMoveWest && array_push($actions, ["label" => "[←] West", "key" => "MOVE", "payload" => MovementDirection::West->value]);
    $canCast && array_push($actions, ["label" => "[C]ast a spell", "key" => "CAST_SPELL", "payload" => null]);
    $canRetire && array_push($actions, ["label" => "[R]etire", "key" => "EVENT_RETIRE", "payload" => null]);

    return $actions;
  }

  public function canDoAction(
    array $actionsArray,
    string $action,
    $target = null
  ): bool {
    foreach ($actionsArray as $element) {
      if ($element['key'] === $action) {
        if ($target === null) {
          return true;
        }
        if ($element['payload'] === $target) {
          return true;
        }
      }
    }
    return false;
  }

  private function handleMovement(MovementDirection $target): void
  {
    match ($target) {
      MovementDirection::North => $this->_playerLocationService->movePlayerNorth(),
      MovementDirection::South => $this->_playerLocationService->movePlayerSouth(),
      MovementDirection::East => $this->_playerLocationService->movePlayerEast(),
      MovementDirection::West => $this->_playerLocationService->movePlayerWest(),
    };
    $this->_userInteractionsService->incrementMoves();
  }

  private function handleRun(): void
  {
    $isFailed = rand(0, 1) == 1;
    if ($isFailed) {
      $this->_feedbackEventsService->addEvent(C_ACTION_RUN_FAIL_EVENT["key"], C_ACTION_RUN_FAIL_EVENT["label"]);
    } else {
      $this->_feedbackEventsService->addEvent(C_ACTION_RUN_SUCCESS_EVENT["key"], C_ACTION_RUN_SUCCESS_EVENT["label"]);
      $this->_playerLocationService->moveToPreviousPosition();
    }
  }

  private function handlePickup(string $target): void
  {
    $currentItem = $this->_itemsService->getItem($target);

    $currentWeight = $this->_stateService->getCharacterStatsWeight();
    $this->_stateService->setCharacterStatsWeight($currentWeight + $currentItem->sumWeight);
    $this->_itemsService->removeItemFromMap($target);
    $currentGold = $this->_stateService->getCharacterStatsMoney();
    $this->_stateService->setCharacterStatsMoney($currentGold + $currentItem->wealth);

    if ($currentItem->key === "ITEM_ORB") {
      $this->_stateService->setHasOrb(true);
    }
    if ($currentItem->key === "ITEM_GRAIL") {
      $this->_stateService->setHasGrail(true);
    }

    if ($currentItem->key === "ITEM_CHEST_SWORD") {
      $this->_stateService->setEquipmentSword("EPIC");
    }
    if ($currentItem->key === "ITEM_CHEST_SHIELD") {
      $this->_stateService->setEquipmentShield("EPIC");
    }
    if ($currentItem->key === "ITEM_CHEST_ARMOR") {
      $this->_stateService->setEquipmentArmor("EPIC");
    }
    if ($currentItem->key === "ITEM_CHEST_BOW") {
      $this->_stateService->setEquipmentBow("EPIC");
    }

    $this->_feedbackEventsService->addEvent("ITEM_PICKUP", $currentItem->pickedupLabel);
  }
}
