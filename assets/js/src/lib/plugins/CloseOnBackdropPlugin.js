export function CloseOnBackdropPlugin({
  event = 'pointerdown',
  primaryOnly = true,
  backdropSelector = '[data-backdrop]',
} = {}) {
  let onDown;
  return {
    onOpen(instance) {
      const { root, panel } = instance;
      const backdrop = root.querySelector(backdropSelector) || root;
      onDown = (e) => {
        if (
          primaryOnly &&
          e instanceof PointerEvent &&
          e.pointerType === 'mouse' &&
          e.button !== 0
        )
          return;
        const target = e.target;
        if (target === backdrop) {
          instance.close();
          return;
        }
        if (root.contains(target) && panel && !panel.contains(target)) {
          instance.close();
        }
			};
			root.addEventListener(event, onDown);
    },
		onClose(instance) {
			instance.root.removeEventListener(event, onDown);
			onDown = null;
		}
  };
}
