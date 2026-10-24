// svgo.config.mjs  (SVGO v4, ESM)
export default {

  js2svg: {
    pretty: true, // форматировать вывод
    indent: 2, // отступы (число пробелов)
  },
  multipass: true,
  plugins: [
    // цвета -> currentColor (кроме 'none'/'transparent')
    { name: 'convertColors', params: { currentColor: true } },

    // вычищаем мусорные атрибуты (не трогаем xmlns, aria-*, role, id, fill/stroke)
    { name: 'removeAttrs', params: { attrs: '(style|class|data-*)' } },

    // убираем width/height у корневого <svg>
    { name: 'removeDimensions' },

    // лёгкие и безопасные оптимизации
    'collapseGroups',
    'convertPathData',
  ],
};
