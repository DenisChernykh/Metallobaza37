//VGCart.add(123, 2).then(res => console.log(res));
//VGCart.update(123, 2).then(res => console.log(res));
//VGCart.remove(123).then(res => console.log(res));

class VGCart {
    static init() {
        if (typeof VG_Catalog === "undefined") {
            console.error("VG_Catalog is not defined.");
            return;
        }

        this.ajaxUrl = VG_Catalog.ajax_url + '/wp-content/plugins/VG_Catalog/router.php';
    }

    static async request(route, data = {}) {
        try {
            const payload = {route, ...data};

            const response = await axios.post(this.ajaxUrl, payload, {
                headers: {'Content-Type': 'application/json'}
            });

            return response.data;
        } catch (error) {
            console.error("VGCart AJAX Error:", error);

            const message = error.response?.data?.message || error.message;
            return {error: true, message};
        }
    }

    static add(productId, qty = 1) {
        return this.request("cart-add", {product_id: productId, quantity: qty});
    }

    static update(productId, qty = 1) {
        return this.request("cart-update", {product_id: productId, quantity: qty});
    }

    static remove(productId) {
        return this.request("cart-remove", {product_id: productId});
    }
}

VGCart.init();