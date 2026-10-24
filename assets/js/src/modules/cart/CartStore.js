import { clamp, toInt } from '../../utils/utils';

export class CartStore {
  constructor({ persistKey = 'cart:v1', transport = null } = {}) {
    this._items = new Map();
    this._subs = new Set();
    this._persistKey = persistKey;
    this._transport = transport;

    this._load();
  }

  subscribe(fn) {
    this._subs.add(fn);
    return () => this._subs.delete(fn);
  }
  _emit(changedId = null) {
    const items = this.getItems();
    for (const fn of this._subs) fn({ items, changedId });
  }

  getItems() {
    return Array.from(this._items.values());
  }
  getItem(id) {
    return this._items.get(String(id)) || null;
  }
  getQty(id) {
    return this.getItem(id)?.qty || 0;
  }
  getTotalQty() {
    return this.getItems().reduce((sum, item) => sum + item.qty, 0);
  }
  removeMany(ids = []) {
    let changed = false;
    for (const id of ids) {
      const key = String(id);
      if (this._items.has(key)) {
        this._items.delete(key);
        changed = true;
      }
    }
    if (changed) {
      this._persist();
      this._emit();
    }
  }
  add(id, meta = {}) {
    const current = this.getItem(id) || {
      id: String(id),
      qty: 0,
      status: 'idle',
    };
    return this.setQty(id, current.qty + 1, meta);
  }

  remove(id, meta = {}) {
    const current = this.getItem(id) || {
      id: String(id),
      qty: 0,
      status: 'idle',
    };
    return this.setQty(id, current.qty - 1, meta);
  }
  setQty(id, nextQty, meta = {}) {
    const key = String(id);
    const prev = this.getItem(key) || { id: key, qty: 0, status: 'idle' };
    const stock = toInt(meta.stock ?? prev.stock ?? Infinity, Infinity);
    const qty = clamp(
      toInt(nextQty, 0),
      0,
      Number.isFinite(stock) ? stock : Infinity
    );

    if (
      qty === prev.qty &&
      meta.price == null &&
      meta.title == null &&
      meta.image == null &&
      meta.stock == null
    ) {
      return Promise.resolve();
    }
    if (qty === 0) {
      this._items.delete(key);
      this._persist();
      this._emit(key);
      return Promise.resolve();
    }
    const next = {
      id: key,
      qty,
      stock: meta.stock ?? prev.stock,
      price: meta.price ?? prev.price,
      title: meta.title ?? prev.title,
      image: meta.image ?? prev.image,
      status: this._transport ? 'pending' : 'idle',
    };
    this._items.set(key, next);
    this._persist();
    this._emit(key);

    if (!this._transport) {
      return Promise.resolve();
    }

    const payload = { id: key, qty };
    return this._transport(payload)
      .then(() => {
        const ok = this.getItem(key);
        if (!ok) return;
        ok.status = 'idle';
        this._items.set(key, ok);
        this._persist();
        this._emit(key);
      })
      .catch((error) => {
        this._items.set(key, { ...prev, status: 'error' });
        this._persist();
        this._emit(key);
        throw error;
      });
  }
  clear() {
    if (this._items.size === 0) return;
    this._items.clear();
    this._persist();
    this._emit(null);
  }
  _persist() {
    try {
      const json = JSON.stringify(this.getItems());
      localStorage.setItem(this._persistKey, json);
    } catch {}
  }
  _load() {
    try {
      const raw = localStorage.getItem(this._persistKey);
      if (!raw) return;
      const array = JSON.parse(raw);
      if (!Array.isArray(array)) return;
      this._items.clear();

      for (const item of array) {
        if (!item || item.id == null) continue;
        this._items.set(String(item.id), {
          ...item,
          id: String(item.id),
          qty: Math.max(0, toInt(item.qty, 0)),
          status: 'idle',
        });
      }
    } catch {}
  }
}
