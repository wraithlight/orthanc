<?php

class LoginController {
  public function loginGuest() {
    $username = 'guest_' . roll_d10k();
    $_SESSION['username'] = $username;
    echo json_encode([
      'username' => $username
    ], JSON_PRETTY_PRINT);
  }
}
?>
