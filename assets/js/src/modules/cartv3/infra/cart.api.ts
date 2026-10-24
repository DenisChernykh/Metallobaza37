// /assets/js/cart/infra/cart.api.ts
import type { Cart, CartRepository } from "../domain/cart.types";
import { RouterListResponse } from "./dto/router.types";
import { mapRouterListToCart } from "./mappers/routerCart.mapper";
import { callRouter } from "./router.transport";


type RouterItem = {
	id: string;
	quantity: number | string;
	product_id?: number | string;
	product?: {
		title?: string;
		price?: number | string | null;
		sale_price?: number | string | null;
		sku?: string;
		post_id?: number | string;
		unit?: string;
	};
	options?: unknown[];
}


export class CartApiRepository implements CartRepository {
	async get(): Promise<Cart> {
		const response = await callRouter<RouterListResponse>("GET", "cart-get-list");
		return mapRouterListToCart(response);
	}

	async addItem(input: { productId: number | string; quantity: number, optionIds?: number[], rez?: number }): Promise<Cart> {

		const response = await callRouter<RouterListResponse>("POST", "cart-add", {
			product_id: input.productId,
			quantity: input.quantity,
			option_ids: input.optionIds,
			rez: input.rez

		});

		if (response.data?.cart) return mapRouterListToCart(response);
		return this.get();
	}

	async updateItem(input: { itemId: string; quantity: number }): Promise<Cart> {
		const response = await callRouter<RouterListResponse>("PATCH", "cart-update", {
			cart_id: input.itemId,
			quantity: input.quantity,
		});

		if (response.data?.cart) return mapRouterListToCart(response);
		return this.get();
	}

	async removeItem(itemId: string): Promise<Cart> {
		const response = await callRouter<RouterListResponse>("DELETE", "cart-remove", {
			cart_id: itemId,
		});

		if (response.data?.cart) return mapRouterListToCart(response);
		return this.get();
	}

	async clear(): Promise<Cart> {
		const response = await callRouter<RouterListResponse>("DELETE", "cart-clear");

		if (response.data?.cart) return mapRouterListToCart(response);
		return this.get();
	}
}