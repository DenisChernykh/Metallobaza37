export const OverlayPlugin = (overlayManager) => ({
  onOpen() {
    overlayManager.open();
  },
  onClose() {
    overlayManager.close();
  },
});
