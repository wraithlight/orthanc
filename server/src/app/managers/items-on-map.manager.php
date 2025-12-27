<?php

class ItemsOnMapManager {
  private const GOLD_WEIGHT = 1;
  private const ORB_WEIGHT = 1000;
  private const WEIGHT_SWORD = 100;
  private const WEIGHT_SHIELD = 100;
  private const WEIGHT_ARMOR = 100;

  private const GOLD_PERCENTAGE_ON_MAP = 2;
  private const NUMBER_OF_ORBS = 1;

  public function getItemsOnMap(
    array $tileCoordinates
  ): array {
    $itemsInChests = [
      [
        "key" => "ITEM_CHEST_SWORD",
        "weight" => self::WEIGHT_SWORD,
        "pickedupLabel" => "You picked up a sword from the chest."
      ],
      [
        "key" => "ITEM_CHEST_SHIELD",
        "weight" => self::WEIGHT_SHIELD,
        "pickedupLabel" => "You picked up a shield from the chest."
      ],
      [
        "key" => "ITEM_CHEST_ARMOR",
        "weight" => self::WEIGHT_ARMOR,
        "pickedupLabel" => "You picked up an armor from the chest."
      ],
    ];

    $numberOfTiles = count($tileCoordinates);
    $numberOfGoldOnMap = $numberOfTiles / 100 * self::GOLD_PERCENTAGE_ON_MAP;

    $gold = $this->getGoldItemsOnMap($numberOfGoldOnMap, $tileCoordinates);
    $orb = $this->getOrbsOnMap(self::NUMBER_OF_ORBS, $tileCoordinates);
    $chests = $this->getChestsOnMap($itemsInChests, $tileCoordinates);

    return array_merge(
      $gold,
      $orb,
      $chests
    );
  }

  /**
   * @return Item[]
   */
  private function getOrbsOnMap(
    int $number,
    array $tileCoordinates
  ): array {
    $itemsOnMap = [];
    for ($i = 0; $i < $number; $i++) {
      $randomIndex = array_rand($tileCoordinates, 1);
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
          $tileCoordinates[$randomIndex]["x"],
          $tileCoordinates[$randomIndex]["y"],
        )
      );
    }
    return $itemsOnMap;
  }

  /**
   * @return Item[]
   */
  private function getChestsOnMap(
    array $itemsInChests,
    array $tileCoordinates
  ): array {
    $itemsOnMap = [];
    for ($i = 0; $i < count($itemsInChests); $i++) {
      $randomIndex = array_rand($tileCoordinates, 1);
      array_push(
        $itemsOnMap,
        createItem(
          $itemsInChests[$i]["key"],
          "item_chest",
          1,
          0,
          $itemsInChests[$i]["weight"],
          true,
          "Loot chest",
          $itemsInChests[$i]["pickedupLabel"],
          "You see a chest",
          $tileCoordinates[$randomIndex]["x"],
          $tileCoordinates[$randomIndex]["y"],
        )
      );
    }
    return $itemsOnMap;
  }

  /**
   * @return Item[]
   */
  private function getGoldItemsOnMap(
    int $number,
    array $tileCoordinates
  ): array {
    $itemsOnMap = [];
    for ($i = 0; $i < $number; $i++) {
      $randomIndex = array_rand($tileCoordinates, 1);
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
          $tileCoordinates[$randomIndex]["x"],
          $tileCoordinates[$randomIndex]["y"],
        )
      );
    }
    return $itemsOnMap;
  }

}
