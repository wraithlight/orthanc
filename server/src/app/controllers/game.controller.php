<?php

class GameController
{

  private const GOLD_PERCENTAGE_ON_MAP = 2;
  private const GOLD_WEIGHT = 1;
  private const ORB_WEIGHT = 1000;

  public function startGame()
  {
    $stateService = new StateService();
    $chatMembersService = new ChatMembersService();
    $chatMessageService = new ChatMessagesService();
    $maze = new Maze();

    $username = $stateService->getPlayerName();
    $location = $maze->getPlayerInitialLocation();
    $currentHits = $stateService->getPlayerMaxHits();

    $chatMembersService->addMember($username);
    $stateService->setPlayerPosition($location["x"], $location["y"]);
    $stateService->setPlayerCurHits($currentHits);
    $stateService->setChatLastMessageId($chatMessageService->getLastMessageId());
    $stateService->setEquipmentSword("SWORD_NORMAL");
    $stateService->setEquipmentShield("SHIELD_NORMAL");
    $stateService->setEquipmentArmor("ARMOR_NORMAL");
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

    $walkableTiles = $maze->getWalkableTiles();
    $numberOfWalkableTiles = count($walkableTiles);
    $numberOfGoldOnMap = $numberOfWalkableTiles / 100 * self::GOLD_PERCENTAGE_ON_MAP;
    $itemsOnMap = [];
    for ($i = 0; $i < $numberOfGoldOnMap; $i++) {
      $randomIndex = array_rand($walkableTiles, 1);
      $amount = roll_d10k();
      array_push(
        $itemsOnMap,
        [
          "x" => $walkableTiles[$randomIndex]["x"],
          "y" => $walkableTiles[$randomIndex]["y"],
          "item" => "ITEM_GOLD",
          "amount" => $amount
        ]
      );
    }
    $randomIndex = array_rand($walkableTiles, 1);
    array_push(
      $itemsOnMap,
      [
        "x" => $walkableTiles[$randomIndex]["x"],
        "y" => $walkableTiles[$randomIndex]["y"],
        "item" => "ITEM_ORB",
        "amount" => 1
      ]
    );

    array_push(
      $itemsOnMap,
      [
        "x" => 1,
        "y" => 1,
        "item" => "ITEM_GOLD",
        "amount" => 100,
        "weight" => 100 * self::GOLD_WEIGHT
      ]
    );
    $stateService->setItems($itemsOnMap);

    $this->sendBackState("START", null);
  }

  public function onAction()
  {
    $rawBody = file_get_contents('php://input');
    $payload = json_decode($rawBody, true);
    $action = $payload['action'];
    $target = array_key_exists('payload', $payload) ? $payload['payload'] : null;

    $maze = new Maze();
    $stateService = new StateService();
    $location = $stateService->getPlayerPosition();
    $tilesAround = $maze->getTilesAroundPlayer($location['x'], $location['y']);
    $tiles = $this->calculateTiles($tilesAround, $location['x'], $location['y']);
    $hasPlayerWon = $stateService->getHasOrb() && $maze->getPlayerInitialLocation() === $location;
    $possibleActions = $this->getPossibleActions($tiles, $hasPlayerWon);
    $canDo = $this->canDoAction($possibleActions, $action, $target);

    if ($canDo) {
      switch ($action) {
        case "MOVE_NORTH": {
          $stateService->moveNorth();
          break;
        }
        case "MOVE_EAST": {
          $stateService->moveEast();
          break;
        }
        case "MOVE_SOUTH": {
          $stateService->moveSouth();
          break;
        }
        case "MOVE_WEST": {
          $stateService->moveWest();
          break;
        }
        case "PICKUP" && $target === "ITEM_ORB": {
          $stateService->setHasOrb(true);
          $spells = $stateService->getCharacterSpellsOn();
          array_push($spells, [
            "key" => "SPELL_00",
            "label" => "Blessing of the Orb",
            "remaining" => "∞"
          ]);
          $currentWeight = $stateService->getCharacterStatsWeight();
          $stateService->setCharacterStatsWeight($currentWeight + self::ORB_WEIGHT);
          $stateService->setCharacterSpellsOn($spells);
          $itemsOnMap = array_values(array_filter($stateService->getItems(), fn($m) => $m['item'] !== "ITEM_ORB"));
          $stateService->setItems($itemsOnMap);
        }
        case "PICKUP" && $target === "ITEM_GOLD": {
          $currentGold = $stateService->getCharacterStatsMoney();
          $currentWeight = $stateService->getCharacterStatsWeight();
          $currentItem = $this->getItemsOnTile($location["x"], $location["y"])[0];

          $stateService->setCharacterStatsMoney($currentGold + $currentItem["amount"]);
          $stateService->setCharacterStatsWeight($currentWeight + $currentItem["amount"] * self::GOLD_WEIGHT);

          $itemsOnMap = array_values(array_filter($stateService->getItems(), fn($m) => $m['item'] !== "ITEM_GOLD" && $m["x"] === $location["x"] && $m["y"] === $location["y"]));
          $stateService->setItems($itemsOnMap);
        }
      }
    }

    $this->sendBackState($action, $target);
  }

  public function maze()
  {
    $maze = new Maze();
    $stateService = new StateService();

    $map = $maze->getFullMap();
    $location = $maze->getPlayerInitialLocation();
    $tilesAround = $maze->getTilesAroundPlayer($location['x'], $location['y']);
    echo json_encode([
      "location" => $location,
      "tilesAround" => $tilesAround,
      "gold" => $stateService->getItems(),
      // "walkableTiles" => $maze->getWalkableTiles(),
      "maze" => $map,
      "size" => [
        "width" => $maze->mazeWidth(),
        "height" => $maze->mazeHeight(),
      ]
    ]);
  }

  private function sendBackState(
    string $lastAction,
    $lastActionTarget = null
  ) {
    $maze = new Maze();
    $stateService = new StateService();

    $nextLevelInXp = 1000; // TODO

    $sumXp = $stateService->getCharacterXp();
    $xpFromKills = $stateService->getCharacterXpFromKills();
    $xpFromKillsPercentage = $xpFromKills === 0 ? 0 : ($xpFromKills / $xpFromKills) * 100;

    $location = $stateService->getPlayerPosition();
    $tilesAround = $maze->getTilesAroundPlayer($location['x'], $location['y']);

    $tiles = $this->calculateTiles($tilesAround, $location['x'], $location['y']);

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

      // TOD: XP from kills calculation
    }

    $hasPlayerWon = $stateService->getHasOrb() && $isAtStartPoint;
    $mapHeight = $maze->mazeHeight();
    $mapWidth = $maze->mazeWidth();

    $mapSize = min($mapHeight, $mapWidth);
    echo json_encode([
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
      "mapState" => $tiles
    ]);
  }

  private function calculateTiles(
    $currentTiles,
    $x,
    $y
  ): array {
    return [
      "tile00" => [
        "top" => $this->getBorderType($currentTiles["tile11"], $currentTiles["tile01"]),
        "right" => $this->getBorderType($currentTiles["tile11"], $currentTiles["tile12"]),
        "bottom" => $this->getBorderType($currentTiles["tile11"], $currentTiles["tile21"]),
        "left" => $this->getBorderType($currentTiles["tile11"], $currentTiles["tile10"]),
        "occupiedBy" => null,
        "containsItems" => $this->getItemsOnTile($x - 1, $y - 1)
      ],
      "tile01" => [
        "top" => $this->getBorderType($currentTiles["tile12"], $currentTiles["tile02"]),
        "right" => $this->getBorderType($currentTiles["tile12"], $currentTiles["tile13"]),
        "bottom" => $this->getBorderType($currentTiles["tile12"], $currentTiles["tile22"]),
        "left" => $this->getBorderType($currentTiles["tile12"], $currentTiles["tile11"]),
        "occupiedBy" => null,
        "containsItems" => $this->getItemsOnTile($x + 0, $y - 1)
      ],
      "tile02" => [
        "top" => $this->getBorderType($currentTiles["tile13"], $currentTiles["tile03"]),
        "right" => $this->getBorderType($currentTiles["tile13"], $currentTiles["tile14"]),
        "bottom" => $this->getBorderType($currentTiles["tile13"], $currentTiles["tile23"]),
        "left" => $this->getBorderType($currentTiles["tile13"], $currentTiles["tile12"]),
        "occupiedBy" => null,
        "containsItems" => $this->getItemsOnTile($x + 1, $y - 1)
      ],
      "tile10" => [
        "top" => $this->getBorderType($currentTiles["tile21"], $currentTiles["tile11"]),
        "right" => $this->getBorderType($currentTiles["tile21"], $currentTiles["tile22"]),
        "bottom" => $this->getBorderType($currentTiles["tile21"], $currentTiles["tile31"]),
        "left" => $this->getBorderType($currentTiles["tile21"], $currentTiles["tile20"]),
        "occupiedBy" => null,
        "containsItems" => $this->getItemsOnTile($x - 1, $y + 0)
      ],
      "tile11" => [
        "top" => $this->getBorderType($currentTiles["tile22"], $currentTiles["tile12"]),
        "right" => $this->getBorderType($currentTiles["tile22"], $currentTiles["tile23"]),
        "bottom" => $this->getBorderType($currentTiles["tile22"], $currentTiles["tile32"]),
        "left" => $this->getBorderType($currentTiles["tile22"], $currentTiles["tile21"]),
        "occupiedBy" => [
            "key" => "PLAYER",
          ],
        "containsItems" => $this->getItemsOnTile($x + 0, $y + 0)
      ],
      "tile12" => [
        "top" => $this->getBorderType($currentTiles["tile23"], $currentTiles["tile13"]),
        "right" => $this->getBorderType($currentTiles["tile23"], $currentTiles["tile24"]),
        "bottom" => $this->getBorderType($currentTiles["tile23"], $currentTiles["tile33"]),
        "left" => $this->getBorderType($currentTiles["tile23"], $currentTiles["tile22"]),
        "occupiedBy" => null,
        "containsItems" => $this->getItemsOnTile($x + 1, $y + 0)
      ],
      "tile20" => [
        "top" => $this->getBorderType($currentTiles["tile31"], $currentTiles["tile21"]),
        "right" => $this->getBorderType($currentTiles["tile31"], $currentTiles["tile32"]),
        "bottom" => $this->getBorderType($currentTiles["tile31"], $currentTiles["tile41"]),
        "left" => $this->getBorderType($currentTiles["tile31"], $currentTiles["tile30"]),
        "occupiedBy" => null,
        "containsItems" => $this->getItemsOnTile($x - 1, $y + 1)
      ],
      "tile21" => [
        "top" => $this->getBorderType($currentTiles["tile32"], $currentTiles["tile22"]),
        "right" => $this->getBorderType($currentTiles["tile32"], $currentTiles["tile33"]),
        "bottom" => $this->getBorderType($currentTiles["tile32"], $currentTiles["tile42"]),
        "left" => $this->getBorderType($currentTiles["tile32"], $currentTiles["tile31"]),
        "occupiedBy" => null,
        "containsItems" => $this->getItemsOnTile($x + 0, $y + 1)
      ],
      "tile22" => [
        "top" => $this->getBorderType($currentTiles["tile33"], $currentTiles["tile23"]),
        "right" => $this->getBorderType($currentTiles["tile33"], $currentTiles["tile34"]),
        "bottom" => $this->getBorderType($currentTiles["tile33"], $currentTiles["tile43"]),
        "left" => $this->getBorderType($currentTiles["tile33"], $currentTiles["tile32"]),
        "occupiedBy" => null,
        "containsItems" => $this->getItemsOnTile($x + 1, $y + 1)
      ]
    ];
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
        "label" => "You picked up $lastActionTarget."
      ]);
    }

    $visibleItems = array_merge(...array_values(array_map(fn($m) => $m['containsItems'], $tiles)));

    foreach ($visibleItems as $item) {
      array_push($events, [
        "key" => "ITEM_SEE",
        "label" => "You see a(n) {$item['key']}."
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
    $playerTile = $tiles["tile11"];

    $canFight = false;  // TODO
    $canCast = true;    // TODO
    $canPickup = !empty($playerTile["containsItems"]);

    $canMoveNorth = $playerTile["top"] === "TILE_OPEN";
    $canMoveEast = $playerTile["right"] === "TILE_OPEN";
    $canMoveSouth = $playerTile["bottom"] === "TILE_OPEN";
    $canMoveWest = $playerTile["left"] === "TILE_OPEN";

    if ($canPickup) {
      foreach ($playerTile["containsItems"] as $item) {
        array_push($actions, [
          "label" => "Pick up {$item['key']} ({$item['amount']})",
          "key" => "PICKUP",
          "payload" => $item['key']
        ]);
      }
    }

    $canFight && array_push($actions, ["label" => "[R]un", "key" => "RUN", "payload" => null]);
    $canFight && array_push($actions, ["label" => "[F]ight", "key" => "FIGHT", "payload" => null]);
    !$canFight && $canMoveNorth && array_push($actions, ["label" => "[↑] North", "key" => "MOVE_NORTH", "payload" => null]);
    !$canFight && $canMoveEast && array_push($actions, ["label" => "[→] East", "key" => "MOVE_EAST", "payload" => null]);
    !$canFight && $canMoveSouth && array_push($actions, ["label" => "[↓] South", "key" => "MOVE_SOUTH", "payload" => null]);
    !$canFight && $canMoveWest && array_push($actions, ["label" => "[←] West", "key" => "MOVE_WEST", "payload" => null]);
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

  private function getItemsOnTile($x, $y): array
  {
    // TODO: To make this work properly, tiles that are not visible should not be considered.
    $stateService = new StateService();
    $itemsOnMap = $stateService->getItems();
    $itemsOnTile = array_values(array_filter($itemsOnMap, fn($m) => $m["x"] === $x && $m["y"] === $y));

    return array_map(fn($m) => [
      "key" => $m["item"],
      "amount" => $m["amount"]
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