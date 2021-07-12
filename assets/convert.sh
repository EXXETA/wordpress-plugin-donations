#!/usr/bin/env bash

# NOTE: You need to have ImageMagick installed for this script to work!

set -eu

dir=$(cd -P -- "$(dirname -- "${BASH_SOURCE[0]}")" && pwd -P)
cd "$dir"

rm -rf sample-images
mkdir sample-images

cp -r sample-images-orig/*.PNG sample-images
cd ./sample-images

for file in banner_sample_*.PNG; do
  echo "$file"
  convert $file -resize 1536x -quality 96 "$(basename -s .PNG $file)-l.png"
  convert $file -resize 768x -quality 96 "$(basename -s .PNG $file)-m.png"
  convert $file -resize 384x -quality 96 "$(basename -s .PNG $file)-s.png"
  rm "$file"
done

which exiftool &>/dev/null
if [ $? -eq 0 ]; then
  exiftool -All= *.png
else
  echo "exiftool is not installed"
fi

rm *.png_original

exit 0
