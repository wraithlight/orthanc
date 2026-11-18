<?php

class DatabaseManager
{

  public function __construct()
  {
  }

  public function deploy(): PostDatabaseDeployResponsePayload
  {
    $result = new PostDatabaseDeployResponsePayload();
    return $result;
  }

  public function migrate(string $targetVersion): PostDatabaseMigrateResponsePayload
  {
    $result = new PostDatabaseMigrateResponsePayload();
    $result->currentVersion = $targetVersion;
    return $result;
  }
}
