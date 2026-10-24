export function ScrollLockPlugin() {
  let scrollbarWidth = 0;
  function getScrollbarWidth() {
    return innerWidth - document.documentElement.clientWidth;
  }
  return {
    onOpen() {
      scrollbarWidth = getScrollbarWidth();
      document.body.style.overflow = 'hidden';
    },
    onClose() {
      document.body.style.overflow = '';
    },
  };
}
