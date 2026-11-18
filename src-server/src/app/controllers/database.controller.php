<?php

class DatabaseController
{

  private $_databaseManager;

  public function __construct()
  {
    $this->_databaseManager = new DatabaseManager();
  }

  public function deploy(PostDatabaseDeployRequest $body)
  {
    $result = $this->_databaseManager->deploy();
    echo json_encode(createSuccessResponse($result));
  }

  public function migrate(PostDatabaseMigrateRequest $body)
  {
    $result = $this->_databaseManager->migrate($body->targetVersion);
    echo json_encode(createSuccessResponse($result));
  }
}
