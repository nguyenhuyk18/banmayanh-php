<?php 
class CustomerController {
	function list() {
		$page_title = "Khách hàng";
		$customerRepository = new CustomerRepository();
		$customers = $customerRepository->getAll();
		include "view/customer/list.php";
	}

	function add() {
		$page_title = "Thêm khách hàng";
		$provinceRepository = new ProvinceRepository();
		$provinces = $provinceRepository->getAll();
		include "view/customer/add.php";
	}

	function save() {
		require_fields(['fullname','email','password','mobile']);
		if (!filter_var(input_text('email'), FILTER_VALIDATE_EMAIL)) throw new InvalidArgumentException('Email không hợp lệ.');
		$data = []; 
		$data["name"] = $_POST["fullname"];
		$data["password"]  = password_hash($_POST["password"], PASSWORD_DEFAULT);
		$data["mobile"] = $_POST["mobile"];
		$data["email"] = $_POST["email"];
		$data["login_by"] = "form";
		$data["shipping_name"] = $_POST["shipping_name"];
		$data["shipping_mobile"] = $_POST["shipping_mobile"];
		$data["ward_id"] = $_POST["ward"] ?? null;
		$data["is_active"] = !empty($_POST["active"]) ? 1 : 0;
		$data["housenumber_street"] = $_POST["housenumber_street"] ?? $_POST["housenumumber_street"] ?? '';
		

		$customerRepository = new CustomerRepository();
		if ($customerRepository->save($data)) {
			header("location: index.php?c=customer");
			exit;
		}
	}

	function edit() {
		$page_title = "Cập nhật khách hàng";
		$id = $_GET["id"];
		$customerRepository = new CustomerRepository();
		$customer = require_record($customerRepository->find(positive_id($id)));
		$provinces = (new ProvinceRepository())->getAll();
		include "view/customer/edit.php";
	}

	function update() {
		$id = positive_id($_POST["id"] ?? 0);
		require_fields(['fullname','email','mobile']);
		if (!filter_var(input_text('email'), FILTER_VALIDATE_EMAIL)) throw new InvalidArgumentException('Email không hợp lệ.');
		$customerRepository = new CustomerRepository();
		$customer = require_record($customerRepository->find($id));
		$customer->setName(input_text('fullname'));
		$customer->setEmail(input_text('email'));
		$customer->setMobile(input_text('mobile'));
		$customer->setShippingName(input_text('shipping_name'));
		$customer->setShippingMobile(input_text('shipping_mobile'));
		$customer->setHousenumberStreet(input_text('housenumber_street'));
		$customer->setWardId(input_text('ward'));
		$customer->setIsActive(!empty($_POST['active']) ? 1 : 0);
		if (input_text('password') !== '') $customer->setPassword(password_hash(input_text('password'), PASSWORD_DEFAULT));
		if ($customerRepository->update($customer)) {
			header("location: index.php?c=customer");
			exit;
		}

	}

	function delete() {
		$id = positive_id($_GET["id"] ?? 0);
		if ($this->remove($id)) {
			header("location: index.php?c=customer");
			exit;
		}
	}

	function remove($id) {
		$customerRepository = new CustomerRepository();
		$customer = require_record($customerRepository->find($id));
		if($customerRepository->delete($customer)) {
			return true;
		}
		echo $customerRepository->getError();
		return false;

	}

	function deletes() {
		$ids = $_POST["ids"];
		$flag = true;
		foreach ($ids as $id) {
			if (!$this->remove($id)) {
				$flag = false;
			}
		}

		if ($flag) {
			header("location: index.php?c=customer");
			exit;
		}
	}
}
