import OverlayManager from '../OverlayManager';
import Disclosure from '../Disclosure';
import { CloseOnBackdropPlugin } from '../plugins/CloseOnBackdropPlugin';
import { CloseOnEscPlugin } from '../plugins/CloseOnEscPlugin';
import { FocusTrapPlugin } from '../plugins/FocusTrapPlugin';
import { OverlayPlugin } from '../plugins/OverlayPlugin';
import { RestoreFocusPlugin } from '../plugins/RestoreFocusPlugin';
import { ScrollLockPlugin } from '../plugins/ScrollLockPlugin';
import { CloseOnOutsidePlugin } from '../plugins/CloseOnOutsidePlugin';

const registry = new Map();

export function createModal(root, { overlaySelector = '.site-overlay' } = {}) {
  const overlay = new OverlayManager({ selector: overlaySelector });
  const plugins = [
    OverlayPlugin(overlay),
    ScrollLockPlugin(),
    CloseOnEscPlugin(),
    CloseOnOutsidePlugin(),
    CloseOnBackdropPlugin(),
    FocusTrapPlugin(),
    RestoreFocusPlugin(),
  ];
  const inst = new Disclosure(root, { plugins });
  root.__disclosure = inst;
  registry.set('#' + (root.id || ''), inst);
  return inst;
}

export function initModals(scope = document) {
  scope.querySelectorAll('[data-disclosure].modal').forEach((root) => {
    if (!registry.has('#' + root.id)) createModal(root);
  });

  scope.addEventListener('click', (e) => {
    const openBtn = e.target.closest('[data-modal-open]');
    const closeBtn = e.target.closest('[data-modal-close]');
    if (openBtn) {
      const sel = openBtn.getAttribute('data-modal-open');
      const inst = registry.get(sel);
      if (inst) inst.open(openBtn);
    }
    if (closeBtn) {
      const sel = closeBtn.getAttribute('data-modal-close');
      const inst = registry.get(sel);
      if (inst) inst.close();
    }
  });
}

export const openModal = (selector, initiator = null) =>
  registry.get(selector)?.open(initiator);
export const closeModal = (selector) => registry.get(selector)?.close();
