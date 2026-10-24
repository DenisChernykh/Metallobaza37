import { jsonFetch } from "../shared/http/jsonFetch";

export type HttpMethod = "GET" | "POST" | "PATCH" | "DELETE";

const ROUTER_URL = "/wp-content/plugins/VG_Catalog/router.php";


export async function callRouter<T>(
	method: HttpMethod,
	routeName: string,
	payload?: Record<string, unknown>
): Promise<T> {
	if (method === "GET") {
		const url = `${ROUTER_URL}?route=${encodeURIComponent(routeName)}`;
		return jsonFetch<T>(url, { method: "GET" });
	}

	return jsonFetch<T>(ROUTER_URL, {
		method,
		json: { route: routeName, ...(payload ?? {}) },
	});
}