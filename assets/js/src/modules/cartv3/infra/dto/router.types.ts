
export interface RouterListResponse {
	success: boolean;
	action?: string;
	data?: {
		cart?: RouterItem[];
		pagination?: {
			current_page?: number;
			last_page?: number;
			per_page?: number;
			total?: number;
			from?: number;
			to?: number;
			[key: string]: unknown;
		};
	};
}


export interface RouterItem {
	id: string;
	quantity: number | string;
	product_id?: number | string;
	product?: {
		id?: number | string;
		title?: string;
		price?: number | string | null;
		sale_price?: number | string | null;
		sku?: string;
		post_id?: number | string;
		unit?: {
			name?: string;
		};
		[key: string]: unknown;
		isHit: boolean;
		hasSale: boolean
	};
	options?: unknown[];
	[key: string]: unknown;
	rez?: number
}