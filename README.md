# orthanc
Reimplementation of the Orthanc Labyrinth game from 1975.

<h3>
  <p align="center">
  >><a href="https://orthanc.farkask.eu/" target="_blank"> TRY IT OUT HERE </a><<
  </p>
</h3>

## Get started

**Requirements**
* NodeJS installed
* Docker installed
* yarn

**Local development mode**
```sh

git clone https://github.com/wraithlight/orthanc.git    # fork the repo
cd orthanc                                              # navigate to repo folder
sh init-repo.sh                                         # initialize server/data folder
sh run-dev.sh                                           # run the server in docker (port 3100)
cd frontend                                             # navigate to frontend folder
yarn                                                    # install dependencies via yarn
yarn dev                                                # start dev mode via yarn
open localhost:3000                                     # open the app in your browser

```

**Production-like mode**
```sh

git clone https://github.com/wraithlight/orthanc.git    # fork the repo
cd orthanc                                              # navigate to repo folder
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
* NodeJS
* Docker Desktop
* yarn

**Plugins:**
* Composer (https://marketplace.visualstudio.com/items?itemName=DEVSENSE.composer-php-vscode)
* IntelliPHP (https://marketplace.visualstudio.com/items?itemName=DEVSENSE.intelli-php-vscode)
* PHP (https://marketplace.visualstudio.com/items?itemName=DEVSENSE.phptools-vscode)
* PHP Profiler (https://marketplace.visualstudio.com/items?itemName=DEVSENSE.profiler-php-vscode)

When the commits are being created, please follow the [conventional commits](https://www.conventionalcommits.org/en/v1.0.0/)!
During build the `run-dev.sh` script will create its own docker images name `orthanc-server-dev`. The script itself takes care of the cleanup, only one image and container will be present at the same time. But if you'd like to clean up your machine, do not forget to delete the docker image!

### Technical details
The following technologies are used during the development of this game:
* typescript/javascript
* CSS
* HTML
* Vite
* PHP
* HTML Canvas with Typescript
* Docker
* yarn


## Documentation
The repository has a constantly growing documentation folder that can be found under `/docs`. Here is a quick overview of its content:
* [Error Codes](./docs/ERROR-CODES.md)
