<?php 
class DashboardController {
	function list() {
		// var_dump($_GET);
		$orderRepository = new OrderRepository();
		$from = input_text('from_date', $_GET, date('Y-m-d'));
		$to = input_text('to_date', $_GET, date('Y-m-d'));
		foreach ([$from, $to] as $date) {
			if (!preg_match('/^\d{4}-\d{2}-\d{2}$/D', $date) || date('Y-m-d', strtotime($date)) !== $date) throw new InvalidArgumentException('Ngày báo cáo không hợp lệ.');
		}
		$from_date = $from . " 00:00:00";
		$to_date = $to . " 23:59:59";

		$conds = [
			"created_date" => [
				"type" => "BETWEEN",
				"val" => "'$from_date' AND '$to_date'",
			]
		];

		//SELECT * order WHERE created_date BETWEEN '2020-01-01' AND '2020-12-30'

		$orders = $orderRepository->getBy($conds);
		include "view/dashboard/list.php";
	}
}
