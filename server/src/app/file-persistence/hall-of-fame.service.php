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
    int $startTime
  ): string
  {
    $members = $this->read();
    $member = [
      'name' => $name,
      'id' => $id,
      'started' => $startTime,
      'finished' => time()
    ];
    $members[] = $member;
    $this->write($members);

    return $id;
  }


}
?>
