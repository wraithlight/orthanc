<?php
class ChatMembersService extends BaseIOService
{
    protected string $filePath = __DIR__ . '/../../data/chat_members.json';

    public function addMember(string $name): int
    {
        $members = $this->read();
        $id = count($members) > 0 ? end($members)['id'] + 1 : 1;
        $member = [
            'name' => $name,
            'id' => $id,
            'last_seen' => time()
        ];
        $members[] = $member;
        $this->write($members);

        return $id;
    }

    public function updateLastSeen(string $name): void
    {
        $members = $this->read();
        foreach ($members as &$m) {
            if ($m['name'] === $name) {
                $m['last_seen'] = time();
                break;
            }
        }
        $this->write($members);
    }

    public function cleanupInactiveMembers(int $ttl = 5): void
    {
        $members = $this->read();
        $now = time();
        $members = array_filter($members, fn($m) => isset($m['last_seen']) && ($now - $m['last_seen']) < $ttl);
        $this->write(array_values($members));
    }

    public function getAllMembers(): array
    {
        $members = $this->read();
        return array_map(fn($m) => $m['name'], $members);
    }

}
?>
