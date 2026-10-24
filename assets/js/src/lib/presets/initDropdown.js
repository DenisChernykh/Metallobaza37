import OverlayManager from '../OverlayManager';
import { OverlayPlugin } from '../plugins/OverlayPlugin';
import { CloseSiblingsPlugin } from '../plugins/CloseSiblingsPlugin';
import Disclosure from '../Disclosure';
import { ScrollLockPlugin } from '../plugins/ScrollLockPlugin';
import { CloseOnEscPlugin } from '../plugins/CloseOnEscPlugin';
import { FocusTrapPlugin } from '../plugins/FocusTrapPlugin';
import { CloseOnBackdropPlugin } from '../plugins/CloseOnBackdropPlugin';
import { RestoreFocusPlugin } from '../plugins/RestoreFocusPlugin';
import { CloseOnOutsidePlugin } from '../plugins/CloseOnOutsidePlugin';

const overlay = new OverlayManager();

export function initDropdown(el) {
  const plugins = [];

  plugins.push(CloseOnOutsidePlugin());
  if (el.dataset.overlay === 'true') {
    plugins.push(OverlayPlugin(overlay));
    plugins.push(ScrollLockPlugin());
  }
  if (el.dataset.closeSiblings === 'true') {
    plugins.push(CloseSiblingsPlugin());
  }
  if (el.dataset.escClose === 'true') {
    plugins.push(CloseOnEscPlugin());
  }
  if (el.dataset.backdropClose === 'true') {
    plugins.push(CloseOnBackdropPlugin());
  }
  if (el.dataset.focusTrap === 'true') {
    plugins.push(FocusTrapPlugin());
  }
  if (el.dataset.restoreFocus === 'true') {
    plugins.push(RestoreFocusPlugin());
  }
  const instance = new Disclosure(el, { plugins });
  el.__disclosure = instance;
}
