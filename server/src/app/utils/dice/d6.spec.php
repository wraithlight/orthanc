<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/d6.php';

class D6 extends TestCase
{
  /**
   * @testdox roll_d6() returns an int between 1 and 6
   */
  public function testRollD6ReturnsIntBetween1And6(): void
  {
    for ($i = 0; $i < 100; $i++) {
      $result = roll_d6();

      $this->assertIsInt($result);
      $this->assertGreaterThanOrEqual(1, $result);
      $this->assertLessThanOrEqual(6, $result);
    }
  }
}
