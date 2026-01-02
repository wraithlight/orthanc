<?php

class ActionsManager
{

  private $_feedbackEventsService;
  private $_playerLocationService;

  public function __construct()
  {
    $this->_feedbackEventsService = new FeedbackEventsService();
    $this->_playerLocationService = new PlayerLocationService();
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
      }
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
    $stateService = new StateService();
    $playerTile = $tiles[(int) floor(count($tiles) / 2)];
    $visibleNpcs = array_values(array_filter(array_map(fn($m) => $m['occupiedBy'], $tiles), fn($m) => isset($m) && $m->key !== "CHARACTER"));

    $canFight = count($visibleNpcs) === 1;
    $canRun = $canFight && $this->_playerLocationService->isPreviousAndCurrentSame();
    $canMove = count($visibleNpcs) === 0;
    $canCast = $stateService->getCharacterSpellUnitsCur() > 0;
    $canPickup = !empty($playerTile["containsItems"]);

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
            "payload" => $item->id,
            "isClientSideOnly" => false
          ]);
        }
      }
    }

    $canRun && array_push($actions, ["label" => "[R]un", "key" => "RUN", "payload" => null, "isClientSideOnly" => false]);
    $canFight && array_push($actions, ["label" => "[F]ight", "key" => "FIGHT", "payload" => null, "isClientSideOnly" => false]);
    $canMove && $canMoveNorth && array_push($actions, ["label" => "[↑] North", "key" => "MOVE", "payload" => MovementDirection::North->value, "isClientSideOnly" => false]);
    $canMove && $canMoveEast && array_push($actions, ["label" => "[→] East", "key" => "MOVE", "payload" => MovementDirection::East->value, "isClientSideOnly" => false]);
    $canMove && $canMoveSouth && array_push($actions, ["label" => "[↓] South", "key" => "MOVE", "payload" => MovementDirection::South->value, "isClientSideOnly" => false]);
    $canMove && $canMoveWest && array_push($actions, ["label" => "[←] West", "key" => "MOVE", "payload" => MovementDirection::West->value, "isClientSideOnly" => false]);
    $canCast && array_push($actions, ["label" => "[C]ast a spell", "key" => "CAST_SPELL", "payload" => null, "isClientSideOnly" => true]);

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
}
