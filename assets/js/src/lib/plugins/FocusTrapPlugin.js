export function FocusTrapPlugin() {
  let onKeyDown;

  const getFocusable = (panel) =>
    [
      ...panel.querySelectorAll(
        'a[href], button, textarea, input, select, [tabindex]:not([tabindex="-1"])'
      ),
    ].filter(
      (el) =>
        !el.hasAttribute('disabled') &&
        el.getAttribute('aria-hidden') !== 'true'
    );

  return {
    onOpen(instance) {
      const panel = instance.panel;
      if (!panel) return;

      (getFocusable(panel)[0] || panel).focus({ preventScroll: true });

      onKeyDown = (e) => {
        if (e.key !== 'Tab') return;
        const focusable = getFocusable(panel);
        if (!focusable.length) return;

        const first = focusable[0];
        const last = focusable[focusable.length - 1];

        if (e.shiftKey) {
          if (
            document.activeElement === first ||
            !panel.contains(document.activeElement)
          ) {
            last.focus();
            e.preventDefault();
          }
        } else {
          if (
            document.activeElement === last ||
            !panel.contains(document.activeElement)
          ) {
            first.focus();
            e.preventDefault();
          }
        }
      };
      panel.addEventListener('keydown', onKeyDown);
    },

    onClose(instance) {
      if (instance?.panel && onKeyDown) {
        instance.panel.removeEventListener('keydown', onKeyDown);
      }
      onKeyDown = null;
    },
  };
}
