<?php

class SessionManager {

  public function authenticate(): string {
    if (empty($_COOKIE['PHPSESSID'])) {
      http_response_code(401);
      header('Content-Type: application/json');
      echo json_encode([
          'errorCode' => 'ERROR_0401',
      ]);
      exit;
    }
    session_start();
    return session_id();
  }

  public function createNewSession(): void {
    session_start([
      "use_strict_mode" => 1,
    ]);
    session_regenerate_id(true);
    $_SESSION = [];
  }

  public function getSessionId(): string {
    return session_id();
  }

}
