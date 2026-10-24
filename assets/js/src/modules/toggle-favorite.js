export function initToggleFavorite() {
  document.addEventListener('click', (e) => {
    const btn = (e.target).closest('.favorite-icon--product');
    if (!btn) return;

    const productId = btn.dataset.productId;
    if (!productId) return;

    const isActive = btn.classList.contains('active');
    btn.classList.toggle('active');

    // синхронизация всех кнопок этого товара
    document.querySelectorAll(`.favorite-icon--product[data-product-id="${productId}"]`)
      .forEach((el) => el === btn ? null : el.classList.toggle('active', !isActive));

    toggleProductFavorite(productId);
  });
}

function toggleProductFavorite(productId) {
  fetch('/wp-content/plugins/VG_Catalog/router.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      route: 'favorite-toggle',
      product_id: productId,
    }),
  })
    .then((r) => r.json())
    .then((data) => {
      const count = data?.data?.count || 0;

      // ---- Обновляем mainbar иконку ----
      const mainbarFav = document.querySelector(
        '.mainbar-icons .favorite-icon'
      );
      if (mainbarFav) {
        if (count > 0 && !mainbarFav.classList.contains('active')) {
          mainbarFav.classList.add('active');
        } else if (count < 1 && mainbarFav.classList.contains('active')) {
          mainbarFav.classList.remove('active');
        }
      }

      const favoritePageInput = document.querySelector(
        'input[name="favorite-page"][value="1"]'
      );
      if (favoritePageInput) {
        // Проверяем, остались ли активные избранные товары
        const activeFavorites = document.querySelectorAll(
          '.favorite-icon--product.active'
        );
        if (activeFavorites.length === 0) {
          location.reload();
        }
      }
    });
}
