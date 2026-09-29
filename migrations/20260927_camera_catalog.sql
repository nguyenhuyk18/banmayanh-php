-- Chuyển dữ liệu demo GodaShop sang catalog thiết bị nhiếp ảnh.
-- Migration idempotent: có thể chạy lại an toàn trên database hiện có.

SET NAMES utf8mb4;

UPDATE category SET name = CASE id
    WHEN 1 THEN 'Máy ảnh Mirrorless'
    WHEN 2 THEN 'Máy ảnh DSLR & Compact'
    WHEN 3 THEN 'Ống kính'
    WHEN 4 THEN 'Action Camera'
    WHEN 5 THEN 'Phụ kiện nhiếp ảnh'
    WHEN 6 THEN 'Thiết bị quay phim'
    ELSE name
END
WHERE id BETWEEN 1 AND 6;

UPDATE brand SET name = CASE id
    WHEN 1 THEN 'Lensora Select'
    WHEN 2 THEN 'Lumina Optical'
    WHEN 3 THEN 'Motion Pro'
    ELSE name
END
WHERE id BETWEEN 1 AND 3;

UPDATE product SET
    name = CASE id
        WHEN 2 THEN 'Lumina X7 Mirrorless Kit 18-55mm'
        WHEN 3 THEN 'Lensora ProFrame X9 Full-frame Body'
        WHEN 4 THEN 'Compact C1 Creator Camera'
        WHEN 5 THEN 'Vantage D850 DSLR Kit 24-120mm'
        WHEN 6 THEN 'PocketShot P3 Compact Camera'
        WHEN 7 THEN 'Creator Cam M50 Vlog Kit'
        WHEN 8 THEN 'Rangefinder R2 Classic Camera'
        WHEN 9 THEN 'Motion A4K Adventure Camera'
        WHEN 10 THEN 'Motion Mini Flow Action Camera'
        WHEN 11 THEN 'Lumina Prime 35mm f/1.8 Lens'
        WHEN 12 THEN 'Lumina Prime 50mm f/1.4 Lens'
        WHEN 13 THEN 'Lumina Pro Zoom 24-70mm f/2.8 Lens'
        WHEN 14 THEN 'Lumina Tele 70-200mm f/2.8 Lens'
        WHEN 15 THEN 'Lumina Ultra Wide 14mm f/2.8 Lens'
        WHEN 16 THEN 'Lumina Macro 90mm f/2.8 Lens'
        WHEN 17 THEN 'Carbon Tripod T-Pro Travel'
        WHEN 18 THEN 'Carbon Tripod T-Lite Creator'
        WHEN 19 THEN 'Studio Tripod T-Max 190'
        WHEN 20 THEN 'Video Tripod Fluid Head V2'
        WHEN 21 THEN 'Mini Tripod Creator Pod'
        WHEN 22 THEN 'Travel Tripod Air Carbon'
        WHEN 23 THEN 'Motion Aqua 5K Action Camera'
        WHEN 24 THEN 'Cinema C6 Creator Camera'
        WHEN 25 THEN 'Lensora Studio Starter Camera Kit'
        ELSE name
    END,
    sku = CONCAT('CAM-', LPAD(id, 4, '0')),
    price = CASE
        WHEN id IN (11,12,15,16) THEN 8990000 + (id * 150000)
        WHEN id IN (13,14) THEN 24990000 + (id * 250000)
        WHEN id BETWEEN 17 AND 22 THEN 2490000 + (id * 120000)
        WHEN id IN (9,10,23) THEN 6990000 + (id * 90000)
        ELSE 15990000 + (id * 320000)
    END,
    discount_percentage = CASE WHEN MOD(id, 3) = 0 THEN 10 WHEN MOD(id, 2) = 0 THEN 5 ELSE 0 END,
    discount_from_date = '2026-01-01',
    discount_to_date = '2030-12-31',
    featured_image = CASE
        WHEN id IN (9,10,23) THEN 'camera-action.png'
        WHEN id BETWEEN 11 AND 16 THEN 'camera-lens.png'
        WHEN id BETWEEN 17 AND 22 THEN 'camera-tripod.png'
        ELSE 'camera-mirrorless.png'
    END,
    inventory_qty = 8 + MOD(id * 7, 34),
    category_id = CASE
        WHEN id IN (2,3,7,8,25) THEN 1
        WHEN id IN (4,5,6) THEN 2
        WHEN id BETWEEN 11 AND 16 THEN 3
        WHEN id IN (9,10,23) THEN 4
        WHEN id BETWEEN 17 AND 22 THEN 5
        WHEN id = 24 THEN 6
        ELSE category_id
    END,
    brand_id = 1 + MOD(id, 3),
    created_date = DATE_ADD('2026-01-01 08:00:00', INTERVAL id DAY),
    description = CASE
        WHEN id BETWEEN 11 AND 16 THEN '<h3>Quang học sắc nét cho mọi khung hình</h3><p>Ống kính có thiết kế quang học hiện đại, lấy nét nhanh và vận hành êm. Lớp phủ đa lớp giúp kiểm soát lóe sáng, giữ màu sắc trong trẻo trong nhiều điều kiện ánh sáng.</p><ul><li>Độ nét cao từ tâm đến rìa ảnh</li><li>Lấy nét nhanh, yên tĩnh</li><li>Thiết kế bền bỉ, dễ mang theo</li></ul>'
        WHEN id IN (9,10,23) THEN '<h3>Sẵn sàng cho mọi hành trình</h3><p>Action camera nhỏ gọn với khả năng quay video độ phân giải cao, chống rung hiệu quả và góc nhìn rộng. Phù hợp du lịch, thể thao và nội dung sáng tạo hằng ngày.</p><ul><li>Video chi tiết, màu sắc sống động</li><li>Chống rung điện tử thông minh</li><li>Kết nối nhanh với điện thoại</li></ul>'
        WHEN id BETWEEN 17 AND 22 THEN '<h3>Vững vàng trong từng cú máy</h3><p>Chân máy tối ưu cho người làm nội dung và nhiếp ảnh gia thường xuyên di chuyển. Kết cấu chắc chắn, thao tác nhanh và hỗ trợ căn chỉnh chính xác.</p><ul><li>Khung nhẹ, chịu tải tốt</li><li>Khóa chân thao tác nhanh</li><li>Đầu bi xoay linh hoạt</li></ul>'
        ELSE '<h3>Công cụ sáng tạo đáng tin cậy</h3><p>Máy ảnh mang đến chất lượng hình ảnh giàu chi tiết, lấy nét nhanh và cách điều khiển trực quan. Thiết kế cân bằng giữa hiệu năng, tính cơ động và trải nghiệm sử dụng.</p><ul><li>Cảm biến độ phân giải cao</li><li>Lấy nét theo chủ thể thông minh</li><li>Kết nối không dây và quay video chất lượng cao</li></ul>'
    END,
    star = CASE WHEN star IS NULL OR star < 4 THEN 4.8 ELSE star END,
    featured = CASE WHEN id IN (2,5,13,17,23,24,25) THEN 1 ELSE 0 END
WHERE id BETWEEN 2 AND 25;

UPDATE image_item SET name = CASE
    WHEN product_id IN (9,10,23) THEN 'camera-action.png'
    WHEN product_id BETWEEN 11 AND 16 THEN 'camera-lens.png'
    WHEN product_id BETWEEN 17 AND 22 THEN 'camera-tripod.png'
    ELSE 'camera-mirrorless.png'
END
WHERE product_id BETWEEN 2 AND 25;

