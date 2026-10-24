<?php
/** @var string $name */
/** @var string $items */
/** @var string $from */
/** @var array  $products */
?>

<?= !$name ?: sprintf('<b>Форма</b>: %s<br><br>', $name) ?>

<?= $items ?? '' ?>

<?php if (!empty($products['items'])): ?>
    <br>
    <h3>Список товаров</h3>

    <table width="100%" cellpadding="6" cellspacing="0" border="1" style="border-collapse: collapse;">
        <thead>
        <tr>
            <th align="left">Товар</th>
            <th align="center">Кол-во</th>
            <th align="right">Цена</th>
            <th align="right">Сумма</th>
        </tr>
        </thead>
        <tbody>

        <?php
        $total = 0;
        foreach ($products['items'] as $product):
            $price = $product['salePrice'] > 0
                ? $product['salePrice']
                : $product['price'];

            $sum = $price * $product['quantity'];
            $rezExtra = 0;

            // проверяем, нужно ли нарезать
            if (!empty($product['rez']) && (int)$product['rez'] === 1) {
                $rezPrice = $product['priceCut'] ?? 5; // цена реза по умолчанию 5
                $rezExtra = $rezPrice * $product['quantity'];
            }

            $total += $sum;

            ?>
            <tr>
                <td>
                    <strong><?= htmlspecialchars($product['name']) ?></strong><br>
                    <?php if (!empty($product['sku'])): ?>
                        <small>Артикул: <?= htmlspecialchars($product['sku']) ?></small><br>
                    <?php endif; ?>
                    <?php if (!empty($product['href'])): ?>
                        <a href="<?= $product['href'] ?>">Открыть товар</a>
                    <?php endif; ?>
                    <?php if (!empty($product['rez']) && (int)$product['rez'] === 1): ?>
                        <small>Требуется нарезка (+<?= number_format($rezPrice, 2, '.', ' ') ?> ₽)</small>
                    <?php endif; ?>
                </td>
                <td align="center"><?= $product['quantity'] ?></td>
                <td align="right"><?= number_format($price, 2, '.', ' ') ?> ₽</td>
                <td align="right"><?= number_format($sum, 2, '.', ' ') ?> ₽</td>
            </tr>
        <?php endforeach; ?>

        </tbody>
        <tfoot>
        <tr>
            <td colspan="3" align="right"><strong>Итого:</strong></td>
            <td align="right"><strong><?= number_format($total, 2, '.', ' ') ?> ₽</strong></td>
        </tr>
        </tfoot>
    </table>
<?php endif; ?>

<br><br>
<?= !$from ?: sprintf('Форма отправлена со страницы: %s', $from) ?>
