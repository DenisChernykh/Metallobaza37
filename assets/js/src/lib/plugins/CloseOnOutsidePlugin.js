export function CloseOnOutsidePlugin({
  event = 'pointerdown',
  primaryOnly = true,
  ignoreSelector,
} = {}) {
  let onDown;
  return {
    onOpen(instance) {
      const { root } = instance;
      onDown = (e) => {
        if (
          primaryOnly &&
          e instanceof PointerEvent &&
          e.pointerType === 'mouse' &&
          e.button !== 0
        )
          return;
        const target = e.target;
        if (ignoreSelector && target.closest(ignoreSelector)) return;
        if (!root.contains(target)) instance.close();
      };
      document.addEventListener(event, onDown);
    },
    onClose() {
      document.removeEventListener(event, onDown);
      onDown = null;
    },
  };
}
