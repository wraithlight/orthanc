<?php

class CharacterCreationController {
  public function generate() {
    $str = roll_d8() + roll_d8();
    $int = roll_d8() + roll_d8();
    $dex = roll_d8() + roll_d8();
    $con = roll_d8() + roll_d8();
    
    $hits = divide($dex, 3) + roll_d8();

    echo json_encode([
        'stats' => [
            'str' => $str,
            'int' => $int,
            'dex' => $dex,
            'con' => $con
        ],
        'hits' => $hits
    ], JSON_PRETTY_PRINT);
  }
}
?>
