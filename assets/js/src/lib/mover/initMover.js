import { Mover } from './Mover';

export function initMover(el) {
  const instance = new Mover(el);
  el.__tabs = instance;
}
