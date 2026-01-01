<?php
class ChatManager
{

  private $_chatMembersService;
  private $_chatMessageService;
  private $_stateService;

  public function __construct()
  {
    $this->_chatMembersService = new ChatMembersService();
    $this->_chatMessageService = new ChatMessagesService();
    $this->_stateService = new StateService();
  }

  public function addMember(
    string $id
  ) {
    $username = $this->_stateService->getPlayerName();
    $lastMessageId = $this->_chatMessageService->getLastMessageId();

    $this->_chatMembersService->addMember($username, $id);
    $this->_stateService->setChatLastMessageId($lastMessageId);
  }

  public function sendMessage(
    string $message
  ) {
    $this->_chatMessageService->addMessage([
      "payload" => [
        'sender' => $this->_stateService->getPlayerName(),
        'message' => $message
      ]
    ]);
  }

  public function onPoll(
    string $id
  ): array
  {
    $lastMessageId = $this->_stateService->getChatLastMessageId();

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
