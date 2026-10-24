<form role="search" method="get" class="header-search" id="searchform" action="<?php echo esc_url(home_url('/')); ?>">
	<div class="search__wrapper">
		<button type="submit">
			<?php the_icon('search') ?>
		</button>

		<input class="search__input js-search-input" type="text" value="<?php echo get_search_query() ?>" name="s" id="s" placeholder="Поиск…" />
		<button class="search__clear js-search-clear" type="button" aria-label="Очистить">
			<?php the_icon('cross') ?>
		</button>
	</div>
</form>