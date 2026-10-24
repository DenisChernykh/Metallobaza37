export function initToggleViewMode() {
  const toggleButtons = document.querySelectorAll('[data-view]');
  toggleButtons.forEach((button) => {
    if (button.classList.contains('active')) {
      makeVisible(button.dataset.view);
    }
  });
  function makeVisible(view) {
    const productList = document.querySelector('.product-list');
    const productGrid = document.querySelector('.product-grid');

    if (view === 'grid') {
      if(productList){
        productList.classList.add('hidden');
      }
      if(productGrid){
        productGrid.classList.remove('hidden');
      }
    } else if (view === 'list') {
      if(productGrid){
        productGrid.classList.add('hidden');
      }
      if(productList){
        productList.classList.remove('hidden');
      }
    }
  }
  function toggleView() {
    toggleButtons.forEach((button) => {
      button.addEventListener('click', () => {
        toggleButtons.forEach((btn) => {
          btn.classList.remove('active');
        });
        button.classList.add('active');
        makeVisible(button.dataset.view);
      });
    });
  }
  toggleView();
}
