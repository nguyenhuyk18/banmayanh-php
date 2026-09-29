<?php 
// CUNG CẤP BIẾN ĐỂ HỖ TRỢ CHO GIAO DIỆN PHẦN ĐỊA CHỈ.


// CẦN DANH SÁCH TỈNH THÀNH PHỐ
$provinceRepository = new ProvinceRepository();
$provinces = $provinceRepository->getAll();

// CÓ MỖI ID PHƯỜNG XÃ TRONG DATABASE , LÀM SAO SUY RA ĐƯỢC DANH SÁCH QUẬN HUYỆN THUỘC TỈNH THÀNH MÀ KHÁCH HÀNG ĐÓ Ở
$selected_ward = $customer->getWard(); // dựa vào cột ward_id trng bảng customer, tìm được đối tượng ward
// khởi tạo rỗng để dùng trong trường hợp chưa chỉ định nơi ở (mã phường xã)
$districts = [];
$wards = [];
$selected_province_id = NULL;
$selected_district_id = NULL;
$selected_ward_id = NULL;
$shipping_fee = 0;


if($selected_ward) {
    // Từ ward, tìm ra quận huyện mà người đó ở
    $selected_district = $selected_ward->getDistrict();

    // Từ district, tìm ra được cái tỉnh thành mà người đó ở
    $selected_province = $selected_district->getProvince();

    // tìm danh sách quận huyện tương ứng với tỉnh thành người đó ở
    $districts = $selected_province->getDistricts();

    // tìm danh sách phường xã tương ứng với quận huyện người đó ở
    $wards = $selected_district->getWards();


    $selected_ward_id = $selected_ward->getId();
    $selected_district_id = $selected_district->getId();
    $selected_province_id = $selected_province->getId();


    // Tìm danh sách quận huyện tương ứng với tỉnh thành người đó ở
    $shipping_fee = $selected_province->getShippingFee();
}
