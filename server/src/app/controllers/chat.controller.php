<?php

class ChatController
{
  private $sessionManager;

  public function __construct()
  {
    $this->sessionManager = new SessionManager();
  }

  public function sendMessage()
  {
    $this->sessionManager->authenticate();

    $stateService = new StateService();
    $username = $stateService->getPlayerName();
    $rawBody = file_get_contents('php://input');
    $payload = json_decode($rawBody, true);
    $message = $payload['message'];

    $chatMessageService = new ChatMessagesService();
    $chatMessageService->addMessage([
      "payload" => [
        'sender' => $username,
        'message' => $message
      ]
    ]);

    echo json_encode([]);
  }

  public function getMessages()
  {
    $id = $this->sessionManager->authenticate();

    $stateService = new StateService();
    $chatMembersService = new ChatMembersService();
    $chatMessageService = new ChatMessagesService();

    $chatMembersService->updateLastSeen($id);

    $lastMessageId = $stateService->getChatLastMessageId();

    $messages = $chatMessageService->getMessagesSince($lastMessageId);
    $members = $chatMembersService->getActiveMembers();

    echo json_encode([
      "payload" => [
        'messages' => $messages,
        'members' => $members
      ]
    ], JSON_UNESCAPED_UNICODE);
  }
}
?>