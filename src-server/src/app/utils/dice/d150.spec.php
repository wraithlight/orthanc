<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/d150.php';

class D150 extends TestCase
{
  /**
   * @testdox roll_d150() returns an int between 1 and 150
   */
  public function testRollD150ReturnsIntBetween1And150(): void
  {
    for ($i = 0; $i < 100; $i++) {
      $result = roll_d150();

      $this->assertIsInt($result);
      $this->assertGreaterThanOrEqual(1, $result);
      $this->assertLessThanOrEqual(150, $result);
    }
  }
}
