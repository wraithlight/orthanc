<?php

class GameController
{
  private $_actionsManager;
  private $_chatManager;
  private $_itemsOnMapManager;
  private $_sessionManager;

  public function __construct()
  {
    $this->_actionsManager = new ActionsManager();
    $this->_chatManager = new ChatManager();
    $this->_itemsOnMapManager = new ItemsOnMapManager();
    $this->_sessionManager = new SessionManager();
  }

  private const GOLD_WEIGHT = 1;

  public function startGame()
  {
    $this->_sessionManager->authenticate();

    $maze = new Maze();
    $stateService = new StateService();
    $levelService = new LevelService();
    $playerService = new PlayerService();
    $playerLocationService = new PlayerLocationService();
    $feedbackEventsService = new FeedbackEventsService();
    $userInteractionsService = new UserInteractionsService();

    $levelService->setCurrentAllXp(0);
    $levelService->setCurrentKillsXp(0);

    $maxSpellUnits = $levelService->getMaxSpellUnits();

    $location = $maze->getPlayerInitialLocation();

    $playerLocationService->setPlayerCurrentLocation($location["x"], $location["y"]);
    $playerLocationService->setPlayerInitialLocation($location["x"], $location["y"]);
    $playerLocationService->setPlayerPreviousLocation($location["x"], $location["y"]);

    $feedbackEventsService->startRound();
    $userInteractionsService->reset();
    // Modifiers
    $stateService->setCharacterSpellsOn([]);
    // Inventory
    $stateService->setEquipmentSword("NORMAL");
    $stateService->setEquipmentShield("NORMAL");
    $stateService->setEquipmentArmor("NORMAL");
    $stateService->setEquipmentBow("NORMAL");
    $stateService->setEquipmentArrows(0);
    $stateService->setHasOrb(false);
    // Stats
    $playerService->setCurrentHitsToMax();
    $stateService->setCharacterStatsMoney(0);
    $stateService->setCharacterStatsWeight(0);
    // Spells
    $playerService->setCurrentSpellUnits($maxSpellUnits);

    $stateService->setMapFull($maze->getFullMaze());
    $stateService->setStartTime(time());

    $walkableTiles = $maze->getWalkableTiles(); // TODO: move this to manager layer.
    $this->_itemsOnMapManager->createItemsOnMap($walkableTiles);

    $stateService->setPreviousGameState($this->getGameState());
    $this->sendBackState();
  }

  public function onAction()
  {
    $this->_sessionManager->authenticate();

    $playerLocationService = new PlayerLocationService();
    $feedbackEventsService = new FeedbackEventsService();

    $rawBody = file_get_contents('php://input');
    $payload = json_decode($rawBody, true);
    $action = $payload['action'];
    $target = array_key_exists('payload', $payload) ? $payload['payload'] : null;

    $feedbackEventsService->startRound();
    $location = $playerLocationService->getPlayerCurrentLocation();
    $tiles = $this->calculateTiles($location->coordX, $location->coordY);
    $possibleActions = $this->_actionsManager->getPossibleActions($tiles, $this->getGameState());
    $canDo = $this->_actionsManager->canDoAction($possibleActions, $action, $target);

    $canDo && $this->_actionsManager->handleAction($action, $target);
    $this->sendBackState();
  }

  private function sendBackState()
  {
    $maze = new Maze();
    $playerService = new PlayerService();
    $stateService = new StateService();
    $levelService = new LevelService();
    $hallOfFameService = new HallOfFameService();
    $playerLocationService = new PlayerLocationService();

    $location = $playerLocationService->getPlayerCurrentLocation();

    $tiles = $this->calculateTiles($location->coordX, $location->coordY);
    $isAtStartPoint = $playerLocationService->isAtInitialLocation();

    if ($isAtStartPoint) {
      $playerService->setCurrentHitsToMax();

      $money = $stateService->getCharacterStatsMoney();
      $weight = $stateService->getCharacterStatsMoney();

      $stateService->setCharacterStatsMoney(0);
      $stateService->setCharacterStatsWeight($weight - $money * self::GOLD_WEIGHT);

      $levelService->addXp($money);
      $spellUnits = $levelService->getMaxSpellUnits();
      $playerService->setCurrentSpellUnits($spellUnits);
    }

    $gameState = $this->getGameState();
    $previousGameState = $stateService->getPreviousGameState();

    if ($gameState === GameState::EndSuccess && $previousGameState !== GameState::EndSuccess) {
      $configService = new ConfigService();
      $userInteractionsService = new UserInteractionsService();

      $playerName = $stateService->getPlayerName();
      $hallOfFameService->addUser(
        $playerName,
        $this->_sessionManager->getSessionId(),
        $stateService->getStartTime(),
        $levelService->getCurrentXp(),
        $levelService->getXpPercentageFromKills(),
        $userInteractionsService->getMoves(),
        $userInteractionsService->getActions(),
        $levelService->getLevel(),
        $configService->getVersion()
      );
      $this->_chatManager->sendSystemMessage("All hail to $playerName who is just conquered the dungeon!"); 
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

    $minimapState[$location->coordY][$location->coordX] = "PLAYER";
    $stateService->setPreviousGameState($gameState);

    $mapSize = min($mapHeight, $mapWidth);
    echo json_encode([
      "payload" => [
        "gameState" => $gameState,
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
        "hits" => $playerService->getCurrentHits(),
        "maxHits" => $playerService->getMaxHits(),
        "activeSpells" => $stateService->getCharacterSpellsOn(),
        "equipment" => [
          "sword" => $stateService->getEquipmentSword(),
          "shield" => $stateService->getEquipmentShield(),
          "armor" => $stateService->getEquipmentArmor(),
          "arrows" => $stateService->getEquipmentArrows(),
          "bow" => $stateService->getEquipmentBow(),
        ],
        "statistics" => [
          "experience" => $levelService->getCurrentXp(),
          "money" => $stateService->getCharacterStatsMoney(),
          "nextLevelInXp" => $levelService->getXpForNextLevel(),
          "weight" => $stateService->getCharacterStatsWeight(),
          "playerLevel" => $levelService->getLevel(),
          "spellUnits" => [
            "current" => $playerService->getCurrentSpellUnits(),
            "maximum" => $levelService->getMaxSpellUnits()
          ],
          "xpPercentageFromKills" => $levelService->getXpPercentageFromKills()
        ],
        "availableSpells" => [
          "level_1" => [
            [
              "displayName" => "Levitation",
              "action" => [
                "key" => "CAST",
                "payload" => "b77cc1a0-91ec-4d64-bb6d-21717737ea3c"
              ]
            ]
          ],
          "level_2" => [
            [
              "displayName" => "Invisibility",
              "action" => [
                "key" => "CAST",
                "payload" => "9b3ea5f2-e43b-44d0-83f3-e2d97dfff065"
              ]
            ]
          ],
          "level_3" => [
            [
              "displayName" => "Immortality",
              "action" => [
                "key" => "CAST",
                "payload" => "bdf61c73-d9e3-41e2-b05d-4433caf2e650"
              ]
            ]
          ],
          "level_4" => [
            [
              "displayName" => "Teleportation",
              "action" => [
                "key" => "CAST",
                "payload" => "a012e1e0-59ad-4f0c-b230-d911d91086cd"
              ]
            ]
          ]
        ],
        "events" => $this->getEvents($tiles),
        "possibleActions" => $this->_actionsManager->getPossibleActions($tiles, $gameState),
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
          "occupiedBy" => ($x === 0 && $y === 0) ? (object) ["key" => "CHARACTER"] : $this->getNPCsOnTile($centerX + $x, $centerY + $y),
          "containsItems" => $this->getItemsOnTile($centerX + $x, $centerY + $y),
          "x" => $centerX + $x,
          "y" => $centerY + $y
        ]);
      }
    }
    return $result;
  }

  private function getEvents(
    array $tiles
  ): array {
    $feedbackEventsService = new FeedbackEventsService();
    $visibleItems = array_merge(
      ...array_filter(
        array_map(fn($m) => $m['containsItems'], $tiles),
        fn($items) => !empty($items)
      )
    );

    foreach ($visibleItems as $item) {
      $feedbackEventsService->addEvent("ITEM_SEE", $item->visibleLabel);
    }

    return $feedbackEventsService->getEvents();
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

  private function getNPCsOnTile($x, $y): ?NPC
  {
    // if ($x === 2 && $y === 1) {
    //   $manager = new NPCsOnMapManager();
    //   return $manager->getNPCsOnMap([])[0];
    // }
    return null;
  }

  private function getItemsOnTile($x, $y): array
  {
    // TODO: To make this work properly, tiles that are not visible should not be considered.
    $itemsService = new ItemsService();
    $itemsOnTile = $itemsService->getItemsOnTile($x, $y);

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

  private function getGameState(): GameState
  {
    if ($this->isPlayerDead()) {
      return GameState::EndFail;
    }
    if ($this->areWinConditionsMet()) {
      return GameState::EndSuccess;
    }
    return GameState::Running;
  }

  private function isPlayerDead(): bool
  {
    $playerService = new PlayerService();
    $currentHits = $playerService->getCurrentHits();
    $isAlive = $currentHits <= 0;
    return $isAlive;
  }

  private function areWinConditionsMet(): bool
  {
    $playerService = new PlayerService();
    $stateService = new StateService();
    $playerLocationService = new PlayerLocationService();

    $hasOrb = $stateService->getHasOrb();
    $currentHits = $playerService->getCurrentHits();

    $hasWinItems = $hasOrb;
    $isAlive = $currentHits > 0;
    $isAtStartPoint = $playerLocationService->isAtInitialLocation();

    return $hasWinItems && $isAlive && $isAtStartPoint;
  }

}
?>