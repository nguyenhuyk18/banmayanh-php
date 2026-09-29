SET NAMES utf8mb4;

INSERT INTO article (title, slug, summary, content, is_published, created_at)
SELECT
  '7 bước chọn máy ảnh đầu tiên phù hợp với bạn',
  '7-buoc-chon-may-anh-dau-tien',
  'Từ nhu cầu chụp, ngân sách đến hệ ống kính: đây là checklist ngắn gọn giúp người mới chọn đúng bộ máy ảnh.',
  '1. Xác định thể loại bạn chụp nhiều nhất: du lịch, chân dung, sản phẩm hay video.\n\n2. Đặt ngân sách cho cả thân máy, ống kính, thẻ nhớ và pin dự phòng.\n\n3. Ưu tiên cảm giác cầm nắm và cách bố trí nút bấm; một chiếc máy thoải mái sẽ được mang theo nhiều hơn.\n\n4. Kiểm tra hệ ống kính và chi phí nâng cấp về sau.\n\n5. Với video, hãy quan tâm đến lấy nét, chống rung, cổng micro và khả năng tản nhiệt.\n\n6. Đừng chỉ so sánh megapixel; kích thước cảm biến, ống kính và khả năng xử lý cũng rất quan trọng.\n\n7. Trải nghiệm thực tế hoặc nhờ Lensora tư vấn trước khi quyết định.',
  1,
  '2026-09-29 09:00:00'
WHERE NOT EXISTS (SELECT 1 FROM article WHERE slug = '7-buoc-chon-may-anh-dau-tien');
