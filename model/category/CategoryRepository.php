<?php
class CategoryRepository extends BaseRepository{
	
	protected function fetchAll($condition = null)
	{
		global $conn;
		$categories = array();
		$sql = "SELECT * FROM category";
		if ($condition) 
		{
			$sql .= " WHERE  $condition";//SELECT * FROM category WHERE id =1
		}

		$result = $conn->query($sql);

		if ($result->num_rows > 0) 
		{
			while ($row = $result->fetch_assoc()) 
			{
				$category = new Category($row["id"], $row["name"]);
				$categories[] = $category;
			}
		}
		return $categories;
	}

	function getAll() {
		return $this->fetchAll();
	}

	function find($id) {
        $id = positive_id($id);
		global $conn; 
		$condition = "id = $id";
		$categories = $this->fetchAll($condition);
		$category = current($categories);
		return $category;
	}

	function save($data) {
        return db_insert('category', [
            'name' => $data["name"]
        ]);
    }

	function update($category) {
        return db_update('category', [
            'name' => $category->getName()
        ], $category->getId());
    }

	function delete($category) {
		global $conn;
		$id = $category->getId();
		$sql = "DELETE FROM category WHERE id=$id";
		if ($conn->query($sql) === TRUE) {
		    return true;
		} 
		$this->error = "Error: " . $sql . PHP_EOL . $conn->error;
		return false;
	}
}