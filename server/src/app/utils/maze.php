<?php

class Maze {
  
  public function getPlayerInitialLocation(): array {
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

  public function getTilesAroundPlayer($x, $y): array {
    $maze = $this->getMaze();

    $tile00 = $this->getTile($x - 2, $y - 2);
    $tile01 = $this->getTile($x - 1, $y - 2);
    $tile02 = $this->getTile($x + 0, $y - 2);
    $tile03 = $this->getTile($x + 1, $y - 2);
    $tile04 = $this->getTile($x + 2, $y - 2);

    $tile10 = $this->getTile($x - 2, $y - 1);
    $tile11 = $this->getTile($x - 1, $y - 1);
    $tile12 = $this->getTile($x + 0, $y - 1);
    $tile13 = $this->getTile($x + 1, $y - 1);
    $tile14 = $this->getTile($x + 2, $y - 1);

    $tile20 = $this->getTile($x - 2, $y + 0);
    $tile21 = $this->getTile($x - 1, $y + 0);
    $tile22 = $this->getTile($x + 0, $y + 0);
    $tile23 = $this->getTile($x + 1, $y + 0);
    $tile24 = $this->getTile($x + 2, $y + 0);

    $tile30 = $this->getTile($x - 2, $y + 1);
    $tile31 = $this->getTile($x - 1, $y + 1);
    $tile32 = $this->getTile($x + 0, $y + 1);
    $tile33 = $this->getTile($x + 1, $y + 1);
    $tile34 = $this->getTile($x + 2, $y + 1);

    $tile40 = $this->getTile($x - 2, $y + 2);
    $tile41 = $this->getTile($x - 1, $y + 2);
    $tile42 = $this->getTile($x + 0, $y + 2);
    $tile43 = $this->getTile($x + 1, $y + 2);
    $tile44 = $this->getTile($x + 2, $y + 2);


    return [
        "tile00" => $tile00,
        "tile01" => $tile01,
        "tile02" => $tile02,
        "tile03" => $tile03,
        "tile04" => $tile04,
        "tile10" => $tile10,
        "tile11" => $tile11,
        "tile12" => $tile12,
        "tile13" => $tile13,
        "tile14" => $tile14,
        "tile20" => $tile20,
        "tile21" => $tile21,
        "tile22" => $tile22,
        "tile23" => $tile23,
        "tile24" => $tile24,
        "tile30" => $tile30,
        "tile31" => $tile31,
        "tile32" => $tile32,
        "tile33" => $tile33,
        "tile34" => $tile34,
        "tile40" => $tile40,
        "tile41" => $tile41,
        "tile42" => $tile42,
        "tile43" => $tile43,
        "tile44" => $tile44,
    ];
  }

  public function getFullMap(): array {
    return $this->getMaze();
  }

  public function mazeWidth(): int {
    $maze = $this->getMaze();
    return strlen($maze[0]);
  }

  public function mazeHeight(): int {
    $maze = $this->getMaze();
    return count($maze);
  }

  private function getTile($x, $y): string {
    $maze = $this->getMaze();

    if (!array_key_exists($y, $maze)) return "%";
    if ($x < 0 || $x >= strlen($maze[$y])) return "%";

    return $maze[$y][$x];
  }

  private function getMaze(): array {
    $mazeService = new MazeService();
    return $mazeService->getMaze();
  }

}

?>
