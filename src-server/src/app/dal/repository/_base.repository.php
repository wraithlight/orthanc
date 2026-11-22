<?php

abstract class BaseRepository
{
  protected PDO $_dbConnection;

  public function __construct(PDO $connection) {
    $this->_dbConnection = $connection;
  }

}
