<?php
/**
 * @extends BaseMapper<HallOfFameItemDto, array>
 */
class HallOfFameItemMapper extends BaseMapper {

  public function mapDtoToModel($item): object {
    throw new ErrorException("Not implemented!");
  }

  public function mapModelToDto($item): object {
    $dto = new HallOfFameItemDto();
    $dto->name = $item["name"];
    $dto->id = $item["id"];
    $dto->sessionStartAtUtc = gmdate("Y-m-d\TH:i:s\Z", $item["started"]);
    $dto->sessionEndAtUtc = gmdate("Y-m-d\TH:i:s\Z", $item["finished"]);
    $dto->sessionLengthInMs = $item["duration"];
    $dto->experiencePoints = $item["sumXp"];
    $dto->experienceFromKillsPercentage = $item["xpFromKillsPercentage"];
    $dto->numberOfMoves = $item["numberOfMoves"];
    $dto->numberOfActions = $item["numberOfActions"];
    $dto->characterLevel = $item["characterLevel"];
    $dto->gameVersion = $item["gameVersion"];
    $dto->gameMode = GameMode::from($item["gameMode"]);

    return $dto;
  }

}
