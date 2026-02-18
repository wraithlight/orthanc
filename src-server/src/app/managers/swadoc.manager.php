<?php

use function PHPUnit\Framework\returnArgument;

class SwadocManager
{

  private $_swadocService;

  public function __construct()
  {
    $this->_swadocService = new SwadocService();
  }

  public function getSwadocContent(): string {
    $swadocArray = $this->_swadocService->getSwadocFileContent();
    return $swadocArray;
  }

}
