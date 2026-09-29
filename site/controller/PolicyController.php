<?php 
class PolicyController {
    // Chính sách trả hàng
    function return() {
        require ABSPATH_SITE . 'view/policy/return-table.php';
    }
    // Chính sách thanh toán
    function payment() {
        require ABSPATH_SITE . 'view/policy/payment-table.php';
    }
    // Chính sách giao hàng
    function delivery() {
        require ABSPATH_SITE . 'view/policy/delivery-table.php';
    }
}
