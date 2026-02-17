<?php

class SwadocController
{
  private $_swadocManager;  

  public function __construct()
  {
    $this->_swadocManager = new SwadocManager();
  }

  public function getSwadoc()
  {
    $content = $this->_swadocManager->getSwadocContent();

    header('Content-Type: text/plain');
    echo $content;
  }
}
