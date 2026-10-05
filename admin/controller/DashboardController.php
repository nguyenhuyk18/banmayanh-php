<?php 
class DashboardController {
	function list() {
		$orderRepository = new OrderRepository();
		$from = input_text('from_date', $_GET, date('Y-m-d'));
		$to = input_text('to_date', $_GET, date('Y-m-d'));
		foreach ([$from, $to] as $date) {
			if (!preg_match('/^\d{4}-\d{2}-\d{2}$/D', $date) || date('Y-m-d', strtotime($date)) !== $date) throw new InvalidArgumentException('Ngày báo cáo không hợp lệ.');
		}
		if ($from > $to) throw new InvalidArgumentException('Ngày bắt đầu không được lớn hơn ngày kết thúc.');
		$from_date = $from . " 00:00:00";
		$to_date = $to . " 23:59:59";

		$conds = [
			"created_date" => [
				"type" => "BETWEEN",
				"val" => "'$from_date' AND '$to_date'",
			]
		];

		$orders = $orderRepository->getBy($conds, ['created_date' => 'DESC']);
		$order_count = count($orders);
		$cancel_number = 0;
		$revenue = 0;
		foreach ($orders as $order) {
			if ($order->getStatusId() == 6) {
				$cancel_number++;
				continue;
			}
			if ($order->getPaymentMethod() == 2) {
				$payment = db_execute('SELECT status FROM stripe_payment WHERE order_id=?', [$order->getId()])->get_result()->fetch_assoc();
				if (!$payment || $payment['status'] !== 'paid') continue;
			}
			$revenue += $order->getSubTotalPrice() + $order->getShippingFee();
		}
		$range_query = http_build_query(['from_date' => $from, 'to_date' => $to]);
		include "view/dashboard/list.php";
	}
}
