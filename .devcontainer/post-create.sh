
#!/usr/bin/env bash

set -euo pipefail

cd "$(dirname "$0")/.."

echo "==> Installing Volta"

curl -fsSL https://get.volta.sh | bash

export VOLTA_HOME="$HOME/.volta"
export PATH="$VOLTA_HOME/bin:$PATH"

echo "==> Installing Node.js through Volta"

volta install node@24.12.0

echo "==> Installing Corepack"

npm install --global corepack

corepack enable
corepack prepare yarn@4.12.0 --activate

echo "==> Checking toolchain"

node --version
yarn --version
rustc --version
cargo --version
php --version
composer --version
docker --version

echo "==> Installing PHP dependencies"

cd src-server
composer install

cd ..

echo "==> Installing frontend dependencies"

cd src-frontend-app-web
corepack yarn install

cd ..

echo "==> Generating PHP DAL information"

bash generate-dalinfo-php.sh

echo "==> Generating TypeScript DAL information"

bash generate-dalinfo-ts.sh

echo "==> Orthanc Codespace initialization completed!"
