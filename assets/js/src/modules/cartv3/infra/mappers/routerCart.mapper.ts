
import type { Cart, CartItem, CartMoney } from "../../domain/cart.types";
import type { RouterItem, RouterListResponse } from "../dto/router.types";

function toMoney(value: unknown): CartMoney | undefined {
	const amount = Number(value);
	return Number.isFinite(amount) ? { currency: "RUB", amount } : undefined;
}
function normalizeOptionsToPostId(options: unknown[]): any[] {
	const arr = Array.isArray(options) ? options : [];
	return arr.map((o: any) => {
		const postId = o?.post_id ?? o?.postId ?? o?.id;
		return {
			...o,
			id: postId != null ? String(postId) : "",
			post_id: postId != null ? String(postId) : "",
			group_id: o?.group_id != null ? String(o.group_id) : undefined,
			title: o?.title ?? "",
			price: o?.price != null ? Number(o.price) : undefined,
			groupTitle: o?.groupTitle ?? o?.group_title ?? "",
		};
	});
}
const formatted = Intl.NumberFormat('ru-RU');
export function mapRouterItemToCartItem(item: RouterItem): CartItem {
	const productIdRaw = item.product_id ?? item.product?.post_id ?? "";
	const productTitle = item.product?.title ?? "";
	const salePrice = item.product?.sale_price;
	const basePrice = item.product?.price;
	const unit = !item?.product?.unit?.name ? '/ м.п' : `/ ${item.product?.unit?.name}`
	const priceFormatted = `${formatted.format(Number(salePrice))} ₽`
	const originalPriceFormatted = `${formatted.format(Number(basePrice))} ₽`
	const hasSale = Number(salePrice) > 0 && Number(salePrice) < Number(basePrice)
	const options = normalizeOptionsToPostId(item.options ?? []);
	const rez = item.rez

	return {
		id: String(item.id),
		productId: String(productIdRaw),
		name: productTitle,
		sku: item.product?.sku,
		quantity: Number(item.quantity) || 0,
		price: toMoney(basePrice),
		isHit: item.product?.is_hit === "1",
		originalPriceFormatted,
		priceFormatted: hasSale ? priceFormatted : originalPriceFormatted,
		unit,
		hasSale,
		options,
		rez
	};
}

const formatter = new Intl.NumberFormat("ru-RU");


export function mapRouterListToCart(response: RouterListResponse): Cart {
	const routerItems: RouterItem[] = response.data?.cart ?? [];


	const items: CartItem[] = routerItems.map(mapRouterItemToCartItem);


	const itemsQuantity = items.reduce<number>(
		(sum, cartItem) => sum + (cartItem.quantity || 0),
		0
	);


	let sumWithoutDiscount = 0;
	let sumWithDiscount = 0;

	for (const row of routerItems) {
		const qty = Number(row.quantity) || 0;
		const basePrice = Number(row.product?.price) || 0;
		const salePrice = Number(row.product?.sale_price) || 0;

		const finalUnit =
			salePrice > 0 && salePrice < basePrice ? salePrice : basePrice;

		sumWithoutDiscount += basePrice * qty;
		sumWithDiscount += finalUnit * qty;
	}

	const discount = Math.max(0, sumWithoutDiscount - sumWithDiscount);


	return {
		id: "router",
		version: 0,
		items,
		totals: {
			items_count: items.length,
			items_qty: itemsQuantity,
			sum_without_discount: sumWithoutDiscount,
			sum_with_discount: sumWithDiscount,
			discount,
			sum_without_discount_formatted: `${formatter.format(
				sumWithoutDiscount
			)} ₽`,
			sum_with_discount_formatted: `${formatter.format(
				sumWithDiscount
			)} ₽`,
			discount_formatted: `${formatter.format(discount)} ₽`,
		},
	};
}