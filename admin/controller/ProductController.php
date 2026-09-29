<?php 
class ProductController {
	function list() {
		$page_title = "Danh sách sản phẩm";
		$productRepository = new ProductRepository();
		$products = $productRepository->getAll();
		include "view/product/list.php";
	}

	function add() {
		$categoryRepository = new CategoryRepository();
		$categories = $categoryRepository->getAll();

		$brandRepository = new BrandRepository();
		$brands = $brandRepository->getAll();

		include "view/product/add.php";
	}

	function edit() {
		$id = $_GET["id"];
		$productRepository = new ProductRepository();
		$product = $productRepository->find($id);

		$categoryRepository = new CategoryRepository();
		$categories = $categoryRepository->getAll();

		$brandRepository = new BrandRepository();
		$brands = $brandRepository->getAll();

		include "view/product/edit.php";
	}

	function save(){
		$imageService = new ImageService();
		$filename = $imageService->saveUpload($_FILES['image'] ?? null);
		$data = [];
		$data["name"]					=$_POST["name"];
		$data["barcode"]				=$_POST["barcode"];
		$data["sku"]					=$_POST["sku"];
		$data["price"]					=$_POST["price"];
		$data["discount_percentage"]	=$_POST["discount_percentage"] ?: 0;
		$data["discount_from_date"]		=$_POST["discount_from_date"];
		$data["discount_to_date"]		=$_POST["discount_to_date"];
		$data["featured_image"]			=$filename;
		$data["inventory_qty"]			=$_POST["inventory_qty"];
		$data["created_date"]			= date("Y-m-d");
		$data["description"]			=$_POST["description"];
		$data["featured"]				=!empty($_POST["featured"]) ? 1 : 0;
		$data["category_id"]			=$_POST["category"];
		$data["brand_id"]				=$_POST["brand"];

		$productRepository = new ProductRepository();
		try {
		if($productRepository->save($data)) {
			header("location: index.php?c=product");
			exit();
		}
		} catch (Throwable $error) { unlink(ABSPATH . 'upload/' . $filename); throw $error; }
		echo $productRepository->getError();

	}

	function delete() {
		$id = $_GET["id"];
		$productRepository = new ProductRepository();
		$product = require_record($productRepository->find(positive_id($id)));
		$file = ABSPATH . 'upload/' . basename($product->getFeaturedImage());
		if($productRepository->delete($product)) {
			if (is_file($file)) unlink($file);
			header("location: index.php?c=product");
			exit();
		}

	}

	function update() {
		//var_dump($_FILES);
		$id = $_POST["id"];
		$productRepository = new ProductRepository();
		$product = require_record($productRepository->find(positive_id($id)));
		//set giá trị mới
		$product->setBarCode($_POST["barcode"]);
		$product->setName($_POST["name"]);
		$product->setSku($_POST["sku"]);
		$product->setPrice($_POST["price"]);
		$product->setDiscountPercentage($_POST["discount_percentage"]);
		$product->setDiscountFromDate($_POST["discount_from_date"]);
		$product->setDiscountToDate($_POST["discount_to_date"]);
		$product->setInventoryQty($_POST["inventory_qty"]);
		$product->setFeatured(!empty($_POST["featured"]) ? 1 : 0);
		$product->setCategoryId($_POST["category"]);
		$product->setBrandId($_POST["brand"]);
		$product->setDescription($_POST["description"]);

		$oldFile = null;
		$newFile = null;
		if (!empty($_FILES["image"]["name"]) && 
			$_FILES["image"]["error"] == 0) {
			$oldFile = ABSPATH . 'upload/' . basename($product->getFeaturedImage());
			$imageService = new ImageService();
			$correctFileName = $imageService->saveUpload($_FILES['image']);
			$newFile = ABSPATH . 'upload/' . $correctFileName;
			$product->setFeaturedImage($correctFileName);
		}

		try {
		if ($productRepository->update($product)) {
			if ($oldFile && is_file($oldFile)) unlink($oldFile);
			header("location: index.php?c=product");
			exit();
		}
		} catch (Throwable $error) { if ($newFile && is_file($newFile)) unlink($newFile); throw $error; }
		echo $productRepository->getError();
	}

	
	function findBarcode() {
		$barcode = $_GET["barcode"];
		$productRepository = new ProductRepository();
		$product = $productRepository->findByBarcode($barcode);
		if (empty($product)) {
			return;
		}

		$data = array(
			"id" => $product->getId(),
			"barcode" => $product->getBarcode(),
			"featured_image" => $product->getFeaturedImage(),
			"name" => $product->getName(),
			"price" => $product->getPrice(),
			"sale_price" => $product->getSalePrice(),
			"discount_percentage" => $product->getDiscountPercentage(),
		);
		echo json_encode($data);
	}
	

	

	
}
