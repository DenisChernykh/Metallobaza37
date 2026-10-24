export function initSelect() {
  const title = document.querySelector('.select__title');
  const optionsList = document.querySelector('.select__options');
  const options = document.querySelectorAll('.select__option');
	const currentOption = document.querySelector('#current_sort');
	if (!title || !optionsList || !options) return;
  function toggle() {
    title.addEventListener('click', () => {
      optionsList.classList.toggle('open');

      title.classList.toggle('open');
    });
  }

  function close() {
    optionsList.classList.remove('open');
    title.classList.remove('open');
  }

  function renderCheck() {
    options.forEach((option) => {
      option.querySelectorAll('span svg').forEach((svg) => {
        const parent = svg.closest('span');
        if (parent) parent.remove();
      });

      const optionText = option.textContent?.trim();
      const currentText = currentOption.textContent?.trim();

      if (optionText === currentText) {
        const check = document.createElement('span');
        check.innerHTML = `<svg width="13" height="9" viewBox="0 0 13 9" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M12 1L4.4375 8L1 4.81818" stroke="#100F15" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>`;
        option.appendChild(check);
      }
    });
  }

  function setActive() {
    options.forEach((option) => {
      option.addEventListener('click', () => {
        currentOption.textContent = option.textContent;
        renderCheck();
        close();
      });
    });
  }

  renderCheck();
  setActive();

  toggle();
}
