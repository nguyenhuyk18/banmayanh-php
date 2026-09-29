<?php
class CustomerRepository extends BaseRepository{
	protected function fetchAll($condition = null)
	{
		
		global $conn;
		$customers = array();
		$sql = "SELECT * FROM customer";
			
		if ($condition) 
		{
			$sql .= " WHERE $condition";
		}

		$result = $conn->query($sql);
		if ($result->num_rows > 0) 
		{
			while ($row = $result->fetch_assoc()) 
			{
				
				$customer = new Customer($row["id"], $row["name"], $row["password"], $row["mobile"], $row["email"], $row["login_by"], $row["shipping_name"], $row["shipping_mobile"], $row["ward_id"], $row["housenumber_street"], $row["is_active"]);
				$customers[] = $customer;
			}
		}

		return $customers;
	}

	function getAll() {
		return $this->fetchAll();
	}

	function findEmail($email) {
        $email = $GLOBALS["conn"]->real_escape_string((string) $email);
		global $conn; 
		$condition = "email = '$email'";
		$customers = $this->fetchAll($condition);
		$customer = current($customers);
		return $customer;
	}

	function findEmailAndPassword($email, $password) {
        $email = $GLOBALS["conn"]->real_escape_string((string) $email);
        $password = $GLOBALS["conn"]->real_escape_string((string) $password);
		global $conn; 
		$condition = "email = '$email' AND password = '$password'";
		$customers = $this->fetchAll($condition);
		$customer = current($customers);
		return $customer;
	}


	function save($data) {
        return db_insert('customer', [
            'name' => $data["name"],
            'password' => $data["password"],
            'mobile' => $data["mobile"],
            'email' => $data["email"],
            'login_by' => $data["login_by"],
            'shipping_name' => $data["shipping_name"],
            'shipping_mobile' => $data["shipping_mobile"],
            'ward_id' => $data["ward_id"],
            'housenumber_street' => $data["housenumber_street"],
            'is_active' => $data["is_active"]
        ]);
    }

	function update($customer) {
        return db_update('customer', [
            'name' => $customer->getName(),
            'password' => $customer->getPassword(),
            'mobile' => $customer->getMobile(),
            'email' => $customer->getEmail(),
            'login_by' => $customer->getLoginBy(),
            'shipping_name' => $customer->getShippingName(),
            'shipping_mobile' => $customer->getShippingMobile(),
            'ward_id' => $customer->getWardId(),
            'housenumber_street' => $customer->getHouseNumberStreet(),
            'is_active' => $customer->getIsActive()
        ], $customer->getId());
    }

	function delete($customer) {
		global $conn;
		$id = $customer->getId();
		$sql = "DELETE FROM customer WHERE id=$id";
		if ($conn->query($sql) === TRUE) {
		    return true;
		} 
		$this->error = "Error: " . $sql . PHP_EOL . $conn->error;
		return false;
	}

	function find($id) {
        $id = positive_id($id);
		global $conn; 
		$condition = "id = $id";
		$customers = $this->fetchAll($condition);
		$customer = current($customers);
		return $customer;
	}
}