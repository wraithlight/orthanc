<?php

class ChatController
{
  public function sendMessage()
  {
    if (empty($_COOKIE['PHPSESSID'])) {
      http_response_code(401);
      header('Content-Type: application/json');
      echo json_encode([
          'errorCode' => 'ERROR_0401',
      ]);
      exit;
    }
    session_start();

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

    echo json_encode([], JSON_PRETTY_PRINT);
  }

  public function getMessages()
  {
    if (empty($_COOKIE['PHPSESSID'])) {
      http_response_code(401);
      header('Content-Type: application/json');
      echo json_encode([
          'errorCode' => 'ERROR_0401',
      ]);
      exit;
    }
    session_start();

    $stateService = new StateService();
    $chatMembersService = new ChatMembersService();
    $chatMessageService = new ChatMessagesService();

    $id = session_id();
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