<?php
class ChatManager
{

  private $_chatMembersService;
  private $_chatMessageService;
  private $_sessionService;

  public function __construct()
  {
    $this->_chatMembersService = new ChatMembersService();
    $this->_chatMessageService = new ChatMessagesService();
    $this->_sessionService = new SessionService();
  }

  public function addMember(
    string $id
  ) {
    $username = $this->_sessionService->getPlayerName();
    $lastMessageId = $this->_chatMessageService->getLastMessageId();

    $this->_chatMembersService->addMember($username, $id);
    $this->_sessionService->setLastChatMessageId($lastMessageId);
  }

  public function sendMessage(
    string $message
  ) {
    $this->_chatMessageService->addMessage([
      "payload" => [
        'sender' => $this->_sessionService->getPlayerName(),
        'message' => $message
      ]
    ]);
  }

  public function sendSystemMessage(
    string $message
  ) {
    $this->_chatMessageService->addMessage([
      "payload" => [
        'sender' => "[PLATOSYS]",
        'message' => $message
      ]
    ]);
  }

  public function onPoll(
    string $id
  ): array
  {
    $lastMessageId = $this->_sessionService->getLastChatMessageId();

    $this->_chatMembersService->updateLastSeen($id);
    $members = $this->_chatMembersService->getActiveMembers();
    $messages = $this->_chatMessageService->getMessagesSince($lastMessageId);

    return [
      "payload" => [
        'messages' => $messages,
        'members' => $members
      ]
    ];
  }

}
