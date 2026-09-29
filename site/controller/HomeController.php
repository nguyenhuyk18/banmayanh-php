<?php
class HomeController
{
    function index()
    {
		global $conn, $router, $slugify;
        $page = 1;
        $item_per_page = 4;
        $conds = []; //không điều kiện where

        // Sản phẩm nổi bật
        $sorts = ['featured' => 'DESC']; //cột featured giảm dần
        $productRepository = new ProductRepository();
        $featuredProducts = $productRepository->getBy($conds, $sorts, $page, $item_per_page);

        // Sản phẩm mới nhất
        $sorts = ['created_date' => 'DESC']; //cột featured giảm dần
        $productRepository = new ProductRepository();
        $latestProducts = $productRepository->getBy($conds, $sorts, $page, $item_per_page);

		$bestSellers = [];
		$result = $conn->query("SELECT oi.product_id, SUM(oi.qty) AS sold
			FROM order_item oi JOIN `order` o ON o.id = oi.order_id
			LEFT JOIN stripe_payment sp ON sp.order_id = o.id
			WHERE o.order_status_id <> 6 AND (o.payment_method <> 2 OR sp.status = 'paid')
			GROUP BY oi.product_id ORDER BY sold DESC, oi.product_id ASC LIMIT 4");
		while ($row = $result->fetch_assoc()) {
			$product = $productRepository->find($row['product_id']);
			if ($product) $bestSellers[] = $product;
		}

        // Lấy sản phẩm theo danh mục và đổ ra view

        // Đây là biến lưu trữ cấu trúc dữ liệu cần thiết để gở qua view
        $categoryProducts = [];
        // Lấy tất cả các danh mục trong database 
        $categoryRepository = new CategoryRepository();
        $categories = $categoryRepository->getAll();

        // Duyệt từng category để xây dựng cấu trúc mỗi phần tử:
        // Mỗi phần tử có 2 phần: tên danh mục và danh sách sản phẩm tương ứng với danh mục đó.
        $categoryLinks = [];
        foreach ($categories as $category) {
            $categoryName = $category->getName();
            $categoryId = $category->getId(); //2
            $categoryLinks[$categoryId] = $router->generate('category', [
                'slug' => $slugify->slugify($categoryName),
                'id' => $categoryId
            ]);
            $conds = [
                'category_id' => [
                    'type' => '=',
                    'val' => $categoryId
                ]
            ];
            $products = $productRepository->getBy($conds, ['featured'=>'DESC'], $page, $item_per_page);
            // SELECT * FROM view_product WHERE category_id = 2
            // Dấu [] bên trái dấu = nghĩa là thêm 1 phần tử vào cuối danh sách
            // phần tử là những gì nằm bên phải
            $categoryProducts[] = [
                'categoryName' => $categoryName,
                'products' => $products
            ];
        }


        require ABSPATH_SITE . 'view/home/index.php';
    }
}
