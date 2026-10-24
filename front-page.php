<?php
if (!defined('ABSPATH')) exit;
get_header();
?>

<?php
require_once __DIR__ . "/../../plugins/VG_Catalog/vendor/autoload.php";

use VG_Catalog\Helpers\CategoryHelper;
use VG_Catalog\Domain\Support\Dto\PaginationDTO;
use VG_Catalog\Domain\Services\CartService;

$categoryList = $_SESSION['category_list'] ?? [];

$helper = new CategoryHelper();

$price_list_id = carbon_get_theme_option('price_list_google_url');
$price_list_url = $price_list_id ? wp_get_attachment_url($price_list_id) : '';

$paginationDTO = new PaginationDTO(['limit' => 0]);
$categoryId = null;
$limit = 6;
$random = true;
$excludeIds = [];
$hasSale = true;

$cartService  = new CartService();
$specProducts = $helper->getProductsByCategoryId($categoryId, true, $limit, $excludeIds, $random, $hasSale);

$cartData = $cartService->getList($paginationDTO);

$cartTotals = array_reduce($cartData, function ($acc, $row) {
	$qty   = (int)($row['quantity'] ?? 1);
	$price = (float)($row['product']['price'] ?? 0);
	$sale  = (float)($row['product']['sale_price'] ?? 0);
	$final = ($sale > 0 && $sale < $price) ? $sale : $price;

	$acc['qty']   += $qty;
	$acc['base']  += $price * $qty;
	$acc['final'] += $final * $qty;
	return $acc;
}, ['qty' => 0, 'base' => 0.0, 'final' => 0.0]);

$cartTotals['discount'] = max(0, $cartTotals['base'] - $cartTotals['final']);

$normalizedCart = [
	'id'      => 'router',
	'version' => 0,
	'items'   => array_map(function ($row) {
		$qty   = (int)($row['quantity'] ?? 1);
		$p     = (float)($row['product']['price'] ?? 0);
		$s     = (float)($row['product']['sale_price'] ?? 0);
		$final = ($s > 0 && $s < $p) ? $s : $p;

		$unit = $row['product']['unit']->name ?? 'м.п';
		$price = $row['product']['price'] ?? 0;
		$sale_price = $row['product']['sale_price'] ?? 0;

		return [
			'id'        => (string)($row['id'] ?? ''),
			'productId' => (string)($row['product_id'] ?? 0),
			'name'      => (string)($row['product']['title'] ?? ''),
			'sku'       => (string)($row['product']['sku'] ?? ''),
			'quantity'  => $qty,
			'isHit'     => !empty($row['product']['is_hit']) && $row['product']['is_hit'] === '1',
			'hasSale'   => $s > 0 && $s < $p,
			'unit'      => '/ ' . $unit,

			// да, у тебя тут было два раза 'price' (один финал, второй оригинал).
			// оставляю как есть логически: price = оригинал, salePrice = скидка, priceFormatted = финал
			'price'     => $price,
			'salePrice' => $sale_price,

			'priceFormatted' => number_format($final, 0, '', ' ') . ' ₽',
			'originalPriceFormatted' => number_format($p, 0, '', ' ') . ' ₽'
		];
	}, $cartData),
	'totals'  => [
		'items_qty' => $cartTotals['qty'],
		'sum_without_discount' => $cartTotals['base'],
		'sum_with_discount'    => $cartTotals['final'],
		'discount'             => $cartTotals['discount'],
		'sum_without_discount_formatted' => number_format($cartTotals['base'], 0, '', ' ') . ' ₽',
		'sum_with_discount_formatted'    => number_format($cartTotals['final'], 0, '', ' ') . ' ₽',
		'discount_formatted'             => number_format($cartTotals['discount'], 0, '', ' ') . ' ₽',
	],
];

$normalizedCart = metal_get_normalized_cart();
// keep original normalizedCart
?>

<?php
$intro_slides = carbon_get_theme_option('intro_slides') ?: [];
?>

<?php if (!empty($intro_slides)): ?>
	<section class="intro">
		<div class="intro__slider swiper">
			<div class="intro__wrapper swiper-wrapper">

				<?php foreach ($intro_slides as $slide): ?>
					<?php
					$bgId  = (int)($slide['bg'] ?? 0);
					$bgUrl = $bgId ? wp_get_attachment_image_url($bgId, 'full') : '';

					$title = trim((string)($slide['title'] ?? ''));
					$text  = trim((string)($slide['text'] ?? ''));

					$alt = $bgId ? (string)get_post_meta($bgId, '_wp_attachment_image_alt', true) : '';
					$alt = trim($alt);
					if ($alt === '') $alt = $title;

					$hasBadge    = !empty($slide['has_badge']);
					$badgeText   = trim((string)($slide['badge_text'] ?? ''));
					$badgeIconId = (int)($slide['badge_icon_png'] ?? 0);
					?>

					<div class="intro__slide swiper-slide">
						<div class="intro__bg">
							<?php if ($bgUrl): ?>
								<img src="<?= esc_url($bgUrl) ?>" alt="<?= esc_attr($alt) ?>">
							<?php endif; ?>
						</div>

						<div class="intro__billet"></div>

						<div class="intro__panel panel-intro">
							<div class="container">
								<div class="panel-intro__row">

									<div class="panel-intro__col panel-intro__col--left">
										<div class="panel-intro__title">
											<?php if ($title !== ''): ?>
												<h2 class="h1"><?= esc_html($title) ?></h2>
											<?php endif; ?>
										</div>

										<?php get_template_part('template-parts/buttons/text-icon-button', null, ['href' => '/katalog']); ?>
									</div>

									<div class="panel-intro__col panel-intro__col--right">
										<?php if ($text !== ''): ?>
											<p class="panel-intro__text"><?= esc_html($text) ?></p>
										<?php endif; ?>

										<?php if ($hasBadge && ($badgeText !== '' || $badgeIconId)): ?>
											<div class="panel-intro__badge">
												<?php if ($badgeIconId): ?>
													<span class="panel-intro__badge-icon">
														<?= wp_get_attachment_image(
															$badgeIconId,
															'full',
															false,
															[
																'class' => 'panel-intro__badge-icon-img',
																'alt'   => $badgeText !== '' ? $badgeText : '',
															]
														) ?>
													</span>
												<?php endif; ?>

												<?php if ($badgeText !== ''): ?>
													<span class="panel-intro__badge-text"><?= esc_html($badgeText) ?></span>
												<?php endif; ?>
											</div>
										<?php endif; ?>
									</div>

								</div>
							</div>
						</div>

					</div>
				<?php endforeach; ?>

			</div>

			<?php if (count($intro_slides) > 1): ?>
				<div class="controls-intro__pagination slider-pagination">
					<?php foreach ($intro_slides as $i => $_): ?>
						<div class="slider-pagination__item <?= $i === 0 ? 'active' : '' ?>">
							<span class="slider-pagination__center"></span>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<div class="controls-intro__navigation">
				<?php get_template_part('template-parts/buttons/slider-button', null, array('direction' => 'prev', 'disabled' => true)); ?>
				<?php get_template_part('template-parts/buttons/slider-button', null, array('direction' => 'next')); ?>
			</div>

		</div>
	</section>
<?php endif; ?>


<section class="catalog-grid" aria-label="Категории каталога">
	<div class="container">
		<div class="catalog-grid__top">
			<h2 class="h2">Каталог товаров</h2>
			<?php get_template_part('template-parts/buttons/text-icon-button', null, ['href' => '/katalog/', 'class' => 'text-icon-button--red']); ?>
		</div>

		<ul class="catalog-grid__list">
			<?php foreach ($categoryList as $cat): ?>
				<?php get_template_part('template-parts/catalog-card', null, ['cat' => $cat]); ?>
			<?php endforeach; ?>
		</ul>
	</div>
</section>

<?php metal_print_cart_state(); ?>

<section class="banner">
	<div class="container">
		<div class="banner__inner">
			<div aria-hidden="true" class="banner__image-bg">
				<img src="<?php echo get_template_directory_uri(); ?>/assets/img/banners/lines.svg" alt="Линии">
			</div>

			<div class="banner__announcement">
				<h2 class="h3">Уважаемые покупатели</h2>
				<p>Наш сайт работает в тестовом режиме и находится на стадии наполнения товарами, все цены и вопросы просьба узнавать по нашему номеру</p>
				<a href="tel:<?php echo esc_attr(clean_phone(my_opt('phone_1'))); ?>" class="contacts__link contacts__link--phone h3"><?php echo the_opt('phone_1'); ?></a>
			</div>

			<div class="banner__right">
				<div class="banner__work-time">
					<p>Скачивайте актуальный прайс</p>
					<a href="<?php echo esc_url($price_list_url); ?>" class="form-feedback__btn btn btn--fill">Скачать прайс-лист</a>
				</div>

				<div class="banner__logo">
					<a class="logo" href="<?php echo esc_url(home_url('/')); ?>" aria-label="На главную">
						<img class="logo__img" src="<?php echo esc_url(wp_get_attachment_image_url(carbon_get_theme_option('site_logo_header'), 'full')); ?>" alt="<?php echo esc_attr(get_bloginfo('name')); ?>">
					</a>
				</div>
			</div>
		</div>
	</div>
</section>

<?php ob_start(); ?>
<?php if (!$specProducts->isEmpty()): ?>
	<section data-wp-interactive="cart" class="promotions" aria-label="Категории каталога">
		<div class="container">
			<div class="catalog-grid__top">
				<h2 class="h2">Спецпредложения</h2>
				<?php get_template_part('template-parts/buttons/text-icon-button', null, ['href' => '/specoffers/', 'label' => 'Все спецпредложения', 'class' => 'text-icon-button--red']); ?>
			</div>

			<div class="slider">
				<div class="swiper">
					<div class="swiper-wrapper">
						<?php foreach ($specProducts as $product): ?>
							<div class="swiper-slide"><?php get_template_part('template-parts/product-card', null, $product); ?></div>
						<?php endforeach; ?>
					</div>
				</div>

				<button class="slider-button slider-button--prev">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="22" viewBox="0 0 24 22" fill="none">
						<path d="M10.0721 21.0928C10.2674 21.2881 10.584 21.2881 10.7792 21.0928C10.9745 20.8976 10.9745 20.581 10.7792 20.3857L10.0721 21.0928ZM0.686097 11L0.332543 10.6464C0.238774 10.7402 0.186096 10.8674 0.186097 11C0.186098 11.1326 0.238778 11.2598 0.332548 11.3536L0.686097 11ZM10.6406 1.75265C10.8358 1.55739 10.8358 1.2408 10.6406 1.04554C10.4453 0.850279 10.1287 0.850279 9.93344 1.04554L10.6406 1.75265ZM23.3135 11.5L23.8135 11.5L23.8135 10.5L23.3135 10.5L23.3135 11.5ZM10.4257 20.7393L10.7792 20.3857L1.03964 10.6464L0.686097 11L0.332548 11.3536L10.0721 21.0928L10.4257 20.7393ZM0.686097 11L1.03965 11.3536L10.6406 1.75265L10.287 1.39909L9.93344 1.04554L0.332543 10.6464L0.686097 11ZM0.686097 11L0.686097 11.5L23.3135 11.5L23.3135 11L23.3135 10.5L0.686097 10.5L0.686097 11Z" fill="#909092" />
					</svg>
				</button>

				<button class="slider-button slider-button--next">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="22" viewBox="0 0 24 22" fill="none">
						<path d="M13.9277 0.907158C13.7324 0.711899 13.4159 0.711903 13.2206 0.907168C13.0253 1.10243 13.0253 1.41902 13.2206 1.61428L13.9277 0.907158ZM23.3137 11L23.6673 11.3536C23.761 11.2598 23.8137 11.1326 23.8137 11C23.8137 10.8674 23.761 10.7402 23.6673 10.6464L23.3137 11ZM13.3593 20.2474C13.164 20.4426 13.164 20.7592 13.3593 20.9545C13.5545 21.1497 13.8711 21.1497 14.0664 20.9545L13.3593 20.2474ZM0.686305 10.5L0.186305 10.5V11.5L0.686305 11.5V10.5ZM13.5742 1.26072L13.2206 1.61428L22.9602 11.3536L23.3137 11L23.6673 10.6464L13.9277 0.907158L13.5742 1.26072ZM23.3137 11L22.9602 10.6464L13.3593 20.2474L13.7128 20.6009L14.0664 20.9545L23.6673 11.3536L23.3137 11ZM23.3137 11L23.3137 10.5L0.686305 10.5L0.686305 11L0.686305 11.5L23.3137 11.5L23.3137 11Z" fill="#FF5154" />
					</svg>
				</button>
			</div>

			<div class="swiper-pagination"></div>
		</div>
	</section>
<?php endif; ?>
<?php echo wp_interactivity_process_directives(ob_get_clean()); ?>

<section class="about">
	<div class="pattern"></div>
	<div class="container">
		<div class="about__bg" aria-hidden="true"></div>
		<div class="about__blur"></div>

		<div class="about__top top-about">
			<div class="top-about__col top-about__col--left">
				<h2 class="about__title h2">
					металлобаза Ивтент — комплексный поставщик металлопроката, работаем на рынке с 2001 года
				</h2>
			</div>

			<div class="top-about__col top-about__col--right">
				<div class="top-about__content">
					<div class="top-about__text">
						<p>Компания Металлобаза ивтент реализует металлопрокат в городе Иваново для нужд промышленных и строительных организаций.</p>
						<p>Cвязаны долгосрочными контрактами с ведущими российскими предприятиями, изготавливающими металлопродукцию, что позволяет нашим заказчикам купить металлопрокат оптом и в розницу по действительно низким ценам.</p>
					</div>

					<div class="top-about__links">
						<div class="top-about__link">оптом</div>
						<div class="top-about__link">в розницу</div>
					</div>

					<div class="top-about__link-button">
						<?php get_template_part(
							'template-parts/buttons/text-icon-button',
							null,
							['class' => 'text-icon-button--red', 'label' => 'Подробнее о компании', 'href' => 'about']
						); ?>
					</div>
				</div>
			</div>
		</div>

		<div class="about__features features-cards features-grid">
			<article class="features-cards__item">
				<div class="features-cards__position">01</div>
				<div class="features-cards__title">Оперативная отгрузка без очередей</div>
				<p class="features-cards__text">Наличие широкого ассортимента, комплектация заявок под заказ. Простая покупка.</p>
			</article>

			<article class="features-cards__item">
				<div class="features-cards__position">02</div>
				<div class="features-cards__title">Доставка товара в день заказа</div>
				<p class="features-cards__text">Встретим, поможем, подскажем. Профессиональная консультация от опытных менеджеров. Мы ценим каждого покупателя!</p>
			</article>

			<article class="features-cards__item">
				<div class="features-cards__position">03</div>
				<div class="features-cards__title">Отгрузка металла в розницу</div>
				<p class="features-cards__text">Минимальная партия отгружаемого товара — всего 3 метра, возможно бесплатное хранение на базе по согласованию, погрузка и помощь с доставкой в день заказа.</p>
			</article>
		</div>
	</div>
</section>

<section class="partners">
	<div class="container">
		<div class="partners__top top">
			<h2 class="h2">Партнеры</h2>
			<p>Наши партнёры — это гарантия надёжности и стабильных поставок для ваших строительных и промышленных проектов.</p>
		</div>

		<?php
		$partnerNames = [
			'Северсталь',
			'НЛМК',
			'НТПЗ',
			'КТЗ',
			'ОМК'
		];
		?>

		<div class="partners__grid">
			<?php foreach ($partnerNames as $i => $name): ?>
				<?php get_template_part('template-parts/partners-card', null, [
					'name' => $name,
					'index' => $i + 1
				]); ?>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<section class="news">
	<div class="container">
		<div class="news-top top">
			<h3 class="h2">Новости</h3>
			<a href="/news" class="text-icon-button text-icon-button--red">
				<span class="btn-icon"><?php the_icon('arrow-left-up'); ?></span>
				<span class="text">Все новости</span>
			</a>
		</div>

		<div class="news__wrapper">
			<?php get_template_part('template-parts/news-row'); ?>
		</div>
	</div>
</section>

<?php get_template_part('template-parts/reviews'); ?>
<?php get_template_part('template-parts/feedback'); ?>
<?php get_template_part('template-parts/contacts'); ?>

</div>

<?php get_footer(); ?>