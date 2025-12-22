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

    $this->sendBackState();
  }

  public function onAction() {
    $this->sendBackState();
  }

  private function sendBackState() {
    $stateService = new StateService();
    echo json_encode([
      "character" => [
        "dexterity" => $stateService->getPlayerDexterity(),
        "intelligence" => $stateService->getPlayerIntelligence(),
        "strength" => $stateService->getPlayerStrength(),
        "constitution" => $stateService->getPlayerConstitution()
      ],
      "hits" => $stateService->getPlayerCurHits(),
      "maxHits" => $stateService->getPlayerMaxHits(),
      "activeSpells" => array(
        [
          "key" => "SPELL_01",
          "label" => "Levitation"
        ],
        [
          "key" => "SPELL_02",
          "label" => "Invisibility"
        ],
      ),
      "equipment" => [
        "sword" => "SWORD_NORMAL",
        "shield" => "SHIELD_NORMAL",
        "armor" => "ARMOR_NORMAL",
        "arrows" => 0
      ],
      "statistics" => [
        "experience" => 666,
        "money" => 555,
        "nextLevelInXp" => 999,
        "weight" => 10,
        "playerLevel" => 1,
        "spellUnits" => [
          "current" => 4,
          "maximum" => 4
        ],
        "xpPercentageFromKills" => 10
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
