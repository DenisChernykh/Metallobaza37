export function initFilter() {
    initKatalogFilter();
}

function initKatalogFilter() {
    const filterForm = document.querySelector('[data-filter-form]');

    if (!filterForm) return;

    filterForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const collectedData = collectFilterData(filterForm);
        submitFilter(collectedData);
    });

    filterForm.querySelectorAll('input').forEach(input => {
        input.addEventListener('change', () => {
            const collectedData = collectFilterData(filterForm);
            updateProductCount(collectedData);
        });
    });

    function debounce(func, wait = 300) {
        let timeout;
        return function(...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), wait);
        };
    }

    const sliderElem = document.getElementById('price-range');
    if (sliderElem && sliderElem.noUiSlider) {
        const debouncedUpdate = debounce(() => {
            const filterForm = document.querySelector('[data-filter-form]');
            if (!filterForm) return;

            const collectedData = collectFilterData(filterForm);
            updateProductCount(collectedData);
        }, 400);

        sliderElem.noUiSlider.on('update', function(values) {
            const filterForm = document.querySelector('[data-filter-form]');
            if (!filterForm) return;

            const priceMinInput = filterForm.querySelector('input[name="price_min"]');
            const priceMaxInput = filterForm.querySelector('input[name="price_max"]');
            if (priceMinInput) priceMinInput.value = Math.round(values[0]);
            if (priceMaxInput) priceMaxInput.value = Math.round(values[1]);

            debouncedUpdate();
        });
    }

    const resetBtn = document.querySelector('[data-reset-btn]');
    resetBtn?.addEventListener('click', function(e) {
        e.preventDefault();

        filterForm.reset();
        const url = new URL(window.location.href);

        const searchParams = new URLSearchParams();
        if (url.searchParams.has('s')) {
            searchParams.set('s', url.searchParams.get('s'));
        }

        const baseUrl = url.origin + url.pathname;
        const newUrl = searchParams.toString() ? `${baseUrl}?${searchParams.toString()}` : baseUrl;

        window.history.pushState({}, '', newUrl);
        location.reload();
    });
}

function collectFilterData(form) {
    const formData = new FormData(form);
    const collectedData = {};

    for (let [name, value] of formData.entries()) {
        if (name.endsWith('[]')) {
            const baseName = name.slice(0, -2);
            if (!collectedData[baseName]) collectedData[baseName] = [];
            collectedData[baseName].push(value);
        } else {
            collectedData[name] = value;
        }
    }

    return collectedData;
}

function submitFilter(collectedData) {
    const url = new URL(window.location.href);

    const allowedParams = ['sort', 'sort_id', 'order', 's'];

    const newParams = new URLSearchParams();

    url.searchParams.forEach((value, key) => {
        if (allowedParams.includes(key)) {
            newParams.set(key, value);
        }
    });

    addFilterToParams(newParams, collectedData);

    const finalUrl = `${url.origin}${url.pathname}${newParams.toString() ? `?${newParams.toString()}` : ''}`;
    window.location.href = finalUrl;
}

function addFilterToParams(params, collectedData) {
    const defaultMinPrice = '<?=$filterData["price_range"]["min"]?>';
    const defaultMaxPrice = '<?=$filterData["price_range"]["max"]?>';

    if (collectedData.price_min && collectedData.price_min !== defaultMinPrice) {
        params.set('price_min', collectedData.price_min);
    }
    if (collectedData.price_max && collectedData.price_max !== defaultMaxPrice) {
        params.set('price_max', collectedData.price_max);
    }

    Object.entries(collectedData).forEach(([key, value]) => {
        if (key.startsWith('prop[') && Array.isArray(value)) {
            const propId = key.match(/prop\[(\d+)\]/)?.[1];
            if (propId) {
                value.forEach(val => {
                    if (val && val.trim() !== '') {
                        params.append(`prop[${propId}][]`, val);
                    }
                });
            }
        }
    });
}

function updateProductCount(collectedData) {
    const categoryInput = document.querySelector('input[name="current_category_id"]');
    const categoryId = categoryInput ? categoryInput.value : null;

    const postData = {
        route: 'category-products-count',
        category_id: categoryId,
        price_min: collectedData.price_min || null,
        price_max: collectedData.price_max || null,
        prop: {}
    };

    const specOffersInput = document.querySelector('input[name="page_specoffers"]');
    if (specOffersInput) {
        postData.has_sale = true;
    }

    const searchInput = document.querySelector('input[name="page_search"]');
    if (searchInput) {
        postData.search = searchInput.value;
    }

    Object.entries(collectedData).forEach(([key, value]) => {
        if (key.startsWith('prop[') && Array.isArray(value)) {
            const propId = key.match(/prop\[(\d+)\]/)?.[1];
            if (propId) {
                postData.prop[propId] = value.filter(val => val && val.trim() !== '');
            }
        }
    });

    fetch(`/wp-content/plugins/VG_Catalog/router.php`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(postData)
    })
        .then(res => res.json())
        .then(data => {
            if (data.success && typeof data.data.count !== 'undefined') {
                const count = data.data.count;

                let word = 'товаров';
                if (count % 10 === 1 && count % 100 !== 11) word = 'товар';
                else if ([2,3,4].includes(count % 10) && ![12,13,14].includes(count % 100)) word = 'товара';

                const btn = document.querySelector('[data-products-count]');
                if (btn) btn.textContent = `Показать ${count} ${word}`;
            }
        })
        .catch(err => console.error('Ошибка при получении кол-ва товаров:', err));
}
