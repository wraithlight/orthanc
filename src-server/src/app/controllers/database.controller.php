<?php

class DatabaseController
{

  public function __construct()
  {
  }

  public function deploy(PostDatabaseDeployRequest $body)
  {
    $result = new PostDatabaseDeployResponsePayload();
    echo json_encode(createSuccessResponse($result));
  }

  public function migrate(PostDatabaseMigrateRequest $body)
  {
    $result = new PostDatabaseMigrateResponsePayload();
    echo json_encode(createSuccessResponse($result));
  }
}
