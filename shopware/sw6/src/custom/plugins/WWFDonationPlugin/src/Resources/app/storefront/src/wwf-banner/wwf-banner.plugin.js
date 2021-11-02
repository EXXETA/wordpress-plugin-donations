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
import Plugin from 'src/plugin-system/plugin.class';

export default class WwfBannerPlugin extends Plugin {
    init() {
        const moreInfoButton = this.el.querySelector(".more-campaign-info");
        // look for the three dom elements we need: fade-in-btn, fade-out-btn and info area
        if (!moreInfoButton) {
            console.error("WWFBannerPlugin: No info button found!");
            return;
        }
        const hideInfoArea = this.el.querySelector(".donation-campaign-more-info .fade-out-link");
        if (!hideInfoArea) {
            console.error("WWFBannerPlugin: No fade-out-link-detected");
            return;
        }
        const moreInfoArea = this.el.querySelector(".donation-campaign-collapsible");
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
    }
}