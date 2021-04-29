// import admin block configuration
import './module/sw-cms/blocks/commerce/wwf-banner';
import './module/sw-cms/elements/wwf-banner';

// import and register locales
import deDE from './module/sw-cms/snippet/de-DE.json';
import enGB from './module/sw-cms/snippet/en-GB.json';

Shopware.Locale.extend('de-DE', deDE);
Shopware.Locale.extend('en-GB', enGB);

