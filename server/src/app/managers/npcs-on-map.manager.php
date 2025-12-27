<?php

class NPCsOnMapManager {
  private const NPC_PERCENTAGE_ON_MAP = 2;

  /**
   * @return NPC[]
   */
  public function getNPCsOnMap(
    array $tileCoordinates
  ): array {
    $npcsOnMap = [];
    $minDamage = 3;
    $maxDamage = 5;
    $maxHits = 12;
    $currentHits = 12;
    array_push(
      $npcsOnMap,
      createNPC(
        "NPC_RAT",
        "npc_rat",
        $maxHits,
        $currentHits,
        "The rat bites you!",
        $minDamage,
        $maxDamage,
        null,
        "You see a giant sewer rat. It has $currentHits hits. It can hit you from $minDamage to $maxDamage.",
        2,
        1
      )
    );

    return $npcsOnMap;
  }

}
