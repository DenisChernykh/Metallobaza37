export function initCheckProductFavorite() {
    initCheckFav();
}

function initCheckFav() {
    fetch('/wp-content/plugins/VG_Catalog/router.php?route=favorite-get-list&limit=0', {
        method: 'GET',
    })
        .then(r => r.json())
        .then(data => {
            updateCartUIFav(data.data);
            updateMainbarFavIcon(data.data);
        })
        .catch(err => console.error('Fetch error:', err));
}

function updateCartUIFav(favData) {
    if (!favData?.favorites) return;

    favData.favorites.forEach(item => {
        const postId = item.post_id;

        const productEl = document.querySelectorAll(`.favorite-icon--product[data-product-id="${postId}"]`);

        productEl.forEach(productEl => {
            productEl.classList.add('active');
        });
    });
}

function updateMainbarFavIcon(favData) {
    if (!favData?.favorites?.length) return;

    const mainbarFav = document.querySelector('.mainbar-icons .favorite-icon');
    if (mainbarFav && !mainbarFav.classList.contains('active')) {
        mainbarFav.classList.add('active');
    }
}