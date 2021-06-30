#!/usr/bin/env bash

set -eu

dir=$(cd -P -- "$(dirname -- "${BASH_SOURCE[0]}")" && pwd -P)
cd "$dir"

bash release_sw5.sh
bash release_sw6.sh