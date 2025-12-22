<?php

class GameController {
  public function startGame() {
    $username = $_SESSION['username'];
    $character = $_SESSION['character'];
    
    $currentHits = $character['maxHits'];
    $character['currentHits'] = $currentHits;

    $_SESSION['character'] = $character;
    $chatMessageService = new ChatMessagesService();
    $_SESSION['lastChatMessageId'] = $chatMessageService->getLastMessageId();
    $chatMembersService = new ChatMembersService();
    $chatMembersService->addMember($username);

    echo json_encode([
      'username' => $username,
      'character' => $character
    ], JSON_PRETTY_PRINT);
  }
}
?>
