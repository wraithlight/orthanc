<?php

class ChatController
{
  private $_chatManager;
  private $_sessionManager;

  public function __construct()
  {
    $this->_chatManager = new ChatManager();
    $this->_sessionManager = new SessionManager();
  }

  public function sendMessage()
  {
    $this->_sessionManager->authenticate();

    $rawBody = file_get_contents('php://input');
    $payload = json_decode($rawBody, true);
    $message = $payload['message'];

    $this->_chatManager->sendMessage($message);

    echo json_encode([]);
  }

  public function getMessages()
  {
    $id = $this->_sessionManager->authenticate();
    $result = $this->_chatManager->onPoll($id);

    echo json_encode($result, JSON_UNESCAPED_UNICODE);
  }
}
