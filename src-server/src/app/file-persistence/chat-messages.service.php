<?php

class ChatMessagesService extends BaseIOService
{
  protected string $filePath;

  public function __construct()
  {
    $this->filePath = __DIR__ . getenv("CHAT_MESSAGES_FILE_PATH") . "/chat_messages.json";
  }

  public function addMessage(array $message): int
  {
    $messages = $this->read();
    $id = count($messages) > 0 ? end($messages)['id'] + 1 : 1;
    $message['id'] = $id;
    $message['timestamp'] = microtime(true);
    $messages[] = $message;
    $this->write($messages);

    return $id;
  }

  public function getMessagesSince(int $lastId): array
  {
    $messages = $this->read();
    return array_values(array_filter($messages, fn($m) => $m['id'] > $lastId));
  }

  public function getLastMessageId(): int
  {
    $messages = $this->read();
    if (empty($messages)) {
      return 0;
    }
    return end($messages)['id'];
  }
}

?>