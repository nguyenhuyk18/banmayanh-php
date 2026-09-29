<?php
class ImageItemRepository{
	protected function fetchAll($condition = null)
	{
		global $conn;
		$imageItems = array();
		$sql = "SELECT * FROM image_item";
		if ($condition) 
		{
			$sql .= " WHERE  $condition";
		}

		$result = $conn->query($sql);

		if ($result->num_rows > 0) 
		{
			while ($row = $result->fetch_assoc()) 
			{
				$imageItem = new ImageItem($row["id"], $row["name"], $row["product_id"]);
				$imageItems[] = $imageItem;
			}
		}

		return $imageItems;
	}

	function getAll() {
		return $this->fetchAll();
	}

	function getByProductId($product_id) {
        $product_id = positive_id($product_id);
		global $conn; 
		$condition = "product_id = $product_id";
		$imageItems = $this->fetchAll($condition);
		return $imageItems;
	}

	function find($id) {
        $id = positive_id($id);
		global $conn; 
		$condition = "id = $id";
		$imageItems = $this->fetchAll($condition);
		$imageItem = current($imageItems);
		return $imageItem;
	}

	function save($data) {
        return db_insert('image_item', [
            'name' => $data["name"],
            'product_id' => $data["product_id"]
        ]);
    }

	function update(ImageItem $imageItem) {
        return db_update('image_item', [
            'name' => $imageItem->getName(),
            'product_id' => $imageItem->getProductId()
        ], $imageItem->getId());
    }

	function delete(ImageItem $imageItem) {
		global $conn;
		$id = $imageItem->getId();
		$sql = "DELETE FROM image_item WHERE id=$id";
		if ($conn->query($sql) === TRUE) {
		    return true;
		} 
		echo "Error: " . $sql . PHP_EOL . $conn->error;
		return false;
	}
}