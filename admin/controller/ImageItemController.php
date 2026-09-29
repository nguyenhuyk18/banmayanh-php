<?php 
class ImageItemController {
	function list() {
		$page_title = "Hình ảnh của sản phẩm";
		$productRepository = new ProductRepository();
		$products = $productRepository->getAll();
		include "view/imageItem/list.php";
	}

	function detail() {
		//Liệt kê những hình ảnh chụp ở những khía cạnh khác nhau của product
		$id = $_GET["id"];
		$product_id = $id;
		$productRepository = new ProductRepository();
		$product = $productRepository->find($id);
		include "view/imageItem/detail.php";
	}

	function delete(){
		$id = $_GET["id"];
		$product_id = $_GET["product_id"];
		if ($this->remove($id)) {
			header("location: index.php?c=imageItem&a=detail&id=$product_id");
			exit;
		}
		
	}

	function save(){
		$product_id = positive_id($_GET["id"] ?? 0);
		require_record((new ProductRepository())->find($product_id));
		if (!empty($_FILES["image"]["name"]) && 
			$_FILES["image"]["error"] == 0) {
			$imageService = new ImageService();
			$correctFileName = $imageService->saveUpload($_FILES['image']);
			$data = [];
			$data["name"] = $correctFileName;
			$data["product_id"] = $product_id;
			$imageItemRepository = new ImageItemRepository();
			try {
			if ($imageItemRepository->save($data)) {

				header("location: index.php?c=imageItem&a=detail&id=$product_id");
				exit();
			}
			} catch (Throwable $error) { unlink(ABSPATH . 'upload/' . $correctFileName); throw $error; }
			echo $imageItemRepository->getError();
			exit();
		}

		throw new InvalidArgumentException('Vui lòng chọn ảnh hợp lệ.');

	}

	function deletes() {
		$ids = $_POST["ids"];
		$product_id = $_POST["product_id"];
		$flag = true;
		foreach ($ids as $id) {
			if (!$this->remove($id)) {
				$flag = false;
			}
		}

		if ($flag) {
			header("location: index.php?c=imageItem&a=detail&id=$product_id");
			exit;
		}
	}

	function remove($id) {
		$imageItemRepository = new ImageItemRepository();
		$imageItem = require_record($imageItemRepository->find(positive_id($id)));
		$filename = $imageItem->getName();
		if($imageItemRepository->delete($imageItem)) {
			$removedPath = ABSPATH . 'upload/' . basename($filename);
			if (file_exists($removedPath)) {
				unlink($removedPath);
			}
			return true;
		}
		echo $imageItemRepository->getError();
		return false;

	}
}
