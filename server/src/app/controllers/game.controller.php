<?php

class GameController {

  const GOLD_PERCENTAGE_ON_MAP = 2;
  const GOLD_WEIGHT = 1;

  public function startGame() {
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

    $walkableTiles = $maze->getWalkableTiles();
    $numberOfWalkableTiles = count($walkableTiles);
    $numberOfGoldOnMap = $numberOfWalkableTiles / 100 * self::GOLD_PERCENTAGE_ON_MAP;
    $itemsOnMap = [];
    for($i = 0; $i < $numberOfGoldOnMap; $i++) {
      $randomIndex = array_rand($walkableTiles, 1);
      $amount = roll_d10k();
      array_push(
        $itemsOnMap,
        [
          "x" => $walkableTiles[$randomIndex]["x"],
          "y" => $walkableTiles[$randomIndex]["y"],
          "item" => "ITEM_GOLD",
          "amount" => $amount,
          "weight" => $amount * self::GOLD_WEIGHT
        ]
      );
    }
    array_push(
        $itemsOnMap,
        [
          "x" => 1,
          "y" => 1,
          "item" => "ITEM_GOLD",
          "amount" => $amount,
          "weight" => $amount * self::GOLD_WEIGHT
        ]
      );
    $stateService->setItems($itemsOnMap);

    // TODO
    $stateService->setCharacterSpellsOn(array(
      [
        "key" => "SPELL_01",
        "label" => "Levitation",
        "remaining" => 5
      ]
    ));

    $this->sendBackState();
  }

  public function onAction() {
    $rawBody = file_get_contents('php://input');
    $payload = json_decode($rawBody, true);
    $action = $payload['action'];

    $maze = new Maze();
    $stateService = new StateService();
    $location = $stateService->getPlayerPosition();
    $tilesAround = $maze->getTilesAroundPlayer($location['x'], $location['y']);
    $tiles = $this->calculateTiles($tilesAround, $location['x'], $location['y']);
    $possibleActions = $this->getPossibleActions($tiles);
    $canDo = in_array($action, array_column($possibleActions, 'key'), true);

    if ($canDo) {
      switch($action) {
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
      }
    }

    $this->sendBackState();
  }

  public function maze() {
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

  private function sendBackState() {
    $maze = new Maze();
    $stateService = new StateService();

    $nextLevelInXp = 1000; // TODO

    $sumXp = $stateService->getCharacterXp();
    $xpFromKills = $stateService->getCharacterXpFromKills();
    $xpFromKillsPercentage = $xpFromKills === 0 ? 0 : ($xpFromKills / $xpFromKills) * 100;

    $location = $stateService->getPlayerPosition();
    $tilesAround = $maze->getTilesAroundPlayer($location['x'], $location['y']);

    $tiles = $this->calculateTiles($tilesAround, $location['x'], $location['y']);
    echo json_encode([
      "__debug" => $_SESSION,
      "character" => [
        "dexterity" => $stateService->getPlayerDexterity(),
        "intelligence" => $stateService->getPlayerIntelligence(),
        "strength" => $stateService->getPlayerStrength(),
        "constitution" => $stateService->getPlayerConstitution()
      ],
      "playerName" => $stateService->getPlayerName(),
      "hits" => $stateService->getPlayerCurHits(),
      "maxHits" => $stateService->getPlayerMaxHits(),
      "activeSpells" => $stateService->getCharacterSpellsOn([]),
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
          "current" => $stateService->getCharacterSpellUnitsCur(0),
          "maximum" => $stateService->getCharacterSpellUnitsMax()
        ],
        "xpPercentageFromKills" => $xpFromKillsPercentage
      ],
      "events" => array(
        [
          "key" => "PLAYER_SEE_ENEMY_0_NEUTRAL",
          "label" => "You see a neutral orc."
        ],
        [
          "key" => "ENEMY_0_NETURAL_ACTION_0",
          "label" => "The neutral orc waves at you."
        ]
      ),
      "possibleActions" => $this->getPossibleActions($tiles),
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
          "containsItems" => $this->getItemsOnTile($x + 0, $y - 1)
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

  private function getPossibleActions(
    $tiles
  ): array {
    $actions = [];
    $playerTile = $tiles["tile11"];

    $canFight = false;  // TODO
    $canCast = true;    // TODO
    $canPickup = !empty($playerTile["containsItems"]);

    $canMoveNorth = $playerTile["top"] === "TILE_OPEN";
    $canMoveEast = $playerTile["right"] === "TILE_OPEN";
    $canMoveSouth = $playerTile["bottom"] === "TILE_OPEN";
    $canMoveWest = $playerTile["left"] === "TILE_OPEN";

    if ($canPickup) {
      foreach($playerTile["containsItems"] as $item) {
        array_push($actions, ["label" => "Pick up {$item['key']} ({$item['amount']})", "key" => "PICKUP"]);
      }
    }

    $canFight && array_push($actions, ["label" => "[R]un", "key" => "RUN"]);
    $canFight && array_push($actions, ["label" => "[F]ight", "key" => "FIGHT"]);
    !$canFight && $canMoveNorth && array_push($actions, ["label" => "[N]orth", "key" => "MOVE_NORTH"]);
    !$canFight && $canMoveEast && array_push($actions, ["label" => "[E]ast", "key" => "MOVE_EAST"]);
    !$canFight && $canMoveSouth && array_push($actions, ["label" => "[S]outh", "key" => "MOVE_SOUTH"]);
    !$canFight && $canMoveWest && array_push($actions, ["label" => "[W]est", "key" => "MOVE_WEST"]);
    $canCast && array_push($actions, ["label" => "[C]ast a spell", "key" => "CAST_SPELL"]);

    return $actions;
  }

  private function getBorderType(
    $currentTile,
    $targetTile
  ): string {
    if ($currentTile === "#") return "TILE_OPEN";
    if ($targetTile === "#") return "TILE_WALL";
    if ($targetTile === "%") return "TILE_OPEN_OUT";
    return "TILE_OPEN";
  }

  private function getItemsOnTile($x, $y): array {
    // TODO: To make this work properly, tiles that are not visible should not be considered.
    $stateService = new StateService();
    $itemsOnMap = $stateService->getItems();
    $itemsOnTile = array_values(array_filter($itemsOnMap, fn($m) => $m["x"] === $x && $m["y"] === $y));

    return array_map(fn($m) => [
      "key" => $m["item"],
      "amount" => $m["amount"],
      "weight" => $m["weight"]
    ], $itemsOnTile);
  }
}
?>
