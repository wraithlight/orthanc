<?php
function createNPC(
    string $key,
    string $iconName,
    int $maxHits,
    int $currentHits,
    string $attackLabel,
    int $attackMin,
    int $attackMax,
    ?Item $drops,
    string $visibleLabel,
    int $locationX,
    int $locationY,
): NPC {
  return new NPC(
    $key,
    $iconName,
    $maxHits,
    $currentHits,
    $attackLabel,
    $attackMin,
    $attackMax,
    $drops,
    $visibleLabel,
    $locationX,
    $locationY,
  );
}
?>
