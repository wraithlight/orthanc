<?php
function createItem(
  string $key,
  string $iconName,
  int $amount,
  int $wealth,
  int $sumWeight,
  bool $canPickup,
  string $pickupLabel,
  string $pickedupLabel,
  string $visibleLabel,
  int $locationX,
  int $locationY
): Item {
  return new Item(
    $key,
    $iconName,
    $amount,
    $wealth,
    $sumWeight,
    $canPickup,
    $pickupLabel,
    $pickedupLabel,
    $visibleLabel,
    $locationX,
    $locationY,
  );
}
?>
