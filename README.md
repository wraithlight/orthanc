# orthanc
Reimplementation of the Orthanc Labyrinth game from 1975.

<h3>
  <p align="center">
  >><a href="https://orthanc.farkask.eu/" target="_blank"> TRY IT OUT HERE </a><<
  </p>
</h3>

## Get started

**Requirements**
* Composer installed
* Docker installed
* NodeJS installed **OR** [volta installed](https://docs.volta.sh/guide/getting-started)
* yarn installed **OR** [volta installed](https://docs.volta.sh/guide/getting-started)

**Set up your local environment**
```sh

git clone https://github.com/wraithlight/orthanc.git    # fork the repo
cd orthanc                                              # navigate to the repo
cd server                                               # navigate to server folder
composer install                                        # install dependencies via composer
cd ..                                                   # navigate to the repo root
cd frontend                                             # navigate to frontend folder
yarn                                                    # install dependencies via yarn

```

**Local development mode**
```sh

git clone https://github.com/wraithlight/orthanc.git    # fork the repo
cd orthanc                                              # navigate to the repo
sh run-dev.sh                                           # run the server in docker (port 3100)
cd frontend                                             # navigate to frontend folder
yarn                                                    # install dependencies via yarn
yarn dev                                                # start dev mode via yarn
open localhost:3000                                     # open the app in your browser

```

**Production-like mode**
```sh

git clone https://github.com/wraithlight/orthanc.git    # fork the repo
cd orthanc                                              # navigate to the repo
sh run-local.sh                                         # run the app in docker
open localhost:4100                                     # open the app in your browser

```

| Mode    | SH file         | Docker image name     | Docker container name | BE Port   | FE Port     |
| :-:     | :-:             | :-:                   | :-:                   | :-:       | :-:         |
| `dev`   | `run-dev.sh`    | `orthanc-server-dev`  | `orthanc-server-dev`  | `3100`    | `3000`      |
| `local` | `run-local-sh`  | `orthanc-game-local`  | `orthanc-game-local`  | `4100`    | self-hosted |

## Contribution

To contribute this project, the following tools and plugins are suggested:

**Tools:**
* Visual Studio Code
* Docker Desktop
* NodeJS **OR** volta
* yarn **OR** volta
* Composer

**Plugins:**
* Composer (https://marketplace.visualstudio.com/items?itemName=DEVSENSE.composer-php-vscode)
* IntelliPHP (https://marketplace.visualstudio.com/items?itemName=DEVSENSE.intelli-php-vscode)
* PHP (https://marketplace.visualstudio.com/items?itemName=DEVSENSE.phptools-vscode)
* PHP Profiler (https://marketplace.visualstudio.com/items?itemName=DEVSENSE.profiler-php-vscode)

When your create a commit, please do follow the [conventional commits](https://www.conventionalcommits.org/en/v1.0.0/) guideline to make changelog friendly and easy-to-track commit history!

During Docker build, the `run-dev.sh` script will create its own docker image named `orthanc-server-dev`. The script itself takes care of the cleanup in case of rebuild/rerun, only one image and container will be present at the same time. But if you'd like to clean up your machine, do not forget to delete the docker image! The same applies for the `run-local.sh` file.

| SH file name    | Docker Image name     | Docker Container name   |
| :--             | :--                   | :--                     |
| `run-dev.sh`    | `orthanc-server-dev`  | `orthanc-server-dev`    |
| `run-local.sh`  | `orthanc-game-local`  | `orthanc-game-local`    |

As you may see the Docker Image and the Docker Container have the same name.

### BE scripts
The `composer.json` file under `server` contains the following scripts:

```sh

  gha:test:unit         - Used by GitHub | Runs the unit tests for server.

```

### FE scripts
The `package.json` file under `frontend` contains the following scripts:

```sh

  gha:build:prod        - Used by GitHub | Runs the build with production env.
  gha:build:canary      - Used by GitHub | Runs the build with canary env.
  gha:build:development - Used by GitHub | Runs the build with development env.
  gha:lint              - Used by GitHub | Runs the linter over the typescript codebase.
  gha:stylelint         - Used by GitHub | Runs the linter over the SCSS/CSS codebase.
  gha:htmlhint          - Used by GitHub | Runs the htmlhint over the HTML codebase.
  gha:test:unit         - Used by GitHub | Runs the unit tests for frontend.
  gha:knip              - Used by GitHub | Runs the knip check for frontend.
  gha:sherif            - Used by GitHub | Runs the sherif check for frontend.
  dev                   - Runs the FE locally on port 3000.
  build                 - Runs the build with local-development env.

```

### Technical details
The following technologies are used during the development of this game:
* Composer
* CSS
* Docker
* HTML
* HTML Canvas with Typescript
* PHP
* typescript/javascript
* Vite
* volta
* yarn


## Documentation
The repository has a constantly growing documentation folder that can be found under `/docs`. Here is a quick overview of its content:
* [Deployment](./docs/DEPLOYMENT.md)
* [Environment variables](./docs/ENVIRONMENT-VARIABLES.md)
* [Environments](./docs/ENVIRONMENTS.md)
* [Error Codes](./docs/ERROR-CODES.md)
* [File persistence](./docs/FILE-PERSISTENCE.md)
* [How to create a new environment](./docs/HOW-TO-CREATE-NEW-ENVIRONMENT.md)
* [Repository environments](./docs/REPOSITORY-ENVS.md)
* [Repository secrets](./docs/REPOSITORY-SECRETS.md)
