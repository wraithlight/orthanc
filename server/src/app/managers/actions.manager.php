<?php

class ActionsManager
{

  private $_playerLocationService;

  public function __construct() {
    $this->_playerLocationService = new PlayerLocationService();
  }

  public function handleAction(
    string $action,
    string $target
  ): void
  {
    switch($action) {
      case "MOVE": {
        $this->handleMovement($target);
        break;
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
    $canMove && $canMoveNorth && array_push($actions, ["label" => "[↑] North", "key" => "MOVE", "payload" => "DIRECTION_NORTH", "isClientSideOnly" => false]);
    $canMove && $canMoveEast && array_push($actions, ["label" => "[→] East", "key" => "MOVE", "payload" => "DIRECTION_EAST", "isClientSideOnly" => false]);
    $canMove && $canMoveSouth && array_push($actions, ["label" => "[↓] South", "key" => "MOVE", "payload" => "DIRECTION_SOUTH", "isClientSideOnly" => false]);
    $canMove && $canMoveWest && array_push($actions, ["label" => "[←] West", "key" => "MOVE", "payload" => "DIRECTION_WEST", "isClientSideOnly" => false]);
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

  private function handleMovement(string $target): void
  {
    switch ($target) {
      case $target === "DIRECTION_NORTH": {
        $this->_playerLocationService->movePlayerNorth();
        break;
      }
      case $target === "DIRECTION_EAST": {
        $this->_playerLocationService->movePlayerEast();
        break;
      }
      case $target === "DIRECTION_SOUTH": {
        $this->_playerLocationService->movePlayerSouth();
        break;
      }
      case $target === "DIRECTION_WEST": {
        $this->_playerLocationService->movePlayerWest();
        break;
      }
    }
  }
}
