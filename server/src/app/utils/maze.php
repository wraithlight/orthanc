<?php

class Maze
{

  public function getPlayerInitialLocation(): array
  {
    $maze = $this->getMaze();
    $row = 0;
    for ($i = 0; $i <= count($maze); $i++) {
      if (str_contains($maze[$i], "@")) {
        $row = $i;
        break;
      }
    }
    $col = strpos($maze[$row], "@");
    return [
      "x" => $col,
      "y" => $row
    ];
  }

  public function getWalkableTiles(): array
  {
    $walkableTiles = [];
    $maze = $this->getMaze();
    for ($y = 0; $y < count($maze); $y++) {
      for ($x = 0; $x < strlen($maze[$y]); $x++) {
        if ($this->getTile($x, $y) === ".") {
          array_push($walkableTiles, ["x" => $x, "y" => $y]);
        }
      }
    }
    return $walkableTiles;
  }

  public function mazeWidth(): int
  {
    $maze = $this->getMaze();
    return strlen($maze[0]);
  }

  public function mazeHeight(): int
  {
    $maze = $this->getMaze();
    return count($maze);
  }

  public function getTile($x, $y): string
  {
    $maze = $this->getMaze();

    if (!array_key_exists($y, $maze))
      return "%";
    if ($x < 0 || $x >= strlen($maze[$y]))
      return "%";

    return $maze[$y][$x];
  }

  public function getFullMaze(): array {
    $maze = $this->getMaze();
    $map = array_map(
      fn($row) => array_map(
          fn($char) => (object)[
            "tileChar" => $char,
            "visited" => false
          ],
          str_split($row)
      ),
      $maze
    );
    return $map;
  }

  private function getMaze(): array
  {
    $mazeService = new MazeService();
    return $mazeService->getMaze();
  }

}

?>