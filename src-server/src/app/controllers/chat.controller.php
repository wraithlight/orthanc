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

  public function sendMessage(PostSendChatRequest $body)
  {
    $this->_sessionManager->authenticate();

    $this->_chatManager->sendMessage($body->message);

    echo json_encode(createSuccessResponse(new PostSendChatResponsePayload()));
  }

  public function getMessages()
  {
    $id = $this->_sessionManager->authenticate();
    $result = $this->_chatManager->onPoll($id);

    echo json_encode(createSuccessResponse($result), JSON_UNESCAPED_UNICODE);
  }
}
