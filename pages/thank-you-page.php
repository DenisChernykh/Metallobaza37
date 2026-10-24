<?php

/**
 * Template Name: Спасибо за заказ
 */
get_header() ?>

<?php

require_once __DIR__ . "/../../../plugins/VG_Catalog/vendor/autoload.php";
use VG_Catalog\Domain\Services\CartService;

$cartService = new CartService();
$cartService->clearCart();

?>

    <div class="container thank-you">
        <div class="thank-you__row">
            <div class="thank-you__left">
                <h1 class="thank-you__title h1">Спасибо за заказ</h1>
                <div class="thank-you__text">Менеджер свяжется с вами в ближайшее время для уточнения деталей заказа.</div>
                <a href=" <?php echo home_url() ?>" class="btn btn--fill">Вернуться на главную</a>
            </div>
            <div class="thank-you__right">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/img/thank-you/1.png" alt="Металлоконструкции">
                <div class="pattern"></div>
            </div>
        </div>
    </div>

<?php get_footer() ?>