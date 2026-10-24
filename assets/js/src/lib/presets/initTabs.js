import Tabs from '../Tabs';

export function initTabs(el) {
  const instance = new Tabs(el);
  el.__tabs = instance;
}
