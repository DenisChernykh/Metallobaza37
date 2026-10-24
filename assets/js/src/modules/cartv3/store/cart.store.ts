export { };
const seg1 = '@wordpress';
const seg2 = '/interactivity';
const spec = seg1 + seg2;
const IAPI = await import(/* @vite-ignore */ spec);
import { Cart, CartItem, CartRepository } from "../domain/cart.types";
import { CartApiRepository } from "../infra/cart.api"
import { formatProductCount, } from "../../../utils/utils";

const { store, getContext, getElement, withSyncEvent } = IAPI;
type ItemCtx = { items: CartItem }
type CheckboxEvent = Event & { currentTarget: HTMLInputElement };
type QtyInputEvent = Event & { currentTarget: HTMLInputElement };
type CheckedMap = Record<string, boolean>
type PendingMap = Record<string, boolean>
interface CartState {
	cart: Cart | null;
	isLoading: boolean;
	error: string | null;
	pendingByItem: PendingMap;
	pendingGlobal: boolean;
}
type ProductOption = {
	id: string;
	title: string;
	active: boolean;

}

type ProductOptionGroup = {
	groupId: string;
	title: string;
	required: boolean;
	multiple: boolean;
	activeVariant: string;
	options: ProductOption[];
}

type OptionLike = {
	id?: string | number;
	post_id?: string | number;
};

type ItemWithOptions = {
	selectedOptions?: OptionLike[] | null;
	options?: OptionLike[] | null;
};

export function getItemOptionIds(item: ItemWithOptions | null | undefined): string[] {
	const selected: OptionLike[] = item?.selectedOptions ?? item?.options ?? [];

	const ids = selected
		.map(option => option.id ?? option.post_id)
		.filter((id): id is string | number => id !== undefined && id !== null)
		.map(id => String(id));

	return Array.from(new Set(ids)).sort();
}

function arraysEqualSorted(a: string[], b: string[]) {
	if (a.length !== b.length) return false;
	for (let i = 0; i < a.length; i++) if (a[i] !== b[i]) return false;
	return true;
}



export function activateOptionById(
	groups: ProductOptionGroup[],
	optionId: string
): void {
	for (const group of groups) {
		const groupHasTarget = group.options.some((option) => option.id === optionId);
		if (!groupHasTarget) continue;

		for (const option of group.options) {
			option.active = option.id === optionId;
		}
		group.activeVariant = optionId;
		break;
	}
}

function getSelectedOptionIds() {
	const variantGroups = document.querySelectorAll<HTMLElement>('[data-variant-group]');
	const optionIds: number[] = [];

	variantGroups.forEach((group) => {
		const activeButton = group.querySelector<HTMLButtonElement>('[data-variant-button].active');
		if (activeButton && activeButton.dataset.optionId) {
			optionIds.push(parseInt(activeButton.dataset.optionId));
		}
	});

	return optionIds;
}

const repo: CartRepository = new CartApiRepository();

function findItemById(cart: Cart | null, itemId: string | undefined): CartItem | undefined {
	if (!cart || !itemId) return undefined;
	return cart.items.find(item => item.id === itemId);
}

function findItemByProductId(cart: Cart | null, productId: number | string | undefined): CartItem | undefined {
	if (!cart || !productId) return undefined;
	return cart.items.find((item) => String(item.productId) === String(productId));
}

function findItemsByProductId(cart: Cart | null, productId: number | string | undefined): CartItem[] | undefined {
	if (!cart || !productId) return undefined;
	return cart.items.filter((item) => String(item.productId) === String(productId));
}

function parseQtyInput(value: string): number | null {
	const trimmed = String(value ?? '').trim();
	if (trimmed === '') return null;
	const amount = Number(trimmed);
	if (!Number.isFinite(amount)) return null;
	return Math.max(0, Math.floor(amount));
}

export const { state, actions, callbacks } = store("cart", {
	state: {
		checkedById: {} as CheckedMap,
		isLoading: false,
		error: null,
		pendingByItem: {} as PendingMap,
		pendingGlobal: false,

		visibleIds() {
			const items: CartItem[] = (state.cart?.items ?? []);
			return items.map(item => String(item.productId))
		},
		areAllChecked() {
			const ids = this.visibleIds();
			return ids.length > 0 && ids.every(id => !!this.checkedById[id])
		},
		isIndeterminate() {
			const ids = this.visibleIds();
			const number = ids.filter(id => this.checkedById[id]).length
			return number > 0 && number < ids.length
		},
		isCurrentItemChecked() {
			const { items } = getContext();
			const id = String(items.productId ?? items.id)
			return !!this.checkedById[id]
		},
		hasItems() {
			const items = state.cart?.items;
			return Array.isArray(items) && items.length > 0
		},
		get isEmpty() {
			return !this.hasItems
		},
		get itemsCount() {
			return state.cart?.items.length ?? 0;
		},

		promoTitle() {
			return state.hasItems ? 'Другие товары' : 'Вам понравится'
		},
		itemsCountText() {
			return formatProductCount(this.itemsCount)
		},
		quantityForProduct() {

			const { productId } = getContext()
			const item = findItemByProductId(state.cart, productId)
			return item?.quantity
		},
		quantityForSingleProduct() {
			const { productId } = getContext();
			const items = findItemsByProductId(state.cart, productId) ?? [];
			if (items.length === 0) return 0;

			const activeOptionIds = (state.getActiveOptionIds?.() ?? [])
				.map(String)
				.sort();


			if (activeOptionIds.length === 0) {
				return items.reduce((sum, it) => sum + (Number(it.quantity) || 0), 0);
			}


			const found = items.find(it => arraysEqualSorted(getItemOptionIds(it), activeOptionIds));
			return found ? (Number(found.quantity) || 0) : 0;
		},
		selectedProductIds() {
			const idsMap: Record<string, boolean> = state.checkedById ?? {};
			const items: ReadonlyArray<CartItem> = state.cart?.items ?? [];
			const presentIds: string[] = items.map((item) =>
				String(item.productId ?? item.id)
			);
			return presentIds.filter((id) => !!idsMap[id]);

		},
		hasSelected() {
			return this.selectedProductIds().length > 0
		},
		deleteSelectedLabel() {
			const number = this.selectedProductIds().length;
			return number > 0 ? `Удалить выбранное ${number}` : 'Удалить выбранное';
		},
		isDeleteDisabled() {
			return this.pendingGlobal || !this.hasSelected();
		},
		groupOptions() {
			const { item } = getContext();
			return Array.isArray(item?.options) ? item.options : [];
		},
		getActiveOptionIds() {
			const optionsGroups: ProductOptionGroup[] = state.product.optionsGroups ?? [];
			return optionsGroups.flatMap(group =>
				Array.isArray(group.options)
					? group.options.filter(option => option.active === true).map(option => Number(option.id))
					: []
			);
		}



	},
	actions: {
		async removeSelected() {

			const productIds = state.selectedProductIds();

			if (productIds.length === 0) return;
			state.error = null;
			state.pendingGlobal = true;
			try {
				for (const productId of productIds) {
					const item = findItemByProductId(state.cart, productId);
					if (!item?.id) {
						delete state.checkedById[productId];
						continue;
					}
					state.pendingByItem[item.id] = true;

					state.cart = await repo.removeItem(item.id)
				}
			}
			catch (error: any) {
				state.error = error?.message ?? "Не удалось удалить позицию";
			} finally {
				state.pendingGlobal = false;
			}
		},
		toggleAllChecked(e: CheckboxEvent) {
			const checked = !!e.currentTarget.checked
			for (const id of state.visibleIds()) {
				state.checkedById[id] = checked
			}
		},
		toggleCheck(e: CheckboxEvent) {
			const { items } = getContext();

			const id = String(items.productId ?? items.id)
			state.checkedById[id] = !!e.currentTarget.checked
		},
		async addCurrentProduct() {

			const element = getElement();

			const button = element?.ref;
			const options = button?.closest('[data-options]');
			const item = getContext();
			if (options?.dataset.options === '1') {
				window.location.href = `${item.href}`
			}
			const productId = item?.productId
		
			const quantity = Number(item?.quantity) || 1;
			if (productId == null || Number.isNaN(quantity) || quantity <= 0) return
			try {
				const optionIds = getSelectedOptionIds();
				const rezCheckbox = document.querySelector<HTMLInputElement>(
					'.product-services__checkbox'
				);

				const rez = rezCheckbox && rezCheckbox.checked ? 1 : 0;
				state.error = null;
				state.pendingGlobal = true;
				if (optionIds && rez) {

					state.cart = await repo.addItem({ productId, quantity, optionIds, rez });
				} else if (optionIds) {

					state.cart = await repo.addItem({ productId, quantity, optionIds, rez });
				} else if (rez) {
					state.cart = await repo.addItem({ productId, quantity, rez });
				} else {
					state.cart = await repo.addItem({ productId, quantity });
				}

			} catch (error: any) {
				state.error = error?.message ?? "Не удалось добавить товар в корзину";
			} finally {
				state.pendingGlobal = false;
			}
		},
		async incQuantity() {
			const { items } = getContext() as ItemCtx;
			if (!items) return;
			await actions.updateQuantity({ itemId: items.id, quantity: items.quantity + 1 });

		},
		async decQuantity() {
			const { items } = getContext() as ItemCtx;

			if (!items) return;
			if (items.quantity <= 1) {
				await actions.removeItemById(items.id)
				return;
			}
			await actions.updateQuantity({ itemId: items.id, quantity: items.quantity - 1 });
		},
			async incForProduct() {
				const ctx = getContext()
				const rezCheckbox = document.querySelector<HTMLInputElement>(
					'.product-services__checkbox'
				);

			const rez = rezCheckbox && rezCheckbox.checked ? 1 : 0;
			const item = findItemByProductId(state.cart, ctx.productId);
			if (!item) return;
				await actions.updateQuantity({ itemId: item.id, quantity: item.quantity + 1 }, rez);

			},
			async incForSingleProduct() {
				const { productId } = getContext();
				const items = findItemsByProductId(state.cart, productId) ?? [];

				const active = (state.getActiveOptionIds?.() ?? []).map(String).sort();

				const row = items.find(it => arraysEqualSorted(getItemOptionIds(it), active));
				if (row) {
					await actions.updateQuantity({ itemId: row.id, quantity: row.quantity + 1 });
				}
			},

			async decForSingleProduct() {
				const { productId } = getContext();
				const items = findItemsByProductId(state.cart, productId) ?? [];
				const active = (state.getActiveOptionIds?.() ?? []).map(String).sort();
				if (active.length === 0) return;

				const row = items.find(it => arraysEqualSorted(getItemOptionIds(it), active));
				if (!row) return;

				if ((row.quantity ?? 0) <= 1) {
					await actions.removeItemById(row.id);
				} else {
					await actions.updateQuantity({ itemId: row.id, quantity: row.quantity - 1 });
				}
			},
			async decForProduct() {
				const ctx = getContext();
				const item = findItemByProductId(state.cart, ctx.productId);
				if (!item) return;
				if (item.quantity <= 1) {
					await actions.removeItemById(item.id)
					return;
				}
				await actions.updateQuantity({ itemId: item.id, quantity: item.quantity - 1 });
			},
			async setQuantityForProductFromInput(e: QtyInputEvent) {
				const ctx = getContext();
				const productId = ctx?.productId;
				if (!productId) return;

				const nextQty = parseQtyInput(e.currentTarget.value);
				if (nextQty === null) return;

				const item = findItemByProductId(state.cart, productId);
				if (!item) {
					if (nextQty <= 0) return;
					try {
						state.error = null;
						state.pendingGlobal = true;
						state.cart = await repo.addItem({ productId, quantity: nextQty });
					} catch (error: any) {
						state.error = error?.message ?? "Не удалось добавить товар в корзину";
					} finally {
						state.pendingGlobal = false;
					}
					return;
				}

				if (nextQty <= 0) {
					await actions.removeItemById(item.id);
					return;
				}

				if (Number(item.quantity) === nextQty) return;
				await actions.updateQuantity({ itemId: item.id, quantity: nextQty });
			},
			async setQuantityForSingleProductFromInput(e: QtyInputEvent) {
				const ctx = getContext();
				const productId = ctx?.productId;
				if (!productId) return;

				const nextQty = parseQtyInput(e.currentTarget.value);
				if (nextQty === null) return;

				const items = findItemsByProductId(state.cart, productId) ?? [];
				const active = (state.getActiveOptionIds?.() ?? []).map(String).sort();

				if (active.length === 0) {
					const item = findItemByProductId(state.cart, productId);
					if (!item) return;
					if (nextQty <= 0) await actions.removeItemById(item.id);
					else if (Number(item.quantity) !== nextQty) {
						await actions.updateQuantity({ itemId: item.id, quantity: nextQty });
					}
					return;
				}

				const row = items.find((it) => arraysEqualSorted(getItemOptionIds(it), active));
				if (!row) {
					if (nextQty <= 0) return;
					const optionIds = active
						.map((id) => Number(id))
						.filter((n) => Number.isFinite(n));
					if (optionIds.length === 0) return;

					try {
						const rezCheckbox = document.querySelector<HTMLInputElement>(
							'.product-services__checkbox'
						);
						const rez = rezCheckbox && rezCheckbox.checked ? 1 : 0;

						state.error = null;
						state.pendingGlobal = true;
						state.cart = await repo.addItem({ productId, quantity: nextQty, optionIds, rez });
					} catch (error: any) {
						state.error = error?.message ?? "Не удалось добавить товар в корзину";
					} finally {
						state.pendingGlobal = false;
					}
					return;
				}

				if (nextQty <= 0) {
					await actions.removeItemById(row.id);
					return;
				}

				if (Number(row.quantity) === nextQty) return;
				await actions.updateQuantity({ itemId: row.id, quantity: nextQty });
			},
			async setQuantityForCartItemFromInput(e: QtyInputEvent) {
				const { items } = getContext() as ItemCtx;
				if (!items?.id) return;

				const nextQty = parseQtyInput(e.currentTarget.value);
				if (nextQty === null) return;

				if (nextQty <= 0) {
					await actions.removeItemById(items.id);
					return;
				}

				if (Number(items.quantity) === nextQty) return;
				await actions.updateQuantity({ itemId: items.id, quantity: nextQty });
			},
			async updateQuantity(input: { itemId: string; quantity: number, optionIds?: number[], rez?: number }) {
				const key = input.itemId;
				try {
					state.error = null;
					state.pendingByItem[key] = true;
					state.cart = await repo.updateItem({ itemId: key, quantity: input.quantity });
				} catch (error: any) {
					state.error = error?.message ?? "Не удалось обновить позицию";
				} finally {
					state.pendingByItem[key] = false;
				}
			},
			async removeItem() {
				const { items } = getContext() as ItemCtx;
				const itemId = items?.id;
				if (!itemId) return;
				try {
					state.error = null;
					state.pendingByItem[itemId] = true;
					state.cart = await repo.removeItem(itemId);
				} catch (error: any) {
					state.error = error?.message ?? "Не удалось удалить позицию";
				} finally {
					state.pendingByItem[itemId] = false;
				}
			},
			async clear() {
				try {
					state.error = null;
					state.pendingGlobal = true;
					state.cart = await repo.clear();
				} catch (error: any) {
					state.error = error?.message ?? "Не удалось очистить корзину";
				} finally {
					state.pendingGlobal = false;
				}
			},
			async removeItemById(itemId: string) {
				try {
					state.error = null;
					state.pendingByItem[itemId] = true;
				state.cart = await repo.removeItem(itemId);
			} catch (error: unknown) {
				state.error = error instanceof Error ? error.message : "Не удалось удалить позицию";
			} finally {
				state.pendingByItem[itemId] = false;
			}
		},
		async changeActiveVariant() {
			const { opts } = getContext();
			activateOptionById(state.product.optionsGroups, opts.id)
			state.getActiveOptionIds();
		},
		async addCurrentProductWithOptions() {
			const { productId } = getContext();
			const optionIds = state.getActiveOptionIds();
			const item = getContext();
			const quantity = Number(item?.quantity) || 1;
			try {
				const rezCheckbox = document.querySelector<HTMLInputElement>(
					'.product-services__checkbox'
				);

				const rez = rezCheckbox && rezCheckbox.checked ? 1 : 0;
				state.error = null;
				state.pendingGlobal = true;
				state.cart = await repo.addItem({ productId, quantity, optionIds, rez });


			} catch (error: any) {
				state.error = error?.message ?? "Не удалось добавить товар в корзину";
			} finally {
				state.pendingGlobal = false;
			}

		},
		navigateCatalog: withSyncEvent(function* (event: Event) {


			event.preventDefault();


			const element = getElement();
			let anchor: HTMLAnchorElement | null = null;

			if (element?.ref instanceof HTMLAnchorElement) {
				anchor = element.ref as HTMLAnchorElement;
			} else if (event.currentTarget instanceof HTMLAnchorElement) {
				anchor = event.currentTarget as HTMLAnchorElement;
			} else {
				const target = event.target as HTMLElement | null;
				anchor = target?.closest('a') ?? null;
			}

			if (!anchor || !anchor.href) {
				return;
			}

			const r1 = '@wordpress';
			const r2 = '/interactivity-router';
			const routerSpec = r1 + r2;

			type RouterActions = {
				navigate: (href: string) => Promise<void>;
			};

			const { actions: routerActions } = (yield import(
				/* @vite-ignore */ routerSpec
			)) as {
				actions: RouterActions;
			};


			yield routerActions.navigate(anchor.href);
		}),
	},

});
