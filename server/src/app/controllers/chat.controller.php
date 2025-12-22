<?php

class ChatController {
  public function sendMessage() {
    $stateService = new StateService();
    $username = $stateService->getPlayerName();
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
    $stateService = new StateService();
    $chatMembersService = new ChatMembersService();
    $chatMessageService = new ChatMessagesService();

    $lastMessageId = $stateService->getChatLastMessageId();;
    $messages = $chatMessageService->getMessagesSince($lastMessageId);
    $members = $chatMembersService->getAllMembers();

    echo json_encode([
      'messages' => $messages,
      'members' => $members
    ], JSON_UNESCAPED_UNICODE);
  }
}
?>
