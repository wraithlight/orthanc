## Environment variables

The application uses the following environment variables.

| Name                          | Data type | Example value           | Applicaiton | Location  | Description |
| :-:                           | :-:       | :-:                     | :-:         | :-:       | :-:         |
| `CHAT_MEMBERS_FILE_PATH`      | `string`  | `/../../../data`        | `BE`        | [chat-members.service.php](https://github.com/wraithlight/orthanc/blob/main/server/src/app/file-persistence/chat-members.service.php#L7)        | The path that determines the `chat_members.json` file location.   |
| `CHAT_MESSAGES_FILE_PATH`     | `string`  | `/../../../data`        | `BE`        | [chat-messages.service.php](https://github.com/wraithlight/orthanc/blob/main/server/src/app/file-persistence/chat-messages.service.php#L9)      | The path that determines the `chat_messages.json` file location.  |
| `HALL_OF_FAME_FILE_PATH`      | `string`  | `/../../../data`        | `BE`        | [hall-of-fame.service.php](https://github.com/wraithlight/orthanc/blob/main/server/src/app/file-persistence/hall-of-fame.service.php#L8)        | The path that determines the `hall_of_fame.json` file location.   |
| `SERVER_CORS_ENABLED`         | `boolean` | `true`                  | `BE`        | [cors.php](https://github.com/wraithlight/orthanc/blob/main/server/src/phpapi/core/cors.php#L7)  | Determines when the CORS is enabled on server-side. |
| `SERVER_CORS_ALLOWED_ORIGINS` | `string`  | `http://localhost:3000` | `BE`        | [cors.php](https://github.com/wraithlight/orthanc/blob/main/server/src/phpapi/core/cors.php#L8)  | Lists the allowed remote origins for CORS. |
| `MAZE_FILE_PATH`              | `string`  | `/../../../game-data`   | `BE`        | [maze.service.php](https://github.com/wraithlight/orthanc/blob/main/server/src/app/file-persistence/maze.service.php#L7) | The path that determines the `maze.txt` file location.   |
| `CONFIG_JSON_FILE_PATH`       | `string`  | `/../../fe-assets`      | `BE`        | [config.service.php](https://github.com/wraithlight/orthanc/blob/main/server/src/app/file-persistence/config.service.php#L9) | The path that determines the `config.json` file location. |
