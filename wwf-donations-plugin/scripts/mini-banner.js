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
