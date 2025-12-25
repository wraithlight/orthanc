<?php

class MazeService extends BaseIOService
{
  protected string $filePath = __DIR__ . '/../../../game-data/maze.txt';

  public function getMaze(): array
  {
    $maze = $this->readText();
    return preg_split("/\r\n|\n|\r/", $maze);
  }

}

?>