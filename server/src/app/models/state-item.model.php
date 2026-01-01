<?php

class State_Item
{
  public string $id;
  public string $key;
  public string $iconName;
  public int $amount;
  public int $wealth;
  public int $sumWeight;
  public bool $canPickup;
  public string $pickupLabel;
  public string $pickedupLabel;
  public string $visibleLabel;
  public int $locationX;
  public int $locationY;

  public function __construct(
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
  ) {
    $this->id = generateGuidV4();
    $this->key = $key;
    $this->iconName = $iconName;
    $this->amount = $amount;
    $this->wealth = $wealth;
    $this->sumWeight = $sumWeight;
    $this->canPickup = $canPickup;
    $this->pickupLabel = $pickupLabel;
    $this->pickedupLabel = $pickedupLabel;
    $this->visibleLabel = $visibleLabel;
    $this->locationX = $locationX;
    $this->locationY = $locationY;
  }
}