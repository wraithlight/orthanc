## Environments
The application is deployed to multiple environments:
* dev
* canary
* production

These deployments are using the same configuration/code, but the deployment methodolgies are different.

| Deployment type | Deployment mode     |
| :-:             | :-:                 |
| `dev`           | `CICD automatic`    |
| `canary`        | `manual from main`  |
| `prodcution`    | `manual from main`  |

Since all of these environment are completely different here is a list of their address:

| Deployment type | URL |
| :-:             | :-:                                 |
| `dev`           | https://dev.orthanc.farkask.eu/     |
| `canary`        | https://canary.orthanc.farkask.eu/  |
| `prodcution`    | https://orthanc.farkask.eu/         |
