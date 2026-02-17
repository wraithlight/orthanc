<?php
class SwadocService extends BaseIOService
{
  protected string $filePath;

  public function __construct() {
    $this->filePath = __DIR__ . getenv("SWAGGER_FILE_PATH");
  }

  public function getSwadocFileContent(): string
  {
    $content = $this->readText();
    return $content;
  }

}
