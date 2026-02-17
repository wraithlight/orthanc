## Repository secrets
The following secrets must be created within the repository for each environment.

| Secret name           | Value type  | Example value      | Description |
| :-:                   | :-:         | :-:                | :--         |
| `FTP_HOST`            | `string`    | `ftp.contoso.com`  | The address of the FTP server. Must support port `21`. |
| `FTP_USER`            | `string`    | `UserName`         | The username for the FTP connection. |
| `FTP_PASS`            | `string`    | `Password123!`     | The password for the FTP connection. |
| `FTP_SERVER_FOLDER`   | `string`    | `/`                | The folder where the PHP code will be copied during development. |
| `FTP_FRONTEND_FOLDER` | `string`    | `/`                | The folder on the FTP server where the FE dist will be copied. |
| `FTP_MAZE_FOLDER`     | `string`    | `/`                | The folder where the maze file will be copied. |
| `FTP_SWADOC_FOLDER`   | `string`    | `/api-docs`        | The folder where the swadoc file will be copied. |
