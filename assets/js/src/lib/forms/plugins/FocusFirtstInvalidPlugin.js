export function FocusFirstInvalidPlugin() {
  return {
    onSubmitFail(instance) {
      const invalid = instance.form.querySelector('[aria-invalid="true"]');
      if (invalid) invalid.focus({ preventScroll: false });
    },
  };
}
