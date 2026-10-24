export function initPagination() {
    initPaginationRedirect();
}

function initPaginationRedirect(){
    const isFavoritePage = document.querySelector('input[name="favorite-page"][value="1"]') !== null;
    const paramName = isFavoritePage ? 'pagen' : 'page';

    document.querySelectorAll('[data-pagination-btn]').forEach(btn => {
        btn?.addEventListener('click', event => {
            event.preventDefault();

            const page = btn.getAttribute('data-page');
            if (!page) return;

            const url = new URL(window.location.href);

            url.searchParams.set(paramName, page);
            window.location.href = url.toString();
        });
    });
}