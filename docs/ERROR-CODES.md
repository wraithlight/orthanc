## Error Codes from Orthanc Server
This page describes the error codes that are being returned from the Orthanc Server.

| Code          | Status Code | Description |
| :-:           | :-:         | :-:         |
| `ERROR_0001`  | `400`       | During game load the client sent an invalid action. If the action is not `INITIAL_IN_GAME`, this error will be thrown. |
| `ERROR_0401`  | `401`       | The request did not contain a valid `PHPSESSID`. Meaning the customer is not authneticated. |