<?php
class ChatMembersService extends BaseIOService
{
  protected string $filePath;

  public function __construct() {
    $this->filePath = __DIR__ . getenv("CHAT_MEMBERS_FILE_PATH") ."/chat_members.json";
  }

  public function addMember(
    string $name,
    string $id
  ): string
  {
    $members = $this->read();
    $member = [
      'name' => $name,
      'id' => $id,
      'last_seen' => time()
    ];
    $members[] = $member;
    $this->write($members);

    return $id;
  }

  public function updateLastSeen(string $id): void
  {
    $members = $this->read();
    foreach ($members as &$m) {
      if ($m['id'] === $id) {
        $m['last_seen'] = time();
        break;
      }
    }
    $this->write($members);
  }

  public function getActiveMembers($ttl = 5): array
  {
    $members = $this->read();
    $now = time();
    $members = array_filter($members, fn($m) => isset($m['last_seen']) && ($now - $m['last_seen']) < $ttl);
    return array_values(array_map(fn($m) => $m['name'], $members));
  }

}
?>