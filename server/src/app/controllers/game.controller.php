<?php

class GameController
{

  private const GOLD_PERCENTAGE_ON_MAP = 2;
  private const GOLD_WEIGHT = 1;
  private const ORB_WEIGHT = 1000;

  public function startGame()
  {
    if (empty($_COOKIE['PHPSESSID'])) {
      http_response_code(401);
      header('Content-Type: application/json');
      echo json_encode([
          'errorCode' => 'ERROR_0401',
      ]);
      exit;
    }
    session_start();

    $stateService = new StateService();
    $chatMembersService = new ChatMembersService();
    $chatMessageService = new ChatMessagesService();
    $maze = new Maze();

    $id = session_id();
    $username = $stateService->getPlayerName();
    $location = $maze->getPlayerInitialLocation();
    $currentHits = $stateService->getPlayerMaxHits();

    $chatMembersService->addMember($username, $id);
    $stateService->setPlayerPosition($location["x"], $location["y"]);
    $stateService->setPlayerCurHits($currentHits);
    $stateService->setChatLastMessageId($chatMessageService->getLastMessageId());
    $stateService->setEquipmentSword("NORMAL");
    $stateService->setEquipmentShield("NORMAL");
    $stateService->setEquipmentArmor("NORMAL");
    $stateService->setEquipmentArrows(0);
    $stateService->setCharacterXp(0);
    $stateService->setCharacterXpFromKills(0);
    $stateService->setCharacterStatsMoney(0);
    $stateService->setCharacterStatsLevel(1);
    $stateService->setCharacterStatsWeight(0);
    $stateService->setCharacterSpellUnitsMax(4);
    $stateService->setCharacterSpellUnitsCur(4);
    $stateService->setHasOrb(false);
    $stateService->setCharacterSpellsOn([]);
    $stateService->setMapFull($maze->getFullMaze());
    $stateService->setStartTime(time());

    $walkableTiles = $maze->getWalkableTiles();
    $numberOfWalkableTiles = count($walkableTiles);
    $numberOfGoldOnMap = $numberOfWalkableTiles / 100 * self::GOLD_PERCENTAGE_ON_MAP;
    /** @var Item[] */
    $itemsOnMap = [];
    for ($i = 0; $i < $numberOfGoldOnMap; $i++) {
      $randomIndex = array_rand($walkableTiles, 1);
      $amount = roll_d10k();
      array_push(
        $itemsOnMap,
        createItem(
          "ITEM_GOLD",
          "item_gold",
          $amount,
          $amount,
          $amount * self::GOLD_WEIGHT,
          true,
          "Pick up Gold ($amount)",
          "You picked up Gold ($amount)",
          "You see a pile of gold.",
          $walkableTiles[$randomIndex]["x"],
          $walkableTiles[$randomIndex]["y"],
        )
      );
    }
    $randomIndex = array_rand($walkableTiles, 1);
    array_push(
      $itemsOnMap,
      createItem(
        "ITEM_ORB",
        "item_orb",
        1,
        0,
        1 * self::ORB_WEIGHT,
        true,
        "Pick up the Orb",
        "You picked up the Orb! Run to the entrance!",
        "You see the Orb!",
        $walkableTiles[$randomIndex]["x"],
        $walkableTiles[$randomIndex]["y"],
      )
    );
    // Sword
    $randomIndex = array_rand($walkableTiles, 1);
    array_push(
      $itemsOnMap,
      createItem(
        "ITEM_CHEST_SWORD",
        "item_chest",
        1,
        0,
        100 * self::GOLD_WEIGHT,
        true,
        "Loot chest",
        "You picked up a sword from the chest.",
        "You see a chest",
        $walkableTiles[$randomIndex]["x"],
        $walkableTiles[$randomIndex]["y"],
      )
    );
    $randomIndex = array_rand($walkableTiles, 1);
    array_push(
      $itemsOnMap,
      createItem(
        "ITEM_CHEST_SHIELD",
        "item_chest",
        1,
        0,
        100 * self::GOLD_WEIGHT,
        true,
        "Loot chest",
        "You picked up a shield from the chest.",
        "You see a chest",
        $walkableTiles[$randomIndex]["x"],
        $walkableTiles[$randomIndex]["y"],
      )
    );
    $randomIndex = array_rand($walkableTiles, 1);
    array_push(
      $itemsOnMap,
      createItem(
        "ITEM_CHEST_ARMOR",
        "item_chest",
        1,
        0,
        100 * self::GOLD_WEIGHT,
        true,
        "Loot chest",
        "You picked up an armor from the chest.",
        "You see a chest",
        $walkableTiles[$randomIndex]["x"],
        $walkableTiles[$randomIndex]["y"],
      )
    );

    $stateService->setItems($itemsOnMap);
    $this->sendBackState("START", null);
  }

  public function onAction()
  {
    if (empty($_COOKIE['PHPSESSID'])) {
      http_response_code(401);
      header('Content-Type: application/json');
      echo json_encode([
          'errorCode' => 'ERROR_0401',
      ]);
      exit;
    }
    session_start();

    $rawBody = file_get_contents('php://input');
    $payload = json_decode($rawBody, true);
    $action = $payload['action'];
    $target = array_key_exists('payload', $payload) ? $payload['payload'] : null;

    $maze = new Maze();
    $stateService = new StateService();
    $location = $stateService->getPlayerPosition();
    $tiles = $this->calculateTiles($location['x'], $location['y']);
    $hasPlayerWon = $stateService->getHasOrb() && $maze->getPlayerInitialLocation() === $location;
    $possibleActions = $this->getPossibleActions($tiles, $hasPlayerWon);
    $canDo = $this->canDoAction($possibleActions, $action, $target);

    if ($canDo) {
      switch ($action) {
        case "MOVE" && $target === "DIRECTION_NORTH": {
          $stateService->moveNorth();
          break;
        }
        case "MOVE" && $target === "DIRECTION_EAST": {
          $stateService->moveEast();
          break;
        }
        case "MOVE" && $target === "DIRECTION_SOUTH": {
          $stateService->moveSouth();
          break;
        }
        case "MOVE" && $target === "DIRECTION_WEST": {
          $stateService->moveWest();
          break;
        }
        case "PICKUP": {
          $currentItem = $this->getItemsOnTile($location["x"], $location["y"])[0];

          // General.
          $currentWeight = $stateService->getCharacterStatsWeight();
          $stateService->setCharacterStatsWeight($currentWeight + $currentItem->sumWeight);
          $itemsOnMap = array_values(array_filter($stateService->getItems(), fn($m) => $m->id !== $target));
          $stateService->setItems($itemsOnMap);
          $target = $currentItem->pickedupLabel;

          if ($currentItem->key === "ITEM_GOLD") {
            // TODO: This can be moved to general since Orb has 0 wealth.
            $currentGold = $stateService->getCharacterStatsMoney();
            $stateService->setCharacterStatsMoney($currentGold + $currentItem->wealth);
          }

          if ($currentItem->key === "ITEM_ORB") {
            $stateService->setHasOrb(true);
            $spells = $stateService->getCharacterSpellsOn();
            array_push($spells, [
              "key" => "SPELL_00",
              "label" => "Blessing of the Orb",
              "remaining" => "∞"
            ]);
            $stateService->setCharacterSpellsOn($spells);
          }

          if ($currentItem->key === "ITEM_CHEST_SWORD") {
            $stateService->setEquipmentSword("EPIC");
          }
          if ($currentItem->key === "ITEM_CHEST_SHIELD") {
            $stateService->setEquipmentShield("EPIC");
          }
          if ($currentItem->key === "ITEM_CHEST_ARMOR") {
            $stateService->setEquipmentArmor("EPIC");
          }

        }
      }
    }

    $this->sendBackState($action, $target);
  }

  private function sendBackState(
    string $lastAction,
    $lastActionTarget = null
  ) {
    $maze = new Maze();
    $stateService = new StateService();
    $hallOfFameService = new HallOfFameService();

    $nextLevelInXp = 1000; // TODO

    $sumXp = $stateService->getCharacterXp();
    $xpFromKills = $stateService->getCharacterXpFromKills();
    $xpFromKillsPercentage = $xpFromKills === 0 ? 0 : ($xpFromKills / $sumXp) * 100;

    $location = $stateService->getPlayerPosition();

    $tiles = $this->calculateTiles($location['x'], $location['y']);

    $isAtStartPoint = $maze->getPlayerInitialLocation() === $location;

    if ($isAtStartPoint) {
      $maxHp = $stateService->getPlayerMaxHits();
      $stateService->setPlayerCurHits($maxHp);

      $money = $stateService->getCharacterStatsMoney();
      $weight = $stateService->getCharacterStatsMoney();

      $stateService->setCharacterStatsMoney(0);
      $stateService->setCharacterStatsWeight($weight - $money * self::GOLD_WEIGHT);

      $xp = $stateService->getCharacterXp();
      $stateService->setCharacterXp($xp + $money);
    }

    $hasPlayerWon = $stateService->getHasOrb() && $isAtStartPoint;

    if ($hasPlayerWon) {
      $hallOfFameService->addUser(
        $stateService->getPlayerName(),
        session_id(),
        $stateService->getStartTime()
      );
    }

    $mapHeight = $maze->mazeHeight();
    $mapWidth = $maze->mazeWidth();

    // Update map state
    $map = $stateService->getMapFull();
    foreach ($tiles as $tile) {
      $y = $tile["y"];
      $x = $tile["x"];

      if (isset($map[$y][$x])) {
        $map[$y][$x]->visited = true;
      }
    }
    $stateService->setMapFull($map);
    $minimapState = [];

    for ($y = 0; $y < $mapHeight; $y++) {
      $minimapState[$y] = [];
      for ($x = 0; $x < $mapWidth; $x++) {
        $tile = $map[$y][$x];

        if (!$tile->visited) {
          $minimapState[$y][$x] = "UNKNOWN";
        } elseif ($tile->tileChar === "%" || $tile->tileChar === ".") {
          $minimapState[$y][$x] = "EMPTY";
        } else {
          $minimapState[$y][$x] = "WALL";
        }
      }
    }

    $minimapState[$location['y']][$location['x']] = "PLAYER";

    $mapSize = min($mapHeight, $mapWidth);
    echo json_encode([
      "payload" => [
        "hasPlayerWon" => $hasPlayerWon,
        "mapSize" => [
          "width" => $mapSize,
          "height" => $mapSize
        ],
        "character" => [
          "dexterity" => $stateService->getPlayerDexterity(),
          "intelligence" => $stateService->getPlayerIntelligence(),
          "strength" => $stateService->getPlayerStrength(),
          "constitution" => $stateService->getPlayerConstitution()
        ],
        "playerName" => $stateService->getPlayerName(),
        "hits" => $stateService->getPlayerCurHits(),
        "maxHits" => $stateService->getPlayerMaxHits(),
        "activeSpells" => $stateService->getCharacterSpellsOn(),
        "equipment" => [
          "sword" => $stateService->getEquipmentSword(),
          "shield" => $stateService->getEquipmentShield(),
          "armor" => $stateService->getEquipmentArmor(),
          "arrows" => $stateService->getEquipmentArrows()
        ],
        "statistics" => [
          "experience" => $stateService->getCharacterXp(),
          "money" => $stateService->getCharacterStatsMoney(),
          "nextLevelInXp" => $nextLevelInXp,
          "weight" => $stateService->getCharacterStatsWeight(),
          "playerLevel" => $stateService->getCharacterStatsLevel(),
          "spellUnits" => [
            "current" => $stateService->getCharacterSpellUnitsCur(),
            "maximum" => $stateService->getCharacterSpellUnitsMax()
          ],
          "xpPercentageFromKills" => $xpFromKillsPercentage
        ],
        "events" => $this->getEvents($tiles, $lastAction, $lastActionTarget),
        "possibleActions" => $this->getPossibleActions($tiles, $hasPlayerWon),
        "mapState" => array_map(
          fn($m) => [
            "top" => $m["top"],
            "right" => $m["right"],
            "bottom" => $m["bottom"],
            "left" => $m["left"],
            "occupiedBy" => $m["occupiedBy"],
            "containsItems" => $this->itemToItemDto($m["containsItems"]),
          ],
          $tiles
        ),
        "minimapState" => $minimapState
      ]
    ]);
  }

  private function calculateTiles(
    $centerX,
    $centerY
  ): array {
    $radius = 3;
    $neighborCount = (int) floor($radius / 2);

    $maze = new Maze();

    $result = [];
    for ($y = -$neighborCount; $y <= $neighborCount; $y++) {
      for ($x = -$neighborCount; $x <= $neighborCount; $x++) {
        array_push($result, [
          "top" => $this->getBorderType($maze->getTile($centerX + $x, $centerY + $y), $maze->getTile($centerX + $x - 0, $centerY + $y - 1)),
          "right" => $this->getBorderType($maze->getTile($centerX + $x, $centerY + $y), $maze->getTile($centerX + $x + 1, $centerY + $y - 0)),
          "bottom" => $this->getBorderType($maze->getTile($centerX + $x, $centerY + $y), $maze->getTile($centerX + $x - 0, $centerY + $y + 1)),
          "left" => $this->getBorderType($maze->getTile($centerX + $x, $centerY + $y), $maze->getTile($centerX + $x - 1, $centerY + $y - 0)),
          "occupiedBy" => ($x === 0 && $y === 0) ? ["key" => "CHARACTER"] : null,
          "containsItems" => $this->getItemsOnTile($centerX + $x, $centerY + $y),
          "x" => $centerX + $x,
          "y" => $centerY + $y
        ]);
      }
    }
    return $result;
  }

  private function getEvents(
    array $tiles,
    string $lastAction,
    $lastActionTarget = null
  ): array {
    $events = [];

    if ($lastAction === "PICKUP") {
      array_push($events, [
        "key" => "ITEM_PICKUP",
        "label" => $lastActionTarget
      ]);
    }

    $visibleItems = array_merge(...array_values(array_map(fn($m) => $m['containsItems'], $tiles)));

    foreach ($visibleItems as $item) {
      array_push($events, [
        "key" => "ITEM_SEE",
        "label" => $item->visibleLabel
      ]);
    }

    return $events;
  }

  private function getPossibleActions(
    $tiles,
    $isActionsDisabled
  ): array {
    $actions = [];
    if ($isActionsDisabled) {
      return $actions;
    }
    $playerTile = $tiles[(int) floor(count($tiles) / 2)];

    $canFight = false;  // TODO
    $canCast = true;    // TODO
    $canPickup = !empty($playerTile["containsItems"]);

    $canMoveNorth = $playerTile["top"] === "TILE_OPEN";
    $canMoveEast = $playerTile["right"] === "TILE_OPEN";
    $canMoveSouth = $playerTile["bottom"] === "TILE_OPEN";
    $canMoveWest = $playerTile["left"] === "TILE_OPEN";

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

    $canFight && array_push($actions, ["label" => "[R]un", "key" => "RUN", "payload" => null]);
    $canFight && array_push($actions, ["label" => "[F]ight", "key" => "FIGHT", "payload" => null]);
    !$canFight && $canMoveNorth && array_push($actions, ["label" => "[↑] North", "key" => "MOVE", "payload" => "DIRECTION_NORTH"]);
    !$canFight && $canMoveEast && array_push($actions, ["label" => "[→] East", "key" => "MOVE", "payload" => "DIRECTION_EAST"]);
    !$canFight && $canMoveSouth && array_push($actions, ["label" => "[↓] South", "key" => "MOVE", "payload" => "DIRECTION_SOUTH"]);
    !$canFight && $canMoveWest && array_push($actions, ["label" => "[←] West", "key" => "MOVE", "payload" => "DIRECTION_WEST"]);
    $canCast && array_push($actions, ["label" => "[C]ast a spell", "key" => "CAST_SPELL", "payload" => null]);

    return $actions;
  }
  private function getBorderType(
    $currentTile,
    $targetTile
  ): string {
    if ($currentTile === "#")
      return "TILE_OPEN";
    if ($targetTile === "#")
      return "TILE_WALL";
    if ($targetTile === "%")
      return "TILE_OPEN_OUT";
    return "TILE_OPEN";
  }

  private function itemToItemDto(
    array $items
  ): array {
    return array_map(fn($m) => (object) [
      "id" => $m->id,
      "iconName" => $m->iconName,
    ], $items);
  }

  private function getItemsOnTile($x, $y): array
  {
    // TODO: To make this work properly, tiles that are not visible should not be considered.
    $stateService = new StateService();
    $itemsOnMap = $stateService->getItems();
    $itemsOnTile = array_values(array_filter($itemsOnMap, fn($m) => $m->locationX === $x && $m->locationY === $y));

    return array_map(fn($m) => (object) [
      "id" => $m->id,
      "key" => $m->key,
      "iconName" => $m->iconName,
      "amount" => $m->amount,
      "wealth" => $m->wealth,
      "sumWeight" => $m->sumWeight,
      "canPickup" => $m->canPickup,
      "pickupLabel" => $m->pickupLabel,
      "pickedupLabel" => $m->pickedupLabel,
      "visibleLabel" => $m->visibleLabel,
    ], $itemsOnTile);
  }

  private function canDoAction(
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
}
?>