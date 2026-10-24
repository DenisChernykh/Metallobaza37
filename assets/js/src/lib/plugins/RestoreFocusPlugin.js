export function RestoreFocusPlugin() {
  let lastActive = null;
  return {
    beforeOpen() { lastActive = document.activeElement; },
    onClose() {
      if (lastActive && typeof lastActive.focus === 'function') {
        lastActive.focus({ preventScroll: true });
      }
      lastActive = null;
    },
  };
}