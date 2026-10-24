export function CloseOnEscPlugin() {
  return {
    onOpen(instance) {
      instance.__onEsc = (e) => {
        if (e.key === 'Escape') instance.close();
      };
      document.addEventListener('keydown', instance.__onEsc);
    },
    onClose(instance) {
      if (instance.__onEsc) {
        document.removeEventListener('keydown', instance.__onEsc);
        instance.__onEsc = null;
      }
    },
  };
}
