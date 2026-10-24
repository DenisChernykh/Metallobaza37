export type JsonRequestInit = RequestInit & { json?: unknown };
interface ErrorResponse {
	message?: string;
	[key: string]: unknown;
}

export async function jsonFetch<T>(url: string, init: JsonRequestInit = {}): Promise<T> {
	const { json, headers, ...rest } = init;
	const response = await fetch(url, {
		credentials: "include",
		headers: {
			"Content-Type": "application/json",
			"Accept": "application/json",
			...(headers ?? {}),
		},
		body: json != null ? JSON.stringify(json) : undefined, ...rest
	})
	if (!response.ok) {
		let message = `HTTP ${response.status}`;
		try {
			const data = (await response.json()) as ErrorResponse;
			if (typeof data.message === "string") message = data.message;
		} catch { }
		throw new Error(message);
	}
	return (await response.json()) as T;
}
