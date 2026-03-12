<?php

/**
 * @template TDto
 * @template TModel
 */
abstract class BaseMapper
{
  /**
    * @param TModel $item
    * @return TDto
    */

  abstract public function mapModelToDto($item);
  /**
    * @param TModel[] $items
    * @return TDto[]
    */

  public function mapListToDto(array $items): array {
    return array_map(fn($o) => $this->mapModelToDto($o), $items);
  }

  /**
    * @param TDto $item
    * @return TModel
    */
  abstract public function mapDtoToModel($item);

  /**
    * @param TDto[] $items
    * @return TModel[]
    */
  public function mapListToModel(array $items): array {
    return array_map(fn($o) => $this->mapDtoToModel($o), $items);
  }
}
