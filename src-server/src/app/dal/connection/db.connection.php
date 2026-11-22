<?php

final class DbConnection
{
  private static PDO $instance = null;

  public static function GetInstance(): PDO {
    if (self::$instance === null) {
      self::$instance = new PDO(
        "mysql:host=" . getenv('DATABASE_URL') . ";dbname=" . getenv('DATABASE_NAME') . ";port=" . getenv('DATABASE_PORT'),
        getenv('DATABASE_USER'),
        getenv('DATABASE_PASSWORD')
      );
    }
    return self::$instance;
  }

}
