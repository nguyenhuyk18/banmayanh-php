<?php 
    require ABSPATH_SITE . 'layout/header.php';
?>
<main id="maincontent" class="page-main">
    <div class="container">
        <div class="row">
            <div class="col-xs-9">
                <ol class="breadcrumb">
                    <li><a href="<?= get_base_path() ?>/" target="_self">Trang chủ</a></li>
                    <li><span>/</span></li>
                    <li class="active"><span><?= h($product->getCategory()->getName()) ?></span></li>
                </ol>
            </div>
            <div class="col-xs-3 hidden-lg hidden-md">
                <a class="hidden-lg pull-right btn-aside-mobile" href="javascript:void(0)">Bộ lọc <i
                        class="fa fa-angle-double-right"></i></a>
            </div>
            <div class="clearfix"></div>
            <?php require ABSPATH_SITE . 'layout/sidebar.php' ?>

            <div class="col-md-9 product-detail">
                <div class="row product-info">
                    <div class="col-md-6">
                        <img data-zoom-image="<?= get_base_path() ?>/upload/<?= $product->getFeaturedImage() ?>"
                            class="img-responsive thumbnail main-image-thumbnail"
                            src="<?= get_base_path() ?>/upload/<?= $product->getFeaturedImage() ?>" alt="">
                        <div class="product-detail-carousel-slider">
                            <div class="owl-carousel owl-theme">
                                <div class="item thumbnail"><img src="<?= get_base_path() ?>/upload/<?= $product->getFeaturedImage() ?>"
                                        alt="">
                                </div>
                                <?php foreach($product->getImageItems() as $imageItem): ?>
                                <div class="item thumbnail"><img src="<?= get_base_path() ?>/upload/<?= h($imageItem->getName()) ?>" alt="">
                                </div>
                                <?php endforeach; ?>

                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h5 class="product-name"><?= h($product->getName()) ?></h5>
                        <div class="brand">

                            <span>Nhãn hàng: </span> <span><?= h($product->getBrand()->getName()) ?></span>
                        </div>
                        <div class="product-status">
                            <span>Trạng thái: </span>
                            <?php 
                                if($product->getInventoryQty() > 0):
                            ?>
                            <span class="label-success">Còn hàng</span>
                            <?php endif; ?>
                            <?php if($product->getInventoryQty() == 0): ?>
                            <span class="label-warning">Hết hàng</span>
                            <?php endif; ?>
                        </div>
                        <div class="product-item-price">
                            <span>Giá: </span>
                            <?php 
                                if($product->getPrice() != $product->getSalePrice()) :
                            ?>
                            <span class="product-item-regular"><?= formatMoney($product->getPrice()) ?>₫</span>
                            <?php endif; ?>
                            <span class="product-item-discount"><?= formatMoney($product->getSalePrice()) ?>₫</span>
                        </div>
                        <?php if($product->getInventoryQty() > 0): ?>
						<form method="post" action="<?= app_url('index.php?c=cart&a=add') ?>" class="input-group">
							<input type="hidden" name="_csrf" value="<?= h(csrf_token()) ?>">
							<input type="hidden" name="product_id" value="<?= $product->getId() ?>">
							<input type="number" name="qty" class="product-quantity form-control" value="1" min="1" max="<?= $product->getInventoryQty() ?>" required>
							<button type="submit" class="btn btn-success cart-add-button"><i class="fa fa-shopping-cart"></i> Thêm vào giỏ hàng</button>
						</form>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="row product-description">
                    <div class="col-xs-12">
                        <div role="tabpanel">
                            <!-- Nav tabs -->
                            <ul class="nav nav-tabs" role="tablist">
                                <li role="presentation" class="active">
                                    <a href="#product-description" aria-controls="home" role="tab" data-toggle="tab">Mô
                                        tả</a>
                                </li>
                                <li role="presentation">
                                    <a href="#product-comment" aria-controls="tab" role="tab" data-toggle="tab">Đánh
                                        giá</a>
                                </li>
                            </ul>
                            <!-- Tab panes -->
                            <div class="tab-content">
                                <div role="tabpanel" class="tab-pane active" id="product-description">
                                    <?= $product->getDescription() ?>

                                </div>
                                <div role="tabpanel" class="tab-pane" id="product-comment">
                                    <form class="form-comment" action="" method="POST" role="form">
                                        <label>Đánh giá của bạn</label>
                                        <div class="form-group">
                                            <input type="hidden" name="product_id" value="<?= $product->getId() ?>">
                                            <input class="rating-input" name="rating" type="text" title="" value="4" />
                                            <input type="text" class="form-control" id="" name="fullname"
                                                placeholder="Tên *" required>
                                            <input type="email" name="email" class="form-control" id=""
                                                placeholder="Email *" required>
                                            <textarea name="description" id="input" class="form-control" rows="3"
                                                required placeholder="Nội dung *"></textarea>
                                        </div>
                                        <div class="form-group">
                                            <div class="message alert alert-success" style="display: none">

                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Gửi</button>
                                    </form>
                                    <div class="comment-list">
                                        <?php require ABSPATH_SITE . 'view/product/comments.php' ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row product-related equal">
                    <div class="col-md-12">
                        <h4 class="text-center">Sản phẩm liên quan</h4>
                        <div class="owl-carousel owl-theme">
                            <?php foreach($relatedProducts as $product) :?>
                            <div class="item thumbnail">
                                <?php require ABSPATH_SITE . 'layout/product-horizontal.php' ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?php require ABSPATH_SITE . 'layout/footer.php'; ?>
