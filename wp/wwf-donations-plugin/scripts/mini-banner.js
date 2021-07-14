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
jQuery(document).ready(function () {
    jQuery(".donation-campaign-mini-quantity-input").on("change", function (e) {
        const closest = jQuery(this).parent().parent().find("a.ajax_add_to_cart");
        if (closest.length === 0) {
            // something went wrong
            console.error("Something went wrong during attempt of changing quantity of wwf campaign product.");
            return;
        }
        let currentQty = parseInt(jQuery(this).val(), 10); // use changed quantity
        if (isNaN(currentQty) || currentQty < 1) {
            // fallback = 1
            currentQty = 1;
        }
        closest.attr("data-quantity", currentQty);
    });
});
