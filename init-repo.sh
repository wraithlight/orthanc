#!/bin/bash

FILES=(
  "server/data/chat_members.json"
  "server/data/chat_messages.json"
  "server/data/hall_of_fame.json"
)

for file in "${FILES[@]}"; do
  [[ -e "$file" ]] || touch "$file"
done
