<?php
/**
 * @extends BaseMapper<HallOfFameItemDto, array>
 */
class HallOfFameItemMapper extends BaseMapper {

  public function mapDtoToModel($item): object {
    throw new ErrorException("Not impltemented!");
  }

  public function mapModelToDto($item): object {
    $dto = new HallOfFameItemDto();
    $dto->name = $item["name"];
    $dto->id = $item["id"];
    $dto->started = gmdate("Y-m-d\TH:i:s\Z", $item["started"]);
    $dto->finished = gmdate("Y-m-d\TH:i:s\Z", $item["finished"]);
    $dto->duration = $item["duration"];
    $dto->sumXp = $item["sumXp"];
    $dto->xpFromKillsPercentage = $item["xpFromKillsPercentage"];
    $dto->numberOfMoves = $item["numberOfMoves"];
    $dto->numberOfActions = $item["numberOfActions"];
    $dto->characterLevel = $item["characterLevel"];
    $dto->gameVersion = $item["gameVersion"];
    $dto->gameMode = GameMode::from($item["gameMode"]);

    return $dto;
  }

}
