export type CartMoney = {
	currency: string;
	amount: number;
	formatted?: string;
}

export type CartItemOption = {
	id?: string | number;
	post_id?: string | number;
	[key: string]: unknown;
};
export type CartItem = {
	id: string;
	productId: number | string;
	name?: string;
	sku?: string;
	quantity: number;
	price?: CartMoney;
	line_total?: CartMoney;
	meta?: Record<string, unknown>;
	isHit?: boolean;
	hasSale?: boolean;
	originalPriceFormatted?: string;
	priceFormatted?: string;
	unit?: string;
	options?: CartItemOption[];
	rez?: number;
}

export type CartTotals = {
	items_count: number;
	items_qty: number;
	subtotal?: CartMoney;
	discount_total?: CartMoney;
	shipping_total?: CartMoney;
	tax_total?: CartMoney;
	grand_total?: CartMoney;
	sum_without_discount?: number;
	sum_with_discount?: number;
	discount?: number;
	sum_without_discount_formatted?: string;
	sum_with_discount_formatted?: string;
	discount_formatted?: string;
};

export type Cart = {
	id: string;
	version: number;
	items: CartItem[];
	totals?: CartTotals;
	applied_coupons?: string[];
	selections?: Record<string, unknown>;
};

export interface CartRepository {
	get(): Promise<Cart>;
	addItem(input: { productId: number | string; quantity: number; optionIds?: number[]; rez?: number }): Promise<Cart>;
	updateItem(input: { itemId: string; quantity: number }): Promise<Cart>;
	removeItem(itemId: string): Promise<Cart>;
	clear(): Promise<Cart>;
}

