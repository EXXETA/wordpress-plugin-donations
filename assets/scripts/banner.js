/*
 * Copyright 2020-2021 EXXETA AG, Marius Schuppert
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */
(function () {
    // register mini-banner input event handler
    document.querySelector(".donation-campaign-quantity-input")?.addEventListener("input", e => {
        let currentQty = parseInt(e.target?.value, 10); // use changed quantity
        if (isNaN(currentQty) || currentQty < 1) {
            // fallback = 1
            currentQty = 1;
        }
        if (e.target) {
            e.target.value = currentQty;
        }
    });
    document.querySelector(".donation-campaign-mini-quantity-input")?.addEventListener("input", e => {
        const closest = e.target?.parentNode?.parentNode?.querySelector("a.ajax_add_to_cart")
        if (!closest) {
            // something went wrong
            console.error("Something went wrong during attempt of changing quantity of wwf campaign product.");
            return;
        }
        let currentQty = parseInt(e.target?.value, 10); // use changed quantity
        if (isNaN(currentQty) || currentQty < 1) {
            // fallback = 1
            currentQty = 1;
        }
        closest.dataset.quantity = currentQty;
        e.target.value = currentQty;
    });
    // handle fade-in and fade-out of default banners
    const moreInfoButton = document.querySelector(".more-campaign-info");
    // look for the three dom elements we need: fade-in-btn, fade-out-btn and info area
    if (!moreInfoButton) {
        // do nothing
        return;
    }
    const hideInfoArea = document.querySelector(".donation-campaign-more-info .fade-out-link");
    if (!hideInfoArea) {
        console.error("WWFBannerPlugin: No fade-out-link-detected");
        return;
    }
    const moreInfoArea = document.querySelector(".donation-campaign-collapsible");
    if (!moreInfoArea) {
        console.error("WWFBannerPlugin: No info text area found!");
        return;
    }
    moreInfoButton.addEventListener("click", e => {
        e.preventDefault();
        moreInfoArea.classList.toggle("fade");
    });
    hideInfoArea.addEventListener("click", e => {
        e.preventDefault();
        moreInfoArea.classList.remove("fade");
    });
})();
