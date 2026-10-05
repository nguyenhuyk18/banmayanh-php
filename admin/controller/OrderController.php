<?php 
class OrderController {
	function list() {
		$page_title = "Danh sách đơn hàng";
		$orderRepository = new OrderRepository();
		$from = input_text('from_date', $_GET);
		$to = input_text('to_date', $_GET);
		$report = input_text('report', $_GET, 'all');
		if (!in_array($report, ['all', 'revenue', 'cancelled'], true)) throw new InvalidArgumentException('Bộ lọc đơn hàng không hợp lệ.');
		$conds = [];
		if ($from !== '' || $to !== '') {
			if ($from === '' || $to === '') throw new InvalidArgumentException('Vui lòng chọn đầy đủ khoảng thời gian.');
			foreach ([$from, $to] as $date) {
				if (!preg_match('/^\d{4}-\d{2}-\d{2}$/D', $date) || date('Y-m-d', strtotime($date)) !== $date) throw new InvalidArgumentException('Ngày báo cáo không hợp lệ.');
			}
			if ($from > $to) throw new InvalidArgumentException('Ngày bắt đầu không được lớn hơn ngày kết thúc.');
			$conds['created_date'] = ['type' => 'BETWEEN', 'val' => "'{$from} 00:00:00' AND '{$to} 23:59:59'"];
		}
		if ($report === 'cancelled') $conds['order_status_id'] = ['type' => '=', 'val' => 6];
		$orders = $orderRepository->getBy($conds, ['created_date' => 'DESC']);
		if ($report === 'revenue') {
			$orders = array_values(array_filter($orders, function ($order) {
				if ($order->getStatusId() == 6) return false;
				if ($order->getPaymentMethod() != 2) return true;
				$payment = db_execute('SELECT status FROM stripe_payment WHERE order_id=?', [$order->getId()])->get_result()->fetch_assoc();
				return $payment && $payment['status'] === 'paid';
			}));
		}
		include "view/order/list.php";
	}

	function add() {
		$page_title = "Tạo đơn hàng";
		$customerRepository = new CustomerRepository();
		$customers = $customerRepository->getAll();

		$statusRepository = new StatusRepository();
		$statuses = $statusRepository->getAll();

		$provinceRepository = new ProvinceRepository();
		$provinces = $provinceRepository->getAll();

		$staffRepository = new StaffRepository();
		$staffs = $staffRepository->getAll();
		
		include "view/order/add.php";
	}

	function edit() {
		$order = require_record((new OrderRepository())->find(positive_id($_GET['order_id'] ?? $_GET['id'] ?? 0)));
		$statuses = (new StatusRepository())->getAll();
		$staffs = (new StaffRepository())->getAll();
		$payment = db_execute('SELECT status FROM stripe_payment WHERE order_id=?', [$order->getId()])->get_result()->fetch_assoc();
		$page_title = 'Xử lý đơn hàng';
		include 'view/order/edit.php';
	}

	function update() {
		global $conn;
		$orderId = positive_id($_POST['order_id'] ?? 0);
		$order = require_record((new OrderRepository())->find($orderId));
		$next = positive_id($_POST['status'] ?? 0);
		require_record((new StatusRepository())->find($next));
		$payment = db_execute('SELECT status,session_id FROM stripe_payment WHERE order_id=?', [$orderId])->get_result()->fetch_assoc();
		if ($order->getStatusId() == 6 && $next != 6) throw new DomainException('Đơn đã hủy không thể mở lại.');
		if ($payment && $payment['status'] === 'paid' && $next == 6) throw new DomainException('Đơn Stripe đã thanh toán cần hoàn tiền trước khi hủy.');
		if ($payment && $payment['status'] !== 'paid' && $next != 6) throw new DomainException('Đơn Stripe chưa thanh toán, không thể xác nhận hoặc giao hàng.');
		if ($payment && $payment['status'] === 'pending' && $next == 6) {
			$session = StripeService::retrieve($payment['session_id']);
			if (($session['payment_status'] ?? '') === 'paid') {
				StripeService::reconcile($session);
				throw new DomainException('Stripe vừa xác nhận thanh toán. Hãy kiểm tra hoàn tiền trước khi hủy.');
			}
			if (($session['status'] ?? '') === 'open') $session = StripeService::request('POST', 'checkout/sessions/' . rawurlencode($payment['session_id']) . '/expire');
			StripeService::reconcile($session, true);
			header('Location: index.php?c=order&a=list');
			exit;
		}
		$conn->begin_transaction();
		try {
			if ($order->getStatusId() != 6 && $next == 6) db_execute('UPDATE product p JOIN order_item oi ON oi.product_id=p.id SET p.inventory_qty=p.inventory_qty+oi.qty WHERE oi.order_id=?', [$orderId]);
			$order->setStatusId($next);
			if ($next != 6) {
				$staffId = input_text('staff_id');
				if ($staffId !== '') { require_record((new StaffRepository())->find(positive_id($staffId))); $order->setStaffId($staffId); }
			}
			(new OrderRepository())->update($order);
			$conn->commit();
		} catch (Throwable $error) { $conn->rollback(); throw $error; }
		header('Location: index.php?c=order&a=list');
		exit;
	}

	function save() {
		global $conn;
		require_fields(['status','customer','shipping_name','shipping_mobile','payment_method','ward','housenumber_street','delivered_date']);
		if (input_text('payment_method') !== '0') throw new InvalidArgumentException('Đơn do admin tạo chỉ hỗ trợ COD.');
		$customerId = positive_id($_POST['customer']);
		require_record((new CustomerRepository())->find($customerId));
		$ward = require_record((new WardRepository())->find(location_id($_POST['ward'])));
		if (!preg_match('/^0[0-9]{9}$/D', input_text('shipping_mobile'))) throw new InvalidArgumentException('Số điện thoại không hợp lệ.');
		$ids = $_POST['product_ids'] ?? [];
		$qtys = $_POST['qties'] ?? [];
		if (!is_array($ids) || !is_array($qtys) || !$ids || count($ids) !== count($qtys)) throw new InvalidArgumentException('Vui lòng thêm ít nhất một sản phẩm.');
		$items = [];
		foreach ($ids as $index => $id) {
			$id = positive_id($id);
			$items[$id] = ($items[$id] ?? 0) + positive_id($qtys[$index]);
		}
		ksort($items);
		$conn->begin_transaction();
		try {
			$products = [];
			foreach ($items as $id => $qty) {
				$stmt = db_execute('SELECT id FROM product WHERE id = ? FOR UPDATE', [$id]);
				if (!$stmt->get_result()->fetch_assoc()) throw new DomainException('Sản phẩm không tồn tại.');
				$product = require_record((new ProductRepository())->find($id));
				if ($qty > $product->getInventoryQty()) throw new DomainException('Sản phẩm không còn đủ số lượng.');
				$products[$id] = $product;
			}
			$province = require_record($ward->getDistrict()->getProvince());
			$orderId = (new OrderRepository())->save([
				'created_date'=>date('Y-m-d H:i:s'), 'order_status_id'=>positive_id($_POST['status']),
				'staff_id'=>$_POST['staff'] ?: null, 'customer_id'=>$customerId,
				'shipping_fullname'=>input_text('shipping_name'), 'shipping_mobile'=>input_text('shipping_mobile'),
				'payment_method'=>input_text('payment_method'), 'shipping_ward_id'=>$ward->getId(),
				'shipping_housenumber_street'=>input_text('housenumber_street'),
				'shipping_fee'=>$province->getShippingFee(), 'delivered_date'=>input_text('delivered_date')
			]);
			foreach ($items as $id => $qty) {
				$item = ['order_id'=>$orderId,'product_id'=>$id,'qty'=>$qty,'unit_price'=>$products[$id]->getSalePrice(),'total_price'=>$products[$id]->getSalePrice() * $qty];
				(new OrderItemRepository())->save($item);
				$stmt = db_execute('UPDATE product SET inventory_qty = inventory_qty - ? WHERE id = ? AND inventory_qty >= ?', [$item['qty'],$item['product_id'],$item['qty']]);
				if ($stmt->affected_rows !== 1) throw new DomainException('Sản phẩm không còn đủ số lượng.');
			}
			$conn->commit();
		} catch (Throwable $error) { $conn->rollback(); throw $error; }
		header('Location: index.php?c=order&a=list');
		exit;
	}

	function ajaxGetShippingInfoDefault() {
		$customer_id = $_GET["customer_id"];
		$customerRepository = new CustomerRepository();
		$customer = $customerRepository->find($customer_id);

		$provinceRepository = new ProvinceRepository();
		$provinces = $provinceRepository->getAll();
		$data = [];
		$data["shipping_name"] = $customer->getShippingName();
		$data["shipping_mobile"] = $customer->getShippingMobile();
		$data["housenumber_street"] = $customer->getHousenumberStreet();
		$data["provinces"] = [];
		$data["districts"] = [];
		$data["wards"] = [];
		if (!empty($customer->getWardId())) {
			$data["selected_ward_id"] = $customer->getWardId();

			$ward = $customer->getWard();
			$data["selected_district_id"] = $ward->getDistrictId();

			$district = $ward->getDistrict();
			$data["selected_province_id"] = $district->getProvinceId();

			$province = $district->getProvince();

			$wards = $district->getWards();
			foreach ($wards as $w) {
				$data["wards"][] = ["id" => $w->getId(), "name" => $w->getName()];
			}

			$districts = $province->getDistricts();  

			foreach ($districts as $d) {
				$data["districts"][] = ["id" => $d->getId(), "name" => $d->getName()];
			}
		}

		foreach ($provinces as $p) {
			$data["provinces"][] = ["id" => $p->getId(), "name" => $p->getName()];
		}
		echo json_encode($data);
	}

	function delete() {
		global $conn;
		$order_id = positive_id($_GET["order_id"] ?? 0);
		$orderRepository = new OrderRepository();
		$order = require_record($orderRepository->find($order_id));
		$payment = db_execute('SELECT status FROM stripe_payment WHERE order_id=?', [$order_id])->get_result()->fetch_assoc();
		if ($payment && in_array($payment['status'], ['creating','pending','paid'], true)) throw new DomainException('Không thể xóa đơn Stripe đang thanh toán hoặc đã thanh toán.');
		$conn->begin_transaction();
		try {
			if ($order->getStatusId() != 6) foreach ($order->getOrderItems() as $item) db_execute('UPDATE product SET inventory_qty = inventory_qty + ? WHERE id = ?', [$item->getQty(), $item->getProductId()]);
			if (!$orderRepository->delete($order)) throw new RuntimeException('Could not delete order');
			$conn->commit();
		} catch (Throwable $error) { $conn->rollback(); throw $error; }
		header("location: index.php?c=order");
		exit;
	}

	function confirm(){
		$order_id = $_GET["order_id"];
		$orderRepository = new OrderRepository();
		$order = $orderRepository->find($order_id);
		require_record($order);
		$payment = db_execute('SELECT status FROM stripe_payment WHERE order_id=?', [$order_id])->get_result()->fetch_assoc();
		if ($payment && $payment['status'] !== 'paid') throw new DomainException('Đơn Stripe chưa được thanh toán.');
		$order->setStatusId(2);//xác nhận đơn hàng
		$staffRepository = new StaffRepository();
		$staff = $staffRepository->findUsername($_SESSION["username"]);
		$staff_id = $staff->getId();
		$order->setStaffId($staff_id);//người xác nhận là người có trách nhiệm trên đơn hàng
		if ($orderRepository->update($order)) {
			header("location: index.php?c=order");
			exit;
		}
	}
}
