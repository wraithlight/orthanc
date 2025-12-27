<?php
class HallOfFameService extends BaseIOService
{
  protected string $filePath = __DIR__ . '/../../../data/hall_of_fame.json';

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
