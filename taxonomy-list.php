<?php

/**
 * Template Name: Каталог
 */

get_header() ?>

<?php
require_once __DIR__ . "/../../plugins/VG_Catalog/vendor/autoload.php";

use VG_Catalog\Domain\Services\CategoryService;
use VG_Catalog\Domain\UseCases\Categories\Actions\GetListCategoriesAction;
use VG_Catalog\Domain\UseCases\Categories\Dto\FilterCategoriesDTO;

$categoryService = new CategoryService();

// --- Получение подкатегорий ---
$categoryList = $categoryService->getList(
    new FilterCategoriesDTO(['parent_id' => 0]),
    new GetListCategoriesAction()
);

?>

<section class="catalog-grid" aria-label="Категории каталога">
    <div class="container">
        <div class="catalog-grid__top">

            <?php
            if (function_exists('yoast_breadcrumb')) {
                yoast_breadcrumb('<nav class="breadcrumbs">', '</nav>');
            }
            ?>
            <h2 class="h2">Каталог товаров</h2>


        </div>
        <ul class="catalog-grid__list">
            <?php foreach($categoryList as $cat):?>
                <?php get_template_part('template-parts/catalog-card', null, ['cat' => $cat]) ?>
            <?php endforeach;?>
        </ul>
    </div>
</section>
<?php get_template_part('template-parts/feedback') ?>
<?php get_template_part('template-parts/contacts') ?>
<?php get_footer()  ?>
