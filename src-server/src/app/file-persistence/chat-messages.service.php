<?php

class ChatMessagesService extends BaseIOService
{
  protected string $filePath;

  public function __construct()
  {
    $this->filePath = __DIR__ . getenv("CHAT_MESSAGES_FILE_PATH") . "/chat_messages.json";
  }

  public function addMessage(
    array $message,
    string $tunnel
  ): int
  {
    $messages = $this->read();
    $id = count($messages) > 0 ? end($messages)['id'] + 1 : 1;
    $message['id'] = $id;
    $message['timestamp'] = microtime(true);
    $message['tunnel'] = $tunnel;
    $messages[] = $message;
    $this->write($messages);

    return $id;
  }

  public function getMessagesSince(
    int $lastId,
    string $tunnel
  ): array
  {
    $messages = $this->read();
    return array_values(array_filter(
      $messages,
      fn($m) =>
        isset($m['id'], $m['tunnel']) &&
        $m['tunnel'] === $tunnel &&
        $m['id'] > $lastId
    ));
  }

  public function getLastMessageId(string $tunnel): int
  {
    $messages = $this->read();

    $filtered = array_filter(
      $messages,
      fn($m) =>
        isset($m['id'], $m['tunnel']) &&
        $m['tunnel'] === $tunnel
    );

    if (empty($filtered)) {
      return 0;
    }

    return max(array_column($filtered, 'id'));
  }
}
