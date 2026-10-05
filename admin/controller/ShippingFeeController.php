<?php 
class ShippingFeeController {
	function list() {
		$page_title = "Phí giao hàng";
		$transportRepository = new TransportRepository();
		$transports = $transportRepository->getAll();
		include "view/shippingfee/list.php";
	}

	function edit() {
		$page_title = "Cập nhật phí giao hàng";
		$id = positive_id($_GET["id"] ?? 0);
		$transportRepository = new TransportRepository();
		$transport = require_record($transportRepository->find($id));
		include "view/shippingfee/edit.php";
	}

	function update() {
		require_fields(["id", "price"]);
		$id = positive_id($_POST["id"]);
		$price = input_text("price");
		if (!preg_match('/^[0-9]+$/D', $price)) {
			throw new InvalidArgumentException('Phí giao hàng phải là số nguyên không âm.');
		}
		$price = (int) $price;
		if ($price > 100000000) {
			throw new InvalidArgumentException('Phí giao hàng không được vượt quá 100.000.000 đ.');
		}
		$transportRepository = new TransportRepository();
		$transport = require_record($transportRepository->find($id));
		$transport->setPrice($price);
		if (!$transportRepository->update($transport)) {
			throw new RuntimeException('Không thể cập nhật phí giao hàng.');
		}
		$_SESSION['message'] = 'Đã cập nhật phí giao hàng cho ' . $transport->getProvince()->getName() . '.';
		header("Location: index.php?c=shippingfee");
		exit;
	}

	
}
