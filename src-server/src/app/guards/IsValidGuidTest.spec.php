<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/is-valid-guid.php';

class IsValidGuidTest extends TestCase
{
  /**
   * @dataProvider validGuidProvider
   */
  public function testValidGuids(string $guid): void
  {
    $this->assertTrue(isGuid($guid));
  }

  /**
   * @dataProvider invalidGuidProvider
   */
  public function testInvalidGuids(string $guid): void
  {
    $this->assertFalse(isGuid($guid));
  }

  public function validGuidProvider(): array
  {
    return [
      ['123e4567-e89b-12d3-a456-426614174000'],
      ['aaaaaaaa-bbbb-1ccc-8ddd-eeeeeeeeeeee'],
      ['FFFFFFFF-FFFF-9FFF-BFFF-FFFFFFFFFFFF'],
      ['abcdef12-3456-1abc-8def-1234567890ab'],
    ];
  }

  public function invalidGuidProvider(): array
  {
    return [
      [''],
      ['123'],
      ['not-a-guid'],
      ['123e4567e89b12d3a456426614174000'],
      ['123e4567-e89b-02d3-a456-426614174000'],
      ['123e4567-e89b-12d3-c456-426614174000'],
      ['g23e4567-e89b-12d3-a456-426614174000'],
      ['123e4567-e89b-12d3-a456-42661417400'],
      ['123e4567-e89b-12d3-a456-4266141740000'],
    ];
  }
}
