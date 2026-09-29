<?php
class OrderRepository{
	protected function fetchAll($condition = null, $sort = null)
	{
		global $conn;
		$orders = array();
		$sql = "SELECT * FROM `order`";
		if ($condition) 
		{
			$sql .= " WHERE  $condition";
		}

		if ($sort) {
			$sql .= " $sort";
		}

		$result = $conn->query($sql);

		if ($result->num_rows > 0) 
		{
			while ($row = $result->fetch_assoc()) 
			{
				$order = new Order($row["id"], $row["created_date"], $row["order_status_id"], $row["staff_id"], $row["customer_id"], $row["shipping_fullname"], $row["shipping_mobile"], $row["payment_method"], $row["shipping_ward_id"], $row["shipping_housenumber_street"], $row["shipping_fee"], $row["delivered_date"]);
				$orders[] = $order;
			}
		}

		return $orders;
	}

	function getAll() {
		return $this->fetchAll();
	}

	function getByCustomerId($customer_id) {
        $customer_id = positive_id($customer_id);
		global $conn; 
		$condition = "customer_id = $customer_id";
		$sort = "ORDER BY id DESC";
		return $this->fetchAll($condition, $sort);
	}

	function find($id) {
        $id = positive_id($id);
		global $conn; 
		$condition = "id = $id";
		$orders = $this->fetchAll($condition);
		$order = current($orders);
		return $order;
	}

	function save($data) {
        return db_insert('order', [
            'created_date' => $data["created_date"],
            'order_status_id' => $data["order_status_id"],
            'staff_id' => $data["staff_id"],
            'customer_id' => $data["customer_id"],
            'shipping_fullname' => $data["shipping_fullname"],
            'shipping_mobile' => $data["shipping_mobile"],
            'payment_method' => $data["payment_method"],
            'shipping_ward_id' => $data["shipping_ward_id"],
            'shipping_housenumber_street' => $data["shipping_housenumber_street"],
            'shipping_fee' => $data["shipping_fee"],
            'delivered_date' => $data["delivered_date"]
        ]);
    }

	function update($order) {
        return db_update('order', [
            'created_date' => $order->getCreatedDate(),
            'order_status_id' => $order->getStatusId(),
            'staff_id' => $order->getStaffId(),
            'customer_id' => $order->getCustomerId(),
            'shipping_fullname' => $order->getShippingFullname(),
            'shipping_mobile' => $order->getShippingMobile(),
            'payment_method' => $order->getPaymentMethod(),
            'shipping_ward_id' => $order->getShippingWardId(),
            'shipping_housenumber_street' => $order->getShippingHousenumberStreet(),
            'shipping_fee' => $order->getShippingFee(),
            'delivered_date' => $order->getDeliveredDate()
        ], $order->getId());
    }

	function delete($order) {
		global $conn;
		$orderItemRepository = new OrderItemRepository();
		$orderItems = $order->getOrderItems();
		foreach ($orderItems as $orderItem) {
			if (!$orderItemRepository->delete($orderItem)) {
				echo "Error: " . $sql . PHP_EOL . $conn->error;
				return false;
			}
			
		}
		
		$id = $order->getId();
		$sql = "DELETE FROM `order` WHERE id=$id";
		if ($conn->query($sql) === TRUE) {
		    return true;
		} 
		echo "Error: " . $sql . PHP_EOL . $conn->error;
		return false;
	}

	function getBy($array_conds = array(), $array_sorts = array(), $page = null, $qty_per_page = null) {
		if ($page) {
			$page_index = $page - 1;
		}
		
		$temp = array();
		foreach($array_conds as $column => $cond) {
			$type = $cond['type'];
			$val = $cond['val'];
			$str = "$column $type ";
			if (in_array($type, array("BETWEEN", "LIKE"))) {
				$str .= "$val";
			}
			else {
				$str .= "'$val'";
			}
			$temp[] = $str;
		}
		$condition = null;

		if (count($array_conds)) {
			$condition = implode(" AND ", $temp);
		}

		$temp = array();
		foreach($array_sorts as $key => $sort) {
			$temp[] = "$key $sort";
		}
		$sort = null;

		if (count($array_sorts)) {
			$sort = "ORDER BY ". implode(" , ", $temp);
		}

		$limit = null;
		if ($qty_per_page) {
			$start = $page_index * $qty_per_page;
			$limit = "LIMIT $start, $qty_per_page";
		}
		

		return $this->fetchAll($condition, $sort, $limit);
	}
}