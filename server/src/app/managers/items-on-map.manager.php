<?php

class ItemsOnMapManager
{
  private const GOLD_WEIGHT = 1;
  private const ORB_WEIGHT = 1000;
  private const WEIGHT_SWORD = 20;
  private const WEIGHT_SHIELD = 50;
  private const WEIGHT_ARMOR = 100;
  private const WEIGHT_BOW = 10;

  private const GOLD_PURE_PERCENTAGE_ON_MAP = 2;
  private const GOLD_CHEST_PERCENTAGE_ON_MAP = 2;
  private const NUMBER_OF_ORBS = 1;

  private $_itemsService;

  public function __construct()
  {
    $this->_itemsService = new ItemsService();
  }

  public function createItemsOnMap(
    array $tileCoordinates
  ): void {
    $numberOfTiles = count($tileCoordinates);

    $itemsInChests = [
      ...$this->getItemsInChest(),
    ];

    $chestGoldOnMap = $this->getGoldInChest(
      $this->getRandomElements(
        $this->getCountFromPercentage(self::GOLD_CHEST_PERCENTAGE_ON_MAP, $numberOfTiles),
        $tileCoordinates
      )
    );

    $pureGoldOnMap = $this->getGoldItemsOnMap(
      $this->getRandomElements(
        $this->getCountFromPercentage(self::GOLD_PURE_PERCENTAGE_ON_MAP, $numberOfTiles),
        $tileCoordinates
      )
    );

    $orb = $this->getOrbsOnMap(self::NUMBER_OF_ORBS, $tileCoordinates);
    $chests = $this->getChestsOnMap($itemsInChests, $tileCoordinates);

    $this->_itemsService->setItemsOnMap(
      [
        ...$pureGoldOnMap,
        ...$chestGoldOnMap,
        ...$orb,
        ...$chests
      ]
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
          "ITEM_CHEST_ORB",
          "item_chest",
          1,
          0,
          1 * self::ORB_WEIGHT,
          true,
          "Loot chest",
          "You picked up the Orb! Run to the entrance!",
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
    array $locations
  ): array {
    $itemsOnMap = [];
    for ($i = 0; $i < count($locations); $i++) {
      $amount = roll_d150();
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
          $locations[$i]["x"],
          $locations[$i]["y"],
        )
      );
    }
    return $itemsOnMap;
  }

  private function getGoldInChest(array $locations): array {
    $itemsOnMap = [];
    for ($i = 0; $i < count($locations); $i++) {
      $amount = roll_d150();
      array_push(
        $itemsOnMap,
        createItem(
          "ITEM_CHEST_GOLD",
          "item_chest",
          $amount,
          $amount,
          $amount * self::GOLD_WEIGHT,
          true,
          "Loot chest",
          "You picked up Gold ($amount) from the chest",
          "You see a chest",
          $locations[$i]["x"],
          $locations[$i]["y"],
        )
      );
    }
    return $itemsOnMap;
  }

  private function getItemsInChest(): array {
    return [
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
      [
        "key" => "ITEM_CHEST_BOW",
        "weight" => self::WEIGHT_BOW,
        "pickedupLabel" => "You picked up a bow from the chest."
      ],
    ];
  }

  private function getRandomElements(
    int $numberOfElements,
    array $source
  ): array {
    $indices = array_rand($source, $numberOfElements);
    $result = [];
    foreach ($indices as $index) {
        $result[] = $source[$index];
    }

    return $result;
  }

  private function getCountFromPercentage(
    int $percentage,
    int $base
  ): int {
    return $base / 100 * $percentage;
  }

}
