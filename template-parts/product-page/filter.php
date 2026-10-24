<?php
$filterData = $args;

$currentPriceRange = $filterData['current_price_range'] ?? [];
$priceRange = $filterData['price_range'];
$productsProperties = $filterData['properties'] ?? [];

if(empty($currentPriceRange)){
    $currentPriceRange['min'] = $priceRange['min'];
    $currentPriceRange['max'] = $priceRange['max'];
}

$currentProperties = $_GET['prop'] ?? [];

?>

<?php if(!empty($filterData)):?>

<aside class="filter">
	<div class="filter__grabber"></div>
	<form data-filter-form class="filter__form" method="get" action="">
		<div class="filter__item">
			<div class="filter__top">
				<div class="filter__title">Цена, ₽</div>
				<div class="filter__icon">
					<svg width="13" height="8" viewBox="0 0 13 8" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M1.20068 6.66732L6.53402 1.33398L11.8674 6.66732" stroke="#4F4D56" stroke-width="1.33333" stroke-linecap="round" stroke-linejoin="round" />
					</svg>
				</div>
			</div>

			<div class="filter__body">
				<div class="range" data-range
					data-min="<?=$priceRange['min']?>" data-max="<?=$priceRange['max']?>" data-step="100"
					data-start-min="<?=$currentPriceRange['min']?>" data-start-max="<?=$currentPriceRange['max']?>">
					<div class="range__inputs">


						<input type="text" inputmode="numeric" id="price-min" name="price_min" value="<?=$filterData['price_range']['min']?>">



						<input type="text" inputmode="numeric" id="price-max" name="price_max" value="<?=$filterData['price_range']['max']?>">

					</div>
					<div class="range__slider" id="price-range"></div>

				</div>
			</div>
		</div>

        <?php foreach($productsProperties as $index => $propData):?>
            <div class="filter__item">
                <div class="filter__top">
                    <div class="filter__title"><?=$propData['name']?></div>
                    <div class="filter__icon"><svg width="13" height="8" viewBox="0 0 13 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M1.20068 6.66732L6.53402 1.33398L11.8674 6.66732" stroke="#4F4D56" stroke-width="1.33333" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                </div>
                <div class="filter__body">
                    <?php foreach ($propData['values'] as $key => $var):
                        $isChecked = false;

                        if (!empty($currentProperties[$propData['id']])) {
                            $isChecked = in_array($var, (array)$currentProperties[$propData['id']], true);
                        }
                        ?>
                        <div class="filter__field">
                            <label for="prop_<?=$propData['id']?>_<?=$key?>" class="filter__wrapper">
                                <input
                                        id="prop_<?= $propData['id'] ?>_<?= $key ?>"
                                        type="checkbox"
                                        name="prop[<?= $propData['id'] ?>][]"
                                        value="<?= htmlspecialchars($var) ?>"
                                    <?= $isChecked ? 'checked' : '' ?>
                                >
                                <span class="filter__box"></span>
                                <?=htmlspecialchars($var)?>
                            </label>
                        </div>
                    <?php endforeach;?>
                </div>
            </div>
        <?php endforeach;?>
		<button data-products-count type="submit" class="btn btn--fill">Показать 30 вариантов</button>
		<a data-reset-btn href="" class="filter__btn"><span>Cбросить фильтры</span><svg width="14" height="15" viewBox="0 0 14 15" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M0.5 1L13.5 14M13.5 1L0.5 14" stroke="#909092" stroke-linecap="round" stroke-linejoin="round" />
			</svg>
		</a>
	</form>

</aside>

<?php endif;?>