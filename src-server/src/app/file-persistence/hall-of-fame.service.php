<?php
class HallOfFameService extends BaseIOService
{
  protected string $filePath;

  public function __construct()
  {
    $this->filePath = __DIR__ . getenv("HALL_OF_FAME_FILE_PATH") . "/hall_of_fame.json";
  }


  public function addUser(
    string $name,
    string $id,
    int $sessionStartTime,
    int $sumXp,
    int $xpFromKillsPercentage,
    int $numberOfMoves,
    int $numberOfActions,
    int $characterLevel,
    string $gameVersion,
  ): string
  {
    $members = $this->read();
    $now = time();
    $member = [
      'name' => $name,
      'id' => $id,
      'started' => $sessionStartTime,
      'finished' => $now,
      'duration' => $now - $sessionStartTime,
      'sumXp' => $sumXp,
      'xpFromKillsPercentage' => $xpFromKillsPercentage,
      'numberOfMoves' => $numberOfMoves,
      'numberOfActions' => $numberOfActions,
      'characterLevel' => $characterLevel,
      'gameVersion' => $gameVersion,
    ];
    $members[] = $member;
    $this->write($members);

    return $id;
  }

  public function list(int $limit): array {
    $data = $this->read();
    return array_slice($data, 0, $limit);
  }


}
?>
