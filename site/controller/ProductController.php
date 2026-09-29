<?php
class ProductController {
    function index($category_id = null) {
        global $conn;
        $conds = [];
        $sorts = [];
        $page = max(1, (int) input_text('page', $_GET, '1'));
        $item_per_page = 10;
        $category_id = $category_id ?? input_text('category_id', $_GET);
        if ($category_id !== '') $conds['category_id'] = ['type'=>'=', 'val'=>positive_id($category_id)];
        $priceRange = input_text('price-range', $_GET);
        if ($priceRange !== '') {
            if (!preg_match('/^(\d+)-(\d+|greater)$/D', $priceRange, $parts)) throw new InvalidArgumentException('Khoảng giá không hợp lệ.');
            $start = (int) $parts[1];
            if ($parts[2] === 'greater') $conds['sale_price'] = ['type'=>'>=', 'val'=>$start];
            else {
                $end = (int) $parts[2];
                if ($end < $start) throw new InvalidArgumentException('Khoảng giá không hợp lệ.');
                $conds['sale_price'] = ['type'=>'BETWEEN', 'val'=>"$start AND $end"];
            }
        }
        $search = input_text('search', $_GET);
        if ($search !== '') $conds['name'] = ['type'=>'LIKE', 'val'=>"'%" . $conn->real_escape_string($search) . "%'"];
        $sort = input_text('sort', $_GET);
        if ($sort !== '') {
            $map = ['price'=>'sale_price','alpha'=>'name','created'=>'created_date'];
            if (!preg_match('/^(price|alpha|created)-(asc|desc)$/Di', $sort, $parts)) throw new InvalidArgumentException('Cách sắp xếp không hợp lệ.');
            $sorts[$map[strtolower($parts[1])]] = strtoupper($parts[2]);
        }
        $repository = new ProductRepository();
        $totalProductNumber = $repository->getByNumber($conds);
        $totalPage = max(1, (int) ceil($totalProductNumber / $item_per_page));
        $page = min($page, $totalPage);
        $products = $repository->getBy($conds, $sorts, $page, $item_per_page);
        $categories = (new CategoryRepository())->getAll();
        $selectedCategory = null;
        foreach ($categories as $category) {
            if ((int) $category->getId() === (int) $category_id) {
                $selectedCategory = $category;
                break;
            }
        }
        require ABSPATH_SITE . 'view/product/index.php';
    }

    function detail($id = null) {
        $id = positive_id($id ?? $_GET['id'] ?? $_GET['product_id'] ?? 0);
        $repository = new ProductRepository();
        $product = require_record($repository->find($id));
        $category_id = $product->getCategoryId();
        $relatedProducts = $repository->getBy(['category_id'=>['type'=>'=','val'=>$category_id], 'id'=>['type'=>'!=','val'=>$id]]);
        $categories = (new CategoryRepository())->getAll();
        require ABSPATH_SITE . 'view/product/detail.php';
    }

    function storeComment() {
        require_fields(['product_id','email','fullname','rating','description']);
        $product = require_record((new ProductRepository())->find(positive_id($_POST['product_id'])));
        $rating = filter_var(input_text('rating'), FILTER_VALIDATE_INT, ['options'=>['min_range'=>1,'max_range'=>5]]);
        if (!$rating || !filter_var(input_text('email'), FILTER_VALIDATE_EMAIL)) throw new InvalidArgumentException('Email hoặc số sao không hợp lệ.');
        (new CommentRepository())->save(['product_id'=>$product->getId(), 'email'=>input_text('email'), 'fullname'=>input_text('fullname'), 'star'=>$rating, 'created_date'=>date('Y-m-d H:i:s'), 'description'=>input_text('description')]);
        db_execute('UPDATE product SET star = (SELECT AVG(star) FROM comment WHERE product_id = ?) WHERE id = ?', [$product->getId(), $product->getId()]);
        require ABSPATH_SITE . 'view/product/comments.php';
    }
}
