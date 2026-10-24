<?php
$cat = $args['cat'];
?>

<li class="catalog-grid__item">
	<a class="catalog-tile" href="<?=$cat['link']?>" >
		<span class="catalog-tile__thumb">
			<img src="<?=$cat['image']?>" alt="<?=$cat['name']?>" loading="lazy" />
		</span>
		<span class="catalog-tile__body">
			<span class="catalog-tile__title"><?=$cat['name']?></span>
		</span>
	</a>
</li>