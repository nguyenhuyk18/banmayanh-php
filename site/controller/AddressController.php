<?php 
class AddressController {
    function getDistricts() {
        $province_id = location_id($_GET['province_id'] ?? '');
        $provinceRepository = new DistrictRepository();
        $districts = $provinceRepository->getByProvinceId($province_id);
        
        // gửi cho trình duyệt web danh sách dạng json
        // đối tượng thì phải đổi thuộc tính thành public hết mới đổi qua json được
        echo json_encode($districts);
    }


    function getWards() {
        $district_id = location_id($_GET['district_id'] ?? '');
        $wardRepository = new WardRepository();
        $districts = $wardRepository->getByDistrictId($district_id);
        
        // gửi cho trình duyệt web danh sách dạng json
        // đối tượng thì phải đổi thuộc tính thành public hết mới đổi qua json được
        echo json_encode($districts);
    }

    function getShippingFee() {
        $province_id = location_id($_GET['province_id'] ?? '');
        $provinceRepository = new ProvinceRepository();
        $province = $provinceRepository->find($province_id);
        require_record($province);
        $shipping_fee = $province->getShippingFee();
        echo $shipping_fee;
    }
}
