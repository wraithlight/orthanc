<?php

class ItemsState extends BaseState {

  private const ITEMS_ON_MAP_KEY = "ITEMS__ITEMS_ON_MAP";

  /**
   * @return State_Item[]
   */
  public function getItemsOnMap(): array
  {
    return $this->getItemsOnMapCore();
  }

  /**
   * @param State_Item[] $items
   * @return State_Item[]
   */
  public function setItemsOnMap(
    array $items
  ): array {
    $this->writeState(self::ITEMS_ON_MAP_KEY, $items);
    return $this->getItemsOnMapCore();
  }

  /**
   * @return State_Item[]
   */
  private function getItemsOnMapCore()
  {
    return $this->readState(self::ITEMS_ON_MAP_KEY);
  }

}
