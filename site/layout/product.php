<?php
$slug = $slugify->slugify($product->getName());
$productUrl = $router->generate('productDetail', ['slug' => $slug, 'id' => $product->getId()]);
?>
<article class="product-container">
    <a class="image" href="<?= $productUrl ?>" aria-label="Xem <?= h($product->getName()) ?>">
        <?php if ($product->getDiscountPercentage() > 0): ?>
        <span class="product-badge">-<?= (int) $product->getDiscountPercentage() ?>%</span>
        <?php elseif ($product->getFeatured()): ?>
        <span class="product-badge product-badge-featured">Nổi bật</span>
        <?php endif; ?>
        <img class="img-responsive" loading="lazy" src="<?= get_base_path() ?>/upload/<?= h($product->getFeaturedImage()) ?>"
            alt="<?= h($product->getName()) ?>">
    </a>
    <div class="product-rating" aria-label="Đánh giá sản phẩm">
        <span><i class="fas fa-star"></i> <?= number_format((float) ($product->getStar() ?: 5), 1) ?></span>
        <small><?= $product->getInventoryQty() > 0 ? 'Còn hàng' : 'Tạm hết hàng' ?></small>
    </div>
    <div class="product-meta">
        <h5 class="name">
            <a class="product-name" href="<?= $productUrl ?>"
                title="<?= h($product->getName()) ?>"><?= h($product->getName()) ?></a>
        </h5>
        <div class="product-item-price">
            <?php if ($product->getPrice() != $product->getSalePrice()): ?>
            <span class="product-item-regular"><?= formatMoney($product->getPrice()) ?>₫</span>
            <?php endif; ?>
            <span class="product-item-discount"><?= formatMoney($product->getSalePrice()) ?>₫</span>
        </div>
    </div>
    <div class="button-product-action clearfix">
        <?php if($product->getInventoryQty() > 0): ?>
        <div class="cart icon">
			<form method="post" action="<?= app_url('index.php?c=cart&a=add') ?>" style="display:inline">
				<input type="hidden" name="_csrf" value="<?= h(csrf_token()) ?>">
				<input type="hidden" name="product_id" value="<?= $product->getId() ?>">
				<input type="hidden" name="qty" value="1">
				<button type="submit" class="btn btn-outline-inverse" title="Thêm vào giỏ">
                Thêm vào giỏ <i class="fa fa-shopping-cart"></i>
				</button>
			</form>
        </div>
        <?php endif; ?>
        <div class="quickview icon">
            <a class="btn btn-outline-inverse"
                href="<?= $productUrl ?>"
                title="Xem nhanh">
                Xem chi tiết <i class="fa fa-eye"></i>
            </a>
        </div>
    </div>
</article>
