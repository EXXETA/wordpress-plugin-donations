import MiniBannerPlugin from "./mini-banner/mini-banner.plugin";

const PluginManager = window.PluginManager;
// bind to mini-banner existence DOM element
PluginManager.register('MiniBanner', MiniBannerPlugin, '.mini-banner-add-to-cart-form');