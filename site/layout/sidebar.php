<aside class="col-md-3">
    <div class="inner-aside">
        <div class="category">
            <h5>Danh mục sản phẩm</h5>
            <ul>
                <li class="<?= empty($category_id) ? 'active' : '' ?>">
                    <a href="<?= $router->generate('product') ?>" title="Tất cả sản phẩm" target="_self">Tất cả sản phẩm
                    </a>
                </li>
                <?php 
                
                foreach ($categories as $category): 
                    $slug = $slugify->slugify($category->getName());
                    $categoryLink = $router->generate('category', ['slug' => $slug, 'id' => $category->getId()]);
                    
                    ?>
                <li class="<?= $category_id == $category->getId() ? 'active' : '' ?>">
                    <a href="<?= $categoryLink ?>" title="<?= h($category->getName()) ?>"
                        target="_self"><?= h($category->getName()) ?></a>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="price-range">
            <h5>Khoảng giá</h5>
            <ul>
                <li>
                    <label for="filter-less-100">
                        <input type="radio" id="filter-less-100" name="filter-price" value="0-5000000"
                            <?= !empty($priceRange) && $priceRange == '0-5000000' ? 'checked' : '' ?>>
                        <i class="fa"></i>
                        Giá dưới 5.000.000đ
                    </label>
                </li>
                <li>
                    <label for="filter-100-200">
                        <input type="radio" id="filter-100-200" name="filter-price" value="5000000-15000000"
                            <?=  !empty($priceRange) && $priceRange == '5000000-15000000' ? 'checked' : '' ?>>
                        <i class="fa"></i>
                        5.000.000đ - 15.000.000đ
                    </label>
                </li>
                <li>
                    <label for="filter-200-300">
                        <input type="radio" id="filter-200-300" name="filter-price" value="15000000-30000000"
                            <?=  !empty($priceRange) && $priceRange == '15000000-30000000' ? 'checked' : '' ?>>
                        <i class="fa"></i>
                        15.000.000đ - 30.000.000đ
                    </label>
                </li>
                <li>
                    <label for="filter-300-500">
                        <input type="radio" id="filter-300-500" name="filter-price" value="30000000-50000000"
                            <?= !empty($priceRange) && $priceRange == '30000000-50000000' ? 'checked' : '' ?>>
                        <i class="fa"></i>
                        30.000.000đ - 50.000.000đ
                    </label>
                </li>
                <li>
                    <label for="filter-500-1000">
                        <input type="radio" id="filter-500-1000" name="filter-price" value="50000000-80000000"
                            <?= !empty($priceRange) && $priceRange == '50000000-80000000' ? 'checked' : '' ?>>
                        <i class="fa"></i>
                        50.000.000đ - 80.000.000đ
                    </label>
                </li>
                <li>
                    <label for="filter-greater-1000">
                        <input type="radio" id="filter-greater-1000" name="filter-price" value="80000000-greater"
                            <?= !empty($priceRange) && $priceRange == '80000000-greater' ? 'checked' : '' ?>>
                        <i class="fa"></i>
                        Giá trên 80.000.000đ
                    </label>
                </li>
            </ul>
        </div>
    </div>
</aside>
