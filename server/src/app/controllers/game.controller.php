<?php

class GameController
{
  private $sessionManager;

  public function __construct()
  {
    $this->sessionManager = new SessionManager();
  }

  private const GOLD_WEIGHT = 1;

  public function startGame()
  {
    $id = $this->sessionManager->authenticate();

    $stateService = new StateService();
    $chatMembersService = new ChatMembersService();
    $chatMessageService = new ChatMessagesService();
    $itemsOnMapManager = new ItemsOnMapManager();
    $maze = new Maze();

    $initialXp = 0;
    $initialLevel = $this->getLevel($initialXp);
    $maxSpellUnits = $this->getMaxSpellUnits($initialLevel);

    $username = $stateService->getPlayerName();
    $location = $maze->getPlayerInitialLocation();
    $currentHits = $stateService->getPlayerMaxHits();

    $chatMembersService->addMember($username, $id);
    $stateService->setPlayerPosition($location["x"], $location["y"]);
    $stateService->setPlayerPreviousPosition($location["x"], $location["y"]);
    $stateService->setPlayerCurHits($currentHits);
    $stateService->setChatLastMessageId($chatMessageService->getLastMessageId());
    $stateService->setEquipmentSword("NORMAL");
    $stateService->setEquipmentShield("NORMAL");
    $stateService->setEquipmentArmor("NORMAL");
    $stateService->setEquipmentArrows(0);
    $stateService->setCharacterXp($initialXp);
    $stateService->setCharacterXpFromKills(0);
    $stateService->setCharacterStatsMoney(0);
    $stateService->setCharacterStatsWeight(0);
    $stateService->setCharacterSpellUnitsMax($maxSpellUnits);
    $stateService->setCharacterSpellUnitsCur($maxSpellUnits);
    $stateService->setHasOrb(false);
    $stateService->setCharacterSpellsOn([]);
    $stateService->setMapFull($maze->getFullMaze());
    $stateService->setStartTime(time());
    $stateService->setFeedbackEvents([]);

    $walkableTiles = $maze->getWalkableTiles();
    $itemsOnMap = $itemsOnMapManager->getItemsOnMap($walkableTiles);

    $stateService->setItems($itemsOnMap);
    $this->sendBackState();
  }

  public function onAction()
  {
    $this->sessionManager->authenticate();

    $stateService = new StateService();
    $actionsManager = new ActionsManager();

    $rawBody = file_get_contents('php://input');
    $payload = json_decode($rawBody, true);
    $action = $payload['action'];
    $target = array_key_exists('payload', $payload) ? $payload['payload'] : null;

    $location = $stateService->getPlayerPosition();
    $tiles = $this->calculateTiles($location['x'], $location['y']);
    $possibleActions = $actionsManager->getPossibleActions($tiles, $this->getGameState());
    $canDo = $actionsManager->canDoAction($possibleActions, $action, $target);

    $feedbackEvents = [];
    if ($canDo) {
      $actionsManager->handleAction($action, $target);
      switch ($action) {
        case "RUN": {
          $isFailed = rand(0, 1) == 1;
          if ($isFailed) {
            array_push(
              $feedbackEvents,
              [
                "key" => "ACTION_RUN_FAIL",
                "label" => "Your legs tremble with fear; you find yourself unable to flee this mysterious form of evil."
              ]
            );
            break;
          } else {
            array_push(
              $feedbackEvents,
              [
                "key" => "ACTION_RUN_SUCCESS",
                "label" => "You flee into the choking dark, the malignant presence fading behind you as the shadows swallow your escape."
              ]
            );
          }
          $previousPosition = $stateService->getPlayerPreviousPosition();
          $stateService->setPlayerPreviousPosition($location["x"], $location["y"]);
          $stateService->setPlayerPosition($previousPosition["x"], $previousPosition["y"]);
          break;
        }
        case "PICKUP": {
          $currentItem = array_values(array_filter($this->getItemsOnTile($location["x"], $location["y"]), fn($m) => $m->id === $target))[0];

          $currentWeight = $stateService->getCharacterStatsWeight();
          $stateService->setCharacterStatsWeight($currentWeight + $currentItem->sumWeight);
          $itemsOnMap = array_values(array_filter($stateService->getItems(), fn($m) => $m->id !== $target));
          $stateService->setItems($itemsOnMap);
          $currentGold = $stateService->getCharacterStatsMoney();
          $stateService->setCharacterStatsMoney($currentGold + $currentItem->wealth);

          if ($currentItem->key === "ITEM_CHEST_ORB") {
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

          array_push($feedbackEvents, [
            "key" => "ITEM_PICKUP",
            "label" => $currentItem->pickedupLabel
          ]);
        }
      }
    }

    $stateService->setFeedbackEvents($feedbackEvents);

    $this->sendBackState();
  }

  private function sendBackState()
  {
    $actionsManager = new ActionsManager();

    $maze = new Maze();
    $stateService = new StateService();
    $hallOfFameService = new HallOfFameService();

    $sumXp = $stateService->getCharacterXp();
    $xpFromKills = $stateService->getCharacterXpFromKills();
    $xpFromKillsPercentage = $xpFromKills === 0 ? 0 : ($xpFromKills / $sumXp) * 100;

    $location = $stateService->getPlayerPosition();

    $tiles = $this->calculateTiles($location['x'], $location['y']);

    $isAtStartPoint = $maze->getPlayerInitialLocation() === $location;

    if ($isAtStartPoint) {
      $maxHp = $stateService->getPlayerMaxHits();
      $maxHp > 0 ?? $stateService->setPlayerCurHits($maxHp);

      $money = $stateService->getCharacterStatsMoney();
      $weight = $stateService->getCharacterStatsMoney();

      $stateService->setCharacterStatsMoney(0);
      $stateService->setCharacterStatsWeight($weight - $money * self::GOLD_WEIGHT);

      $newXp = $sumXp + $money;
      $stateService->setCharacterXp($newXp);

      $level = $this->getLevel($newXp);
      $spellUnits = $this->getMaxSpellUnits($level);
      $stateService->setCharacterSpellUnitsCur($spellUnits);
      $stateService->setCharacterSpellUnitsMax($spellUnits);
    }

    $gameState = $this->getGameState();

    if ($gameState === GameState::EndSuccess) {
      $hallOfFameService->addUser(
        $stateService->getPlayerName(),
        $this->sessionManager->getSessionId(),
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

    $xp = $stateService->getCharacterXp();
    $level = $this->getLevel($xp);

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
          "experience" => $xp,
          "money" => $stateService->getCharacterStatsMoney(),
          "nextLevelInXp" => $this->getXpForNextLevel($level) - $xp,
          "weight" => $stateService->getCharacterStatsWeight(),
          "playerLevel" => $level,
          "spellUnits" => [
            "current" => $stateService->getCharacterSpellUnitsCur(),
            "maximum" => $stateService->getCharacterSpellUnitsMax()
          ],
          "xpPercentageFromKills" => $xpFromKillsPercentage
        ],
        "availableSpells" => [
          "level_1" => [
            [
              "displayName" => "Levitation",
              "action" => [
                "key" => "CAST",
                "payload" => "b77cc1a0-91ec-4d64-bb6d-21717737ea3c",
                "isClientSideOnly" => false
              ]
            ]
          ],
          "level_2" => [
            [
              "displayName" => "Invisibility",
              "action" => [
                "key" => "CAST",
                "payload" => "9b3ea5f2-e43b-44d0-83f3-e2d97dfff065",
                "isClientSideOnly" => false
              ]
            ]
          ],
          "level_3" => [
            [
              "displayName" => "Immortality",
              "action" => [
                "key" => "CAST",
                "payload" => "bdf61c73-d9e3-41e2-b05d-4433caf2e650",
                "isClientSideOnly" => false
              ]
            ]
          ],
          "level_4" => [
            [
              "displayName" => "Teleportation",
              "action" => [
                "key" => "CAST",
                "payload" => "a012e1e0-59ad-4f0c-b230-d911d91086cd",
                "isClientSideOnly" => false
              ]
            ]
          ]
        ],
        "events" => $this->getEvents($tiles),
        "possibleActions" => $actionsManager->getPossibleActions($tiles, $this->getGameState()),
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
    $stateService = new StateService();
    $events = $stateService->getFeedbackEvents();

    $visibleItems = array_merge(...array_values(array_map(fn($m) => $m['containsItems'], $tiles)));

    foreach ($visibleItems as $item) {
      array_push($events, [
        "key" => "ITEM_SEE",
        "label" => $item->visibleLabel
      ]);
    }

    return $events;
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
    $stateService = new StateService();
    $currentHits = $stateService->getPlayerCurHits();
    $isAlive = $currentHits <= 0;
    return $isAlive;
  }

  private function areWinConditionsMet(): bool
  {
    $maze = new Maze();
    $stateService = new StateService();

    $hasOrb = $stateService->getHasOrb();
    $currentHits = $stateService->getPlayerCurHits();
    $currentLocation = $stateService->getPlayerPosition();
    $initialLocation = $maze->getPlayerInitialLocation();

    $hasWinItems = $hasOrb;
    $isAlive = $currentHits > 0;
    $isAtStartPoint = $currentLocation === $initialLocation;

    return $hasWinItems && $isAlive && $isAtStartPoint;
  }

  private function getXpForNextLevel(
    int $currentLevel
  ): int {
    $THRESHOLD_LEVEL = 8;
    $THRESHOLD_XP = 128000;

    $targetLevel = $currentLevel + 1;
    if ($targetLevel > $THRESHOLD_LEVEL) {
      $levelsAbove = $targetLevel - $THRESHOLD_LEVEL;
      return $levelsAbove * $THRESHOLD_XP;
    } else {
      return pow(2, $currentLevel) * 500;
    }
  }

  private function getLevel(int $xp): int
  {
    $THRESHOLD_LEVEL = 8;
    $THRESHOLD_XP = 128000;

    if ($xp > $THRESHOLD_XP) {
      $levelsAbove = (int) floor($xp / $THRESHOLD_XP);
      return $levelsAbove + $THRESHOLD_LEVEL;
    } else {
      $lvlBase = floor($xp / 500);
      return $lvlBase < 1
        ? 1
        : floor(log($lvlBase, 2)) + 1
      ;
    }
  }

  private function getMaxSpellUnits(
    int $level
  ): int {
    return ($level + 1) * 2;
  }

}
?>