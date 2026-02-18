<?php

class ItemsService {

  private $_itemsState;

  public function __construct()
  {
    $this->_itemsState = new ItemsState();  
  }

  /**
   * @param State_Item[] $items
   * @return State_Item[]
   */
  public function setItemsOnMap(array $items): array {
    return $this->_itemsState->setItemsOnMap($items);
  }

  /**
   * @return State_Item[]
   */
  public function getItemsOnMap(): array {
    return $this->_itemsState->getItemsOnMap();
  }

  /**
   * @param int $x
   * @param int $y
   * @return State_Item[]
   */
  public function getItemsOnTile(int $x, int $y): array {
    $items = $this->getItemsOnMap();
    return array_values(array_filter($items, fn($m) => $m->locationX === $x && $m->locationY === $y));
  }

  /**
   * @param string $id
   * @return State_Item[]
   */
  public function getItem(string $id): object {
    $items = $this->getItemsOnMap();
    return (object)array_values(array_filter($items, fn($m) => $m->id === $id))[0];
  }

  /**
   * @param string $id
   * @return State_Item[]
   */
  public function removeItemFromMap(string $id): array {
    $items = $this->getItemsOnMap();
    $remainingItems = array_values(array_filter($items, fn($m) => $m->id !== $id));
    return $this->_itemsState->setItemsOnMap($remainingItems);
  }

}
