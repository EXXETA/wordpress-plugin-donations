# Banner assets

This `npm` package contains common assets (images and styles actually) which are being used by all shop banner plugins.

Integrate this package into your shop-specific plugin's release/development process!

## Commands

- `npm run clean`: Remove the "dist" directory
- `npm run styles`: Transpile the `banner.scss` file to `banner.css` by using node-sass
- `npm run copy:assets`: Copy image files to dist
- `npm run assemble`: Run `styles` and `copy:assets`