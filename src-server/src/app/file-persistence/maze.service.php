<?php

class MazeService extends BaseIOService
{
  protected string $filePath;
  public function __construct() {
    $this->filePath = __DIR__ . getenv("MAZE_FILE_PATH") . "/maze.txt";
  }

  public function getMaze(): array
  {
    $maze = $this->readText();
    return preg_split("/\r\n|\n|\r/", $maze);
  }

}

?>