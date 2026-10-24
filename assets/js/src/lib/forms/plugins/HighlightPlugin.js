export function HighlightPlugin({
  invalidClass = 'is-invalid',
  validClass = 'is-valid',
} = {}) {
  return {
    onFieldInvalid(_instance, field) {
      field.classList.remove(validClass);
      field.classList.add(invalidClass);
    },
    onFieldValid(_instance, field) {
      field.classList.remove(invalidClass);
      field.classList.add(validClass);
    },
  };
}
