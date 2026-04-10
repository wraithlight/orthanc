<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/d8.php';

class D8 extends TestCase
{
  /**
   * @testdox roll_d8() returns an int between 1 and 6
   */
  public function testRollD8ReturnsIntBetween1And8(): void
  {
    for ($i = 0; $i < 100; $i++) {
      $result = roll_d8();

      $this->assertIsInt($result);
      $this->assertGreaterThanOrEqual(1, $result);
      $this->assertLessThanOrEqual(8, $result);
    }
  }
}
