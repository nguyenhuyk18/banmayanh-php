<?php
class ImageService {
    function saveUpload($upload) {
        if (!is_array($upload) || ($upload['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK || !is_uploaded_file($upload['tmp_name'] ?? '')) {
            throw new InvalidArgumentException('Vui lòng chọn một ảnh hợp lệ.');
        }
        if (($upload['size'] ?? 0) > 5 * 1024 * 1024) throw new InvalidArgumentException('Ảnh không được vượt quá 5 MB.');
        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($upload['tmp_name']);
        $extensions = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp','image/gif'=>'gif'];
        if (!isset($extensions[$mime]) || !getimagesize($upload['tmp_name'])) throw new InvalidArgumentException('Chỉ chấp nhận ảnh JPG, PNG, WebP hoặc GIF.');
        $name = bin2hex(random_bytes(16)) . '.' . $extensions[$mime];
        if (!move_uploaded_file($upload['tmp_name'], ABSPATH . 'upload/' . $name)) throw new RuntimeException('Không thể lưu ảnh tải lên.');
        return $name;
    }
    function getCorrectImage($filename) {
        $base = preg_replace('/[^A-Za-z0-9._-]/', '_', basename($filename));
        return bin2hex(random_bytes(8)) . '_' . $base;
    }
}
