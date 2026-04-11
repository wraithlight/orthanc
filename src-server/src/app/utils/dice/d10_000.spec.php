<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/d10_000.php';

class D10_000 extends TestCase
{
  /**
   * @testdox roll_d10000() returns an int between 1 and 10000
   */
  public function testRoll10000ReturnsIntBetween1And10000(): void
  {
    for ($i = 0; $i < 100; $i++) {
      $result = roll_d10k();

      $this->assertIsInt($result);
      $this->assertGreaterThanOrEqual(1, $result);
      $this->assertLessThanOrEqual(10000, $result);
    }
  }
}
