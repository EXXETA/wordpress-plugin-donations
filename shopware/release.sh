#!/usr/bin/env bash

set -eu

dir=$(cd -P -- "$(dirname -- "${BASH_SOURCE[0]}")" && pwd -P)
cd "$dir"

mkdir -p ../release/sw6

cd sw6/src/custom/plugins/WWFDonationPlugin ../release/sw6
