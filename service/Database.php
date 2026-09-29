<?php
function db_execute($sql, array $values = []) {
    global $conn;
    $stmt = $conn->prepare($sql);
    if ($values) $stmt->bind_param(str_repeat('s', count($values)), ...$values);
    $stmt->execute();
    return $stmt;
}

function db_normalize(array $data) {
    foreach ($data as $key => &$value) {
        if (!is_scalar($value) && $value !== null) throw new InvalidArgumentException('Dữ liệu không hợp lệ.');
        if (in_array($key, ['discount_from_date','discount_to_date'], true) && ($value === '' || $value === null)) $value = '1970-01-01';
        if (in_array($key, ['ward_id','staff_id','shipping_ward_id','delivered_date'], true) && $value === '') $value = null;
        if (in_array($key, ['price','discount_percentage','inventory_qty','star','featured','is_active','shipping_fee','qty','unit_price','total_price'], true)) {
            if ($value === '' || $value === null) $value = 0;
            if (!is_numeric($value) || $value < 0) throw new InvalidArgumentException('Giá và số lượng phải là số không âm.');
        }
        if ($key === 'discount_percentage' && $value > 100) throw new InvalidArgumentException('Giảm giá phải nằm trong khoảng 0–100%.');
    }
    unset($value);
    return $data;
}

function db_insert($table, array $data) {
    global $conn;
    $data = db_normalize($data);
    $columns = '`' . implode('`,`', array_keys($data)) . '`';
    db_execute('INSERT INTO `' . $table . '` (' . $columns . ') VALUES (' . implode(',', array_fill(0, count($data), '?')) . ')', array_values($data));
    return $conn->insert_id ?: true;
}

function db_update($table, array $data, $id) {
    $data = db_normalize($data);
    $assignments = implode(',', array_map(function ($column) { return '`' . $column . '` = ?'; }, array_keys($data)));
    db_execute('UPDATE `' . $table . '` SET ' . $assignments . ' WHERE id = ?', array_merge(array_values($data), [positive_id($id)]));
    return true;
}
