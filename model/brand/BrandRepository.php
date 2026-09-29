<?php
class BrandRepository extends BaseRepository{
	
	protected function fetchAll($condition = null)
	{
		global $conn;
		$brands = array();
		$sql = "SELECT * FROM brand";
		if ($condition) 
		{
			$sql .= " WHERE  $condition";
		}

		$result = $conn->query($sql);

		if ($result->num_rows > 0) 
		{
			while ($row = $result->fetch_assoc()) 
			{
				$brand = new Brand($row["id"], $row["name"]);
				$brands[] = $brand;
			}
		}
		return $brands;
	}

	function getAll() {
		return $this->fetchAll();
	}

	function find($id) {
        $id = positive_id($id);
		global $conn; 
		$condition = "id = $id";
		$brands = $this->fetchAll($condition);
		$brand = current($brands);
		return $brand;
	}

	function save($data) {
        return db_insert('brand', [
            'name' => $data["name"]
        ]);
    }

	function update($brand) {
        return db_update('brand', [
            'name' => $brand->getName()
        ], $brand->getId());
    }

	function delete($brand) {
		global $conn;
		$id = $brand->getId();
		$sql = "DELETE FROM brand WHERE id=$id";
		if ($conn->query($sql) === TRUE) {
		    return true;
		} 
		$this->error = "Error: " . $sql . PHP_EOL . $conn->error;
		return false;
	}
}