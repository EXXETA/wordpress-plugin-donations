#!/usr/bin/env bash
set -eu

dir=$(cd -P -- "$(dirname -- "${BASH_SOURCE[0]}")" && pwd -P)
cd "$dir"

cd "../../../../../../assets"
assetDir="$(pwd)"
npm run assemble

cd "$dir"

# copy files
cp -f "$assetDir/dist/banner.css" "./Resources/views/frontend/_resources/css/banner.css"
cp -fr "$assetDir/dist/images" "./Resources/views/frontend/_resources/css"

echo "OK."
