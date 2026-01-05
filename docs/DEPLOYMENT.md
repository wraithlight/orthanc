## Deployment

The repository has a fine-tuned release train, where the RME should start the deployment only.

The repository contains the following deployment workflows:
* `Deployment Train` [ref](#deployment-train)
* `FTP - Deploy FRONTEND` [ref](#ftp---deploy-frontend)
* `FTP - Deploy MAZE` [ref](#ftp---deploy-maze)
* `FTP - Deploy SERVER` [ref](#ftp---deploy-server)

The repository contains the following github actions:
* `deploy-ftp` [ref](#deploy-ftp)

---

### `Deployment Train`
This is the main entry point of each deployment process. Based on the inputs tis workflow dispatches the required workflows and passes the target environment to them.

Inputs:
* `deploy_be` (`boolean`)
* `deploy_fe` (`boolean`)
* `deploy_maze` (`boolean`)
* `target_env` (`PROD | CANARY | DEV`)

When this workflow triggers another one, it sends the following payload:

```json

{
  "env": "PROD"
}

```

This workflow can be found [here](https://github.com/wraithlight/orthanc/blob/main/.github/workflows/deploy-train.yaml), and can be triggered [here](https://github.com/wraithlight/orthanc/actions/workflows/deploy-train.yaml).

### `FTP - Deploy FRONTEND`
This workflow is responsible for building the frontend application then copy it into the FTP.
The workflow uses the `env` payload property passed from the train. Based on that it sets the GH Environment to read the [secrets](./REPOSITORY-SECRETS.md) from it.

This workflow can be found [here](https://github.com/wraithlight/orthanc/blob/main/.github/workflows/deploy-fe.yaml), and the runs can be checked [here](https://github.com/wraithlight/orthanc/actions/workflows/deploy-fe.yaml).

### `FTP - Deploy MAZE`
This workflow is responsible for deploying the maze itself to the FTP.

The workflow uses the `env` payload property passed from the train. Based on that it sets the GH Environment to read the [secrets](./REPOSITORY-SECRETS.md) from it.

This workflow can be found [here](https://github.com/wraithlight/orthanc/blob/main/.github/workflows/deploy-maze.yaml), and the runs can be checked [here](https://github.com/wraithlight/orthanc/actions/workflows/deploy-maze.yaml).

### `FTP - Deploy SERVER`
This workflow is responsible for deploying the backend code to the FTP.

The workflow uses the `env` payload property passed from the train. Based on that it sets the GH Environment to read the [secrets](./REPOSITORY-SECRETS.md) from it.

This workflow can be found [here](https://github.com/wraithlight/orthanc/blob/main/.github/workflows/deploy-be.yaml), and the runs can be checked [here](https://github.com/wraithlight/orthanc/actions/workflows/deploy-be.yaml).

### `deploy-ftp`
This GitHub action can be found [here](https://github.com/wraithlight/orthanc/tree/main/.github/actions/deploy-ftp). This action is responsible for FTP upload. The action overwrites all files that are part of the deployment, but **does not remove** files that are not in the repository anymore.

Inputs:
* `ftp_host` (`string`)
* `ftp_user` (`string`)
* `ftp_pass` (`string`)
* `remote_dir` (`string`)
* `local_dir` (`string`)
