export const clamp = (number, min, max) => Math.max(min, Math.min(max, number));
export const toInt = (value, defaultValue = 0) => {
  const number = parseInt(value, 10);
  return Number.isFinite(number) ? number : defaultValue;
};
export const debounce = (fn, delay = 300) => {
  let timer;
  return (...args) => {
    clearTimeout(timer);
    timer = setTimeout(() => fn(...args), delay);
  };
};
export function pluralRu(count, forms) {
  const n = Math.abs(Math.trunc(count));
  const mod100 = n % 100;
  const mod10 = n % 10;
  const cases = [2, 0, 1, 1, 1, 2];
  const idx = mod100 > 4 && mod100 < 20 ? 2 : cases[Math.min(mod10, 5)];
  return forms[idx];
}

export function formatProductCount(count) {
  const n = Math.abs(count);
  const mod10 = n % 10;
  const mod100 = n % 100;

  let word;
  if (mod10 === 1 && mod100 !== 11) {
    word = 'товар';
  } else if (mod10 >= 2 && mod10 <= 4 && (mod100 < 12 || mod100 > 14)) {
    word = 'товара';
  } else {
    word = 'товаров';
  }

  return `${count} ${word}`;
}
