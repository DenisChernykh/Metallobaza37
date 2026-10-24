import noUiSlider from 'nouislider';

export function initNoUiSlider() {
  const slider = document.getElementById('price-range');
  if (!slider) return;

  const root = slider.closest('[data-range]');
  const min = parseInt(root?.dataset.min) || 0;
  const max = parseInt(root?.dataset.max) || 100000;
  const step = parseInt(root?.dataset.step) || 100;
  const startMin = parseInt(root?.dataset.startMin) || min;
  const startMax = parseInt(root?.dataset.startMax) || max;

  noUiSlider.create(slider, {
    start: [startMin, startMax],
    connect: true,
    step,
    range: { min, max },
  });

  const minInput = document.getElementById('price-min');
  const maxInput = document.getElementById('price-max');

  // → значения в инпуты
  slider.noUiSlider.on('update', (values, handle) => {
    const value = Math.round(values[handle]);
    if (handle === 0 && minInput) minInput.value = value;
    if (handle === 1 && maxInput) maxInput.value = value;
  });

  // ← значения из инпутов
  const clamp = (val) => Math.min(Math.max(val, min), max);
  const commitFromInputs = () => {
    const lo = clamp(parseInt(minInput?.value) || startMin);
    const hi = clamp(parseInt(maxInput?.value) || startMax);
    slider.noUiSlider.set([Math.min(lo, hi), Math.max(lo, hi)]);
  };

  minInput?.addEventListener('change', commitFromInputs);
  maxInput?.addEventListener('change', commitFromInputs);



}
