<?php
/**
 * @extends BaseMapper<GetHallOfFameResponsePayloadItemsItem, array>
 */
class HallOfFameItemMapper extends BaseMapper {

  public function mapDtoToModel($item): object {
    throw new ErrorException("Not implemented!");
  }

  public function mapModelToDto($item): GetHallOfFameResponsePayloadItemsItem {
    $dto = new GetHallOfFameResponsePayloadItemsItem();
    $dto->name = $item["name"];
    $dto->sessionStartAtUtc = gmdate("Y-m-d\TH:i:s\Z", $item["started"]);
    $dto->sessionEndAtUtc = gmdate("Y-m-d\TH:i:s\Z", $item["finished"]);
    $dto->sessionLengthInMs = $item["duration"];
    $dto->experiencePoints = $item["sumXp"];
    $dto->experienceFromKillsPercentage = $item["xpFromKillsPercentage"];
    $dto->numberOfMoves = $item["numberOfMoves"];
    $dto->numberOfActions = $item["numberOfActions"];
    $dto->characterLevel = $item["characterLevel"];
    $dto->gameVersion = $item["gameVersion"];
    $dto->gameMode = GetHallOfFameResponsePayloadItemsItemGameMode::from($item["gameMode"]);

    return $dto;
  }

}
