<?php

class NPC
{
  public string $id;
  public string $key;
  public string $iconName;
  public int $maxHits;
  public int $currentHits;
  public string $attackLabel;
  public int $attackMin;
  public int $attackMax;
  public ?Item $drops;
  public string $visibleLabel;
  public int $locationX;
  public int $locationY;

  public function __construct(
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
  ) {
    $this->id = generateGuidV4();
    $this->key = $key;
    $this->iconName = $iconName;
    $this->maxHits = $maxHits;
    $this->currentHits = $currentHits;
    $this->attackLabel = $attackLabel;
    $this->attackMin = $attackMin;
    $this->attackMax = $attackMax;
    $this->drops = $drops;
    $this->visibleLabel = $visibleLabel;
    $this->locationX = $locationX;
    $this->locationY = $locationY;
  }
}
?>