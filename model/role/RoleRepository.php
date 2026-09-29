<?php
class RoleRepository extends BaseRepository{
	protected function fetchAll($condition = null)
	{
		global $conn;
		$categories = array();
		$sql = "SELECT * FROM role";
		if ($condition) 
		{
			$sql .= " WHERE $condition";
		}

		$result = $conn->query($sql);

		if ($result->num_rows > 0) 
		{
			while ($row = $result->fetch_assoc()) 
			{
				$role = new Role($row["id"], $row["name"]);
				$categories[] = $role;
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
		$roles = $this->fetchAll($condition);
		$role = current($roles);
		return $role;
	}

	function save($data) {
        return db_insert('role', [
            'name' => $data["name"]
        ]);
    }

	function update($role) {
        return db_update('role', [
            'name' => $role->getName()
        ], $role->getId());
    }

	function delete($role) {
		global $conn;
		$id = $role->getId();
		$sql = "DELETE FROM role WHERE id=$id";
		if ($conn->query($sql) === TRUE) {
		    return true;
		} 
		echo "Error: " . $sql . PHP_EOL . $conn->error;
		return false;
	}

	function getByName($name) {
		$name = $GLOBALS['conn']->real_escape_string((string) $name);
		global $conn; 
		$condition = "name = '$name'";
		$roles = $this->fetchAll($condition);
		$role = current($roles);
		return $role;
	}
}
