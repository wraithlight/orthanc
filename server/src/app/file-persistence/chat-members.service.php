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
            'id' => $id
        ];
        $members[] = $member;
        $this->write($members);

        return $id;
    }

    public function getAllMembers(): array
    {
        $members = $this->read();
        return array_map(fn($m) => $m['name'], $members);
    }

    public function removeMember(int $name): void
    {
        $members = $this->read();
        $members = array_filter($members, fn($m) => $m['name'] !== $name);
        $this->write(array_values($members));
    }
}
?>
