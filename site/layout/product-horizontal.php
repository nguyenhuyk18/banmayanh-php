<?php
$slug = $slugify->slugify($product->getName());
$productUrl = $router->generate('productDetail', ['slug' => $slug, 'id' => $product->getId()]);
?>
<article class="related-product-card">
    <a class="related-product-image" href="<?= $productUrl ?>">
        <img loading="lazy" src="<?= get_base_path() ?>/upload/<?= h($product->getFeaturedImage()) ?>" alt="<?= h($product->getName()) ?>">
    </a>
    <div class="related-product-body">
        <span class="related-product-status"><?= $product->getInventoryQty() > 0 ? 'Còn hàng' : 'Tạm hết hàng' ?></span>
        <h5><a href="<?= $productUrl ?>"><?= h($product->getName()) ?></a></h5>
        <div class="related-product-price"><?= formatMoney($product->getSalePrice()) ?>₫</div>
        <a class="related-product-link" href="<?= $productUrl ?>">Xem chi tiết <i class="fas fa-arrow-right"></i></a>
    </div>
</article>
