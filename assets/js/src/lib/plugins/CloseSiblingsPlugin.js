export const CloseSiblingsPlugin = () => ({
  onOpen(instanse) {
    const siblings = Array.from(instanse.root.parentElement.children).filter(
      (el) =>
        el !== instanse.root && el.dataset.disclosure && el.__disclosure?.isOpen
    );
    siblings.forEach((sibling) => sibling.__disclosure?.close());
  },
});
