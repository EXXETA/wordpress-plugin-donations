import Plugin from 'src/plugin-system/plugin.class';
import HttpClient from "src/service/http-client.service";
import PageLoadingIndicatorUtil from "src/utility/loading-indicator/page-loading-indicator.util";
import DomAccess from "src/helper/dom-access.helper";

export default class MiniBannerPlugin extends Plugin {
    init() {
        this._client = new HttpClient(window.accessKey);

        this.el.addEventListener('submit', ev => {
            // catch this event, prevent default execution and redirect the form data to
            ev.preventDefault();
            PageLoadingIndicatorUtil.create();

            const bannerEl = this.el.closest('.sw-cms-block-commerce-wwf-banner')
            if (!bannerEl || ((ev && ev.hasOwnProperty('path') && !ev.path && !ev.path[0]) && !ev.srcElement) || !ev.target.action) {
                console.error('could not find wwf banner element and/or form content!');
                setTimeout(PageLoadingIndicatorUtil.remove(), 250);
                return;
            }
            let form;
            if (!ev.path) {
                form = ev.srcElement;
            } else {
                form = ev.path[0];
            }
            const search = new URLSearchParams(new FormData(form));

            const targetUrl = encodeURI(ev.target.action + '?' + search.toString());

            this._client.get(targetUrl, resp => {
                // TODO handle error case...
                setTimeout(PageLoadingIndicatorUtil.remove(), 250);

                if (bannerEl.classList.contains('cart-integration')) {
                    if (window.location) {
                        window.location.reload();
                    }
                    return
                }
                const showOffCanvas = DomAccess.getDataAttribute(bannerEl, 'open-offcanvas');
                if (showOffCanvas) {
                    PluginManager.getPluginInstances('OffCanvasCart').forEach(instance => instance.openOffCanvas(window.router['frontend.cart.offcanvas'], false));
                } else {
                    PluginManager.getPluginInstances('CartWidget').forEach(instance => instance.fetch());
                }
            });
        });
    }
}