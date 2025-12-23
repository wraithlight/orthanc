<?php

class GameController {
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
    $this->sendBackState();
  }

  public function maze() {
    $maze = new Maze();

    $map = $maze->getFullMap();
    $location = $maze->getPlayerInitialLocation();
    $tilesAround = $maze->getTilesAroundPlayer($location['x'], $location['y']);

    echo json_encode([
      "location" => $location,
      "tilesAround" => $tilesAround,
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

    echo json_encode([
      "__debug" => $_SESSION,
      "character" => [
        "dexterity" => $stateService->getPlayerDexterity(),
        "intelligence" => $stateService->getPlayerIntelligence(),
        "strength" => $stateService->getPlayerStrength(),
        "constitution" => $stateService->getPlayerConstitution()
      ],
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
      "possibleActions" => array(
        [
          "key" => "ACTION_FIGHT",
          "label" => "[F]ight"
        ],
        [
          "key" => "ACTION_RUN",
          "label" => "[R]un"
        ],
        [
          "key" => "ACTION_CAST",
          "label" => "[C]ast a spell"
        ]
      ),
      "mapState" => $this->calculateTiles($tilesAround)
    ]);
  }

  private function calculateTiles(
    $currentTiles
  ): array {
    return [
      "tile00" => [
          "top" => $this->getBorderType($currentTiles["tile01"]),
          "right" => $this->getBorderType($currentTiles["tile12"]),
          "bottom" => $this->getBorderType($currentTiles["tile21"]),
          "left" => $this->getBorderType($currentTiles["tile12"]),
          "occupiedBy" => null,
          "containsItems" => array()
        ],
        "tile01" => [
          "top" => $this->getBorderType($currentTiles["tile02"]),
          "right" => $this->getBorderType($currentTiles["tile13"]),
          "bottom" => $this->getBorderType($currentTiles["tile22"]),
          "left" => $this->getBorderType($currentTiles["tile11"]),
          "occupiedBy" => null,
          "containsItems" => array()
        ],
        "tile02" => [
          "top" => $this->getBorderType($currentTiles["tile03"]),
          "right" => $this->getBorderType($currentTiles["tile14"]),
          "bottom" => $this->getBorderType($currentTiles["tile23"]),
          "left" => $this->getBorderType($currentTiles["tile12"]),
          "occupiedBy" => null,
          "containsItems" => array()
        ],
        "tile10" => [
          "top" => $this->getBorderType($currentTiles["tile11"]),
          "right" => $this->getBorderType($currentTiles["tile22"]),
          "bottom" => $this->getBorderType($currentTiles["tile31"]),
          "left" => $this->getBorderType($currentTiles["tile20"]),
          "occupiedBy" => null,
          "containsItems" => array()
        ],
        "tile11" => [
          "top" => $this->getBorderType($currentTiles["tile12"]),
          "right" => $this->getBorderType($currentTiles["tile23"]),
          "bottom" => $this->getBorderType($currentTiles["tile32"]),
          "left" => $this->getBorderType($currentTiles["tile21"]),
          "occupiedBy" => [
            "key" => "PLAYER",
          ]
        ],
        "tile12" => [
          "top" => $this->getBorderType($currentTiles["tile13"]),
          "right" => $this->getBorderType($currentTiles["tile24"]),
          "bottom" => $this->getBorderType($currentTiles["tile33"]),
          "left" => $this->getBorderType($currentTiles["tile22"]),
          "occupiedBy" => null,
          "containsItems" => array()
        ],
        "tile20" => [
          "top" => $this->getBorderType($currentTiles["tile21"]),
          "right" => $this->getBorderType($currentTiles["tile32"]),
          "bottom" => $this->getBorderType($currentTiles["tile41"]),
          "left" => $this->getBorderType($currentTiles["tile30"]),
          "occupiedBy" => null,
          "containsItems" => array()
        ],
        "tile21" => [
          "top" => $this->getBorderType($currentTiles["tile22"]),
          "right" => $this->getBorderType($currentTiles["tile33"]),
          "bottom" => $this->getBorderType($currentTiles["tile42"]),
          "left" => $this->getBorderType($currentTiles["tile31"]),
          "occupiedBy" => null,
          "containsItems" => array()
        ],
        "tile22" => [
          "top" => $this->getBorderType($currentTiles["tile23"]),
          "right" => $this->getBorderType($currentTiles["tile34"]),
          "bottom" => $this->getBorderType($currentTiles["tile43"]),
          "left" => $this->getBorderType($currentTiles["tile32"]),
          "occupiedBy" => null,
          "containsItems" => array()
        ]
      ];
  }

  private function getBorderType($targetTile): string {
    if ($targetTile === "%") return "TILE_OPEN";
    if ($targetTile === "#") return "TILE_WALL";
    return "TILE_OPEN";
  }
}
?>
