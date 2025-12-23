<?php

class GameController {
  public function startGame() {
    $stateService = new StateService();
    $chatMembersService = new ChatMembersService();
    $chatMessageService = new ChatMessagesService();

    $username = $stateService->getPlayerName();
    $currentHits = $stateService->getPlayerMaxHits();

    $chatMembersService->addMember($username);
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

  private function sendBackState() {
    $stateService = new StateService();

    $nextLevelInXp = 1000; // TODO

    $sumXp = $stateService->getCharacterXp(0);
    $xpFromKills = $stateService->getCharacterXpFromKills();
    $xpFromKillsPercentage = $xpFromKills === 0 ? 0 : ($xpFromKills / $xpFromKills) * 100;

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
      "mapState" => [
        "tile00" => [
          "top" => "TILE_WALL",
          "right" => "TILE_DOOR",
          "bottom" => "TILE_OPEN",
          "left" => "TILE_OPEN",
          "occupiedBy" => [
            "key" => "",
          ],
          "containsItems" => array(
            [
              "key" => "ITEM_GOLD",
              "amount" => 10
            ]
          )
        ],
        "tile01" => [
          "top" => "TILE_WALL",
          "right" => "TILE_DOOR",
          "bottom" => "TILE_OPEN",
          "left" => "TILE_OPEN",
          "occupiedBy" => [
            "key" => "",
          ],
          "containsItems" => array(
            [
              "key" => "ITEM_GOLD",
              "amount" => 10
            ]
          )
        ],
        "tile02" => [
          "top" => "TILE_WALL",
          "right" => "TILE_DOOR",
          "bottom" => "TILE_OPEN",
          "left" => "TILE_OPEN",
          "occupiedBy" => [
            "key" => "",
          ],
          "containsItems" => array(
            [
              "key" => "ITEM_GOLD",
              "amount" => 10
            ]
          )
        ],
        "tile10" => [
          "top" => "TILE_WALL",
          "right" => "TILE_DOOR",
          "bottom" => "TILE_OPEN",
          "left" => "TILE_OPEN",
          "occupiedBy" => [
            "key" => "PLAYER",
          ],
          "containsItems" => array(
            [
              "key" => "ITEM_GOLD",
              "amount" => 10
            ]
          )
        ],
        "tile11" => [
          "top" => "TILE_WALL",
          "right" => "TILE_DOOR",
          "bottom" => "TILE_OPEN",
          "left" => "TILE_OPEN",
          "occupiedBy" => [
            "key" => "",
          ],
          "containsItems" => array(
            [
              "key" => "ITEM_GOLD",
              "amount" => 10
            ]
          )
        ],
        "tile12" => [
          "top" => "TILE_WALL",
          "right" => "TILE_DOOR",
          "bottom" => "TILE_OPEN",
          "left" => "TILE_OPEN",
          "occupiedBy" => [
            "key" => "",
          ],
          "containsItems" => array(
            [
              "key" => "ITEM_GOLD",
              "amount" => 10
            ]
          )
        ],
        "tile20" => [
          "top" => "TILE_WALL",
          "right" => "TILE_DOOR",
          "bottom" => "TILE_OPEN",
          "left" => "TILE_OPEN",
          "occupiedBy" => [
            "key" => "",
          ],
          "containsItems" => array(
            [
              "key" => "ITEM_GOLD",
              "amount" => 10
            ]
          )
        ],
        "tile21" => [
          "top" => "TILE_WALL",
          "right" => "TILE_DOOR",
          "bottom" => "TILE_OPEN",
          "left" => "TILE_OPEN",
          "occupiedBy" => [
            "key" => "",
          ],
          "containsItems" => array(
            [
              "key" => "ITEM_GOLD",
              "amount" => 10
            ]
          )
        ],
        "tile22" => [
          "top" => "TILE_WALL",
          "right" => "TILE_DOOR",
          "bottom" => "TILE_OPEN",
          "left" => "TILE_OPEN",
          "occupiedBy" => [
            "key" => "",
          ],
          "containsItems" => array(
            [
              "key" => "ITEM_GOLD",
              "amount" => 10
            ]
          )
        ],
      ]
    ]);
  }
}
?>
