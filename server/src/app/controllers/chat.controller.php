<?php

class ChatController {
  public function sendMessage() {
    $username = $_SESSION['username'];
    $rawBody = file_get_contents('php://input');
    $payload = json_decode($rawBody, true);
    $message = $payload['message'];

    $chatMessageService = new ChatMessagesService();
    $id = $chatMessageService->addMessage([
      'sender' => $username,
      'message' => $message
    ]);

    echo json_encode([], JSON_PRETTY_PRINT);
  }

  public function getMessages() {
    $lastMessageId = $_SESSION['lastChatMessageId'];
    $chatMessageService = new ChatMessagesService();
    $chatMembersService = new ChatMembersService();
    $messages = $chatMessageService->getMessagesSince($lastMessageId);
    $members = $chatMembersService->getAllMembers();

    echo json_encode([
      'messages' => $messages,
      'members' => $members
    ], JSON_UNESCAPED_UNICODE);
  }
}
?>
