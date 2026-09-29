<?php
class CommentRepository extends BaseRepository{
	
	protected function fetchAll($condition = null, $sort = null)
	{
		global $conn;
		$comments = array();
		$sql = "SELECT * FROM comment";
		if ($condition) 
		{
			$sql .= " WHERE $condition";
		}

		if ($sort) {
			$sql .= " $sort";
		}

		$result = $conn->query($sql);

		if ($result->num_rows > 0) 
		{
			while ($row = $result->fetch_assoc()) 
			{
				
				$comment = new Comment($row["id"], $row["email"], $row["fullname"], $row["star"], $row["created_date"], $row["description"], $row["product_id"]);
				$comments[] = $comment;
			}
		}

		return $comments;
	}

	function getAll() {
		return $this->fetchAll();
	}

	function find($id) {
        $id = positive_id($id);
		global $conn; 
		$condition = " id = $id";
		$comments = $this->fetchAll($condition);
		$comment = current($comments);
		return $comment;
	}

	function save($data) {
        return db_insert('comment', [
            'email' => $data["email"],
            'fullname' => $data["fullname"],
            'star' => $data["star"],
            'created_date' => $data["created_date"],
            'description' => $data["description"],
            'product_id' => $data["product_id"]
        ]);
    }

	function update($comment) {
        return db_update('comment', [
            'email' => $comment->getEmail(),
            'fullname' => $comment->getFullname(),
            'star' => $comment->getStar(),
            'created_date' => $comment->getCreatedDate(),
            'description' => $comment->getDescription(),
            'product_id' => $comment->getProductId()
        ], $comment->getId());
    }

	function delete($comment) {
		global $conn;
		$id = $comment->getId();
		$sql = "DELETE FROM comment WHERE id=$id";
		if ($conn->query($sql) === TRUE) {
		    return true;
		} 
		$this->error = "Error: " . $sql . PHP_EOL . $conn->error;
		return false;
	}

	function getByProductId($product_id) {
        $product_id = positive_id($product_id);
		return $this->fetchAll("product_id = $product_id", "ORDER BY id DESC");
	}
}