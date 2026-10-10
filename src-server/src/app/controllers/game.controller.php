<?php

class GameController
{
  private $_actionsManager;
  private $_chatManager;
  private $_hallOfFameManager;
  private $_itemsOnMapManager;
  private $_sessionManager;

  public function __construct()
  {
    $this->_actionsManager = new ActionsManager();
    $this->_chatManager = new ChatManager();
    $this->_hallOfFameManager = new HallOfFameManager();
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
    $stateService->setHasGrail(false);
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
    $sessionService = new SessionService();
    $levelService = new LevelService();
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

    $playerName = $sessionService->getPlayerName();
    if ($gameState === GameState::EndSuccess && $previousGameState !== GameState::EndSuccess) {
      $this->_hallOfFameManager->addUser(
        $this->_sessionManager->getSessionId(),
        $stateService->getStartTime()
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
          $minimapState[$y][$x] = PostGameActionResponsePayloadMinimapStateItemItem::Unknown;
        } elseif ($tile->tileChar === "%" || $tile->tileChar === ".") {
          $minimapState[$y][$x] = PostGameActionResponsePayloadMinimapStateItemItem::Empty;
        } else {
          $minimapState[$y][$x] = PostGameActionResponsePayloadMinimapStateItemItem::Wall;
        }
      }
    }

    $minimapState[$location->coordY][$location->coordX] = "PLAYER";
    $stateService->setPreviousGameState($gameState);

    $mapSize = min($mapHeight, $mapWidth);

    $mapSizeResult = new PostGameActionResponsePayloadMapSize();
    $mapSizeResult->width = $mapSize;
    $mapSizeResult->height = $mapSize;

    $characterResult = new PostGameActionResponsePayloadCharacter();
    $characterResult->dexterity = $stateService->getPlayerDexterity();
    $characterResult->intelligence = $stateService->getPlayerIntelligence();
    $characterResult->strength = $stateService->getPlayerStrength();
    $characterResult->constitution = $stateService->getPlayerConstitution();

    $equipmentResult = new PostGameActionResponsePayloadEquipment();
    $equipmentResult->sword = PostGameActionResponsePayloadEquipmentSword::from($stateService->getEquipmentSword());
    $equipmentResult->shield = PostGameActionResponsePayloadEquipmentShield::from($stateService->getEquipmentShield());
    $equipmentResult->armor = PostGameActionResponsePayloadEquipmentArmor::from($stateService->getEquipmentArmor());
    $equipmentResult->arrows = $stateService->getEquipmentArrows();
    $equipmentResult->bow = PostGameActionResponsePayloadEquipmentBow::from($stateService->getEquipmentBow());

    $spellUnitsResult = new PostGameActionResponsePayloadStatisticsSpellUnits();
    $spellUnitsResult->current = $playerService->getCurrentSpellUnits();
    $spellUnitsResult->maximum = $levelService->getMaxSpellUnits();

    $statisticsResult = new PostGameActionResponsePayloadStatistics();
    $statisticsResult->experience = $levelService->getCurrentXp();
    $statisticsResult->money = $stateService->getCharacterStatsMoney();
    $statisticsResult->nextLevelInXp = $levelService->getXpForNextLevel();
    $statisticsResult->weight = $stateService->getCharacterStatsWeight();
    $statisticsResult->playerLevel = $levelService->getLevel();
    $statisticsResult->spellUnits = $spellUnitsResult;
    $statisticsResult->xpPercentageFromKills = $levelService->getXpPercentageFromKills();

    $eventsResult = array_map(function($event) {
      $item = new PostGameActionResponsePayloadEventsItem();
      $item->key = PostGameActionResponsePayloadEventsItemKey::from($event->key);
      $item->label = $event->label;
      return $item;
    }, $this->getEvents(($tiles)));

    $activeSpellsResult = array_map(function($spell) {
      $item = new PostGameActionResponsePayloadActiveSpellsItem();
      $item->key = $spell->key;
      $item->label = $spell->label;
      $item->remaining = $spell->remaining;
      return $item;
    }, $stateService->getCharacterSpellsOn());

    $possibleActionsResult = array_map(function($action) {
      $item = new PostGameActionResponsePayloadPossibleActionsItem();
      $item->key = PostGameActionResponsePayloadPossibleActionsItemKey::from($action['key']);
      $item->label = $action['label'];
      $item->payload = $action['payload'] ?? '';
      return $item;
    }, $this->_actionsManager->getPossibleActions($tiles, $gameState));

    $availableSpell1Result = new PostGameActionResponsePayloadAvailableSpellsLevel1();
    $availableSpell1Result->displayName = "Levitation";
    $availableSpell1ResultAction = new PostGameActionResponsePayloadAvailableSpellsLevel1Action();
    $availableSpell1ResultAction->key = PostGameActionResponsePayloadAvailableSpellsLevel1ActionKey::Cast;
    $availableSpell1ResultAction->payload = "b77cc1a0-91ec-4d64-bb6d-21717737ea3c";
    $availableSpell1Result->action = $availableSpell1ResultAction;

    $availableSpell2Result = new PostGameActionResponsePayloadAvailableSpellsLevel2();
    $availableSpell2Result->displayName = "Invisibility";
    $availableSpell2ResultAction = new PostGameActionResponsePayloadAvailableSpellsLevel2Action();
    $availableSpell2ResultAction->key = PostGameActionResponsePayloadAvailableSpellsLevel2ActionKey::Cast;
    $availableSpell2ResultAction->payload = "9b3ea5f2-e43b-44d0-83f3-e2d97dfff065c";
    $availableSpell2Result->action = $availableSpell2ResultAction;

    $availableSpell3Result = new PostGameActionResponsePayloadAvailableSpellsLevel3();
    $availableSpell3Result->displayName = "Immortality";
    $availableSpell3ResultAction = new PostGameActionResponsePayloadAvailableSpellsLevel3Action();
    $availableSpell3ResultAction->key = PostGameActionResponsePayloadAvailableSpellsLevel3ActionKey::Cast;
    $availableSpell3ResultAction->payload = "bdf61c73-d9e3-41e2-b05d-4433caf2e650";
    $availableSpell3Result->action = $availableSpell3ResultAction;

    $availableSpell4Result = new PostGameActionResponsePayloadAvailableSpellsLevel4();
    $availableSpell4Result->displayName = "Teleportation";
    $availableSpell4ResultAction = new PostGameActionResponsePayloadAvailableSpellsLevel4Action();
    $availableSpell4ResultAction->key = PostGameActionResponsePayloadAvailableSpellsLevel4ActionKey::Cast;
    $availableSpell4ResultAction->payload = "a012e1e0-59ad-4f0c-b230-d911d91086cd";
    $availableSpell4Result->action = $availableSpell4ResultAction;

    $availableSpellsResult = new PostGameActionResponsePayloadAvailableSpells();
    $availableSpellsResult->level_1 = $availableSpell1Result;
    $availableSpellsResult->level_2 = $availableSpell2Result;
    $availableSpellsResult->level_3 = $availableSpell3Result;
    $availableSpellsResult->level_4 = $availableSpell4Result;

    $mapStateResult = array_map(function ($m) {
      $item = new PostGameActionResponsePayloadMapStateItem();
      $item->top = PostGameActionResponsePayloadMapStateItemTop::from($m["top"]);
      $item->right = PostGameActionResponsePayloadMapStateItemRight::from($m["right"]);
      $item->bottom = PostGameActionResponsePayloadMapStateItemBottom::from($m["bottom"]);
      $item->left = PostGameActionResponsePayloadMapStateItemLeft::from($m["left"]);
      $item->occupiedBy = $this->mapOccupiedBy($m["occupiedBy"]);
      $item->containsItems = $this->itemToItemDto($m["containsItems"]);
      return $item;
    }, $tiles);

    $result = new PostGameActionResponsePayload();
    // primitives
    $result->playerName = $playerName;
    $result->hits = $playerService->getCurrentHits();
    $result->maxHits = $playerService->getMaxHits();
    $result->minimapState = $minimapState;
    // complex mapped
    $result->mapSize = $mapSizeResult;
    $result->character = $characterResult;
    $result->equipment = $equipmentResult;
    $result->statistics = $statisticsResult;
    $result->events = $eventsResult;
    $result->activeSpells = $activeSpellsResult;
    $result->possibleActions = $possibleActionsResult;
    $result->availableSpells = $availableSpellsResult;
    $result->mapState = $mapStateResult;
    // callees
    $result->gameState = $this->mapGameState($gameState);
    $result->endgameItems = $this->getEndgameItems();

    echo json_encode(createSuccessResponse($result));
  }

  private function mapOccupiedBy($occupiedBy): PostGameActionResponsePayloadMapStateItemOccupiedBy | null {
    if (is_null($occupiedBy)) return null;
    $result = new PostGameActionResponsePayloadMapStateItemOccupiedBy();
    $result->key = PostGameActionResponsePayloadMapStateItemOccupiedByKey::from($occupiedBy->key);
    return $result;
  }

  private function mapGameState(GameState $gameState): PostGameActionResponsePayloadGameState {
    if ($gameState === GameState::EndFail) return PostGameActionResponsePayloadGameState::GameEndFail;
    if ($gameState === GameState::EndSuccess) return PostGameActionResponsePayloadGameState::GameEndSuccess;
    return PostGameActionResponsePayloadGameState::GameRunning;
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
    $playerService = new PlayerService();
    if ($playerService->isDead()) {
      return GameState::EndFail;
    }
    if ($this->areWinConditionsMet()) {
      return GameState::EndSuccess;
    }
    return GameState::Running;
  }

  private function areWinConditionsMet(): bool
  {
    $playerService = new PlayerService();
    $stateService = new StateService();
    $playerLocationService = new PlayerLocationService();

    $hasOrb = $stateService->getHasOrb();
    $hasGrail = $stateService->getHasGrail();

    $hasWinItems = $hasOrb && $hasGrail;
    $isAlive = $playerService->isAlive();
    $isAtStartPoint = $playerLocationService->isAtInitialLocation();

    return $hasWinItems && $isAlive && $isAtStartPoint;
  }

  /**
   * @return list<PostGameActionResponsePayloadEndgameItemsItem>
   */
  private function getEndgameItems(): array {
    $endgameItems = [];

    $stateService = new StateService();
    $hasOrb = $stateService->getHasOrb();
    $hasGrail = $stateService->getHasGrail();

    if ($hasOrb) {
      $orbItem = new PostGameActionResponsePayloadEndgameItemsItem();
      $orbItem->key = PostGameActionResponsePayloadEndgameItemsItemKey::ItemOrb;
      $orbItem->iconName = PostGameActionResponsePayloadEndgameItemsItemIconName::ItemOrb;
      array_push($endgameItems, $orbItem);
    }

    if ($hasGrail) {
      $grailItem = new PostGameActionResponsePayloadEndgameItemsItem();
      $grailItem->key = PostGameActionResponsePayloadEndgameItemsItemKey::ItemGrail;
      $grailItem->iconName = PostGameActionResponsePayloadEndgameItemsItemIconName::ItemGrail;
      array_push($endgameItems, $grailItem);
    }

    return $endgameItems;
  }

}
