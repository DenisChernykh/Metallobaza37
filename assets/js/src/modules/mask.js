import IMask from 'imask';

export function initMask() {
  const elementList = document.querySelectorAll("input[type='tel']");
  const maskOptions = {
    mask: '+{7} (000) 000-00-00',
  };

  if (elementList) {
    elementList.forEach((element) => {
      const mask = IMask(element, maskOptions);
    });
  }
}
