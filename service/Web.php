<?php
// Keep the app usable on an old Docker image while it is being rebuilt. The Dockerfile
// installs mbstring; these minimal fallbacks prevent Slugify/article rendering from
// becoming a fatal error if an existing image is missing that extension.
if (!function_exists('mb_strtolower')) {
    function mb_strtolower($value, $encoding = null) { return strtolower($value); }
}
if (!function_exists('mb_strlen')) {
    function mb_strlen($value, $encoding = null) { return strlen($value); }
}
if (!function_exists('mb_substr')) {
    function mb_substr($value, $start, $length = null, $encoding = null) { return $length === null ? substr($value, $start) : substr($value, $start, $length); }
}
function app_url($path = '') {
    return get_base_path() . '/' . ltrim($path, '/');
}

function h($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function input_text($key, $source = null, $default = '') {
    $source = $source ?? $_POST;
    $value = $source[$key] ?? $default;
    if (!is_scalar($value)) {
        throw new InvalidArgumentException('Dữ liệu không hợp lệ.');
    }
    return trim((string) $value);
}

function positive_id($value) {
    if (!is_scalar($value) || !preg_match('/^[0-9]+$/D', (string) $value) || (int) $value < 1) {
        throw new InvalidArgumentException('Mã hoặc số lượng không hợp lệ.');
    }
    return (int) $value;
}

function location_id($value) {
    if (!is_scalar($value) || !preg_match('/^[0-9]{1,10}$/D', (string) $value)) {
        throw new InvalidArgumentException('Mã địa chỉ không hợp lệ.');
    }
    return (string) $value;
}

function require_fields(array $fields, $source = null) {
    foreach ($fields as $field) {
        if (input_text($field, $source) === '') {
            throw new InvalidArgumentException('Vui lòng điền đầy đủ thông tin bắt buộc.');
        }
    }
}

function require_post() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        header('Allow: POST');
        exit('Vui lòng gửi thông tin qua biểu mẫu.');
    }
}

function require_record($record) {
    if (!$record) {
        http_response_code(404);
        exit('Không tìm thấy dữ liệu.');
    }
    return $record;
}

function validate_password($password, $confirmation) {
    if (strlen($password) < 8 || $password !== $confirmation) {
        throw new InvalidArgumentException('Mật khẩu phải có ít nhất 8 ký tự và nhập lại trùng khớp.');
    }
}

function validate_address($wardId, $districtId, $provinceId) {
    $ward = (new WardRepository())->find(location_id($wardId));
    if (!$ward || $ward->getDistrictId() != $districtId) {
        throw new InvalidArgumentException('Phường/xã không thuộc quận/huyện đã chọn.');
    }
    $district = $ward->getDistrict();
    if (!$district || $district->getProvinceId() != $provinceId) {
        throw new InvalidArgumentException('Quận/huyện không thuộc tỉnh/thành đã chọn.');
    }
    return require_record($district->getProvince());
}

function json_response($data) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
}

function csrf_token() {
    if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf_token'];
}

function verify_csrf() {
    $token = $_POST['_csrf'] ?? $_GET['_csrf'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!is_string($token) || !hash_equals(csrf_token(), $token)) {
        http_response_code(403);
        exit('Phiên biểu mẫu đã hết hạn. Vui lòng tải lại trang rồi thử lại.');
    }
}

// Supply CSRF tokens to existing forms and mutation links, including admin tables.
function protect_html($html) {
    if (stripos($html, '<html') === false) return $html;
    $token = csrf_token();
    $html = preg_replace_callback('/<form\b[^>]*>/i', function ($match) use ($token) {
        if (preg_match('/\bmethod\s*=\s*(?:["\']?)get\b/i', $match[0])) return $match[0];
        return $match[0] . '<input type="hidden" name="_csrf" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }, $html);
    $html = preg_replace_callback('/href=([\'\"])([^\'\"]*\ba=(?:delete|deletes|deleteRole|confirm|active|disable|logout)[^\'\"]*)\1/i', function ($match) use ($token) {
        return 'href=' . $match[1] . $match[2] . '&amp;_csrf=' . rawurlencode($token) . $match[1];
    }, $html);
    if (stripos($html, 'meta name="csrf-token"') === false) {
        $html = preg_replace('/<\/head\s*>/i', '<meta name="csrf-token" content="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '"><script>window.CSRF_TOKEN=' . json_encode($token) . ';</script></head>', $html, 1);
    }
    if (stripos($html, 'window.CSRF_TOKEN') === false && stripos($html, 'meta name="csrf-token"') !== false) {
        $html = preg_replace('/<\/head\s*>/i', '<script>window.CSRF_TOKEN=' . json_encode($token) . ';</script></head>', $html, 1);
    }
    return $html;
}

function dispatch_action($controllerName, $action, array $allowed) {
    if (!isset($allowed[$controllerName]) || !in_array($action, $allowed[$controllerName], true)) {
        http_response_code(404);
        exit('Không tìm thấy trang.');
    }
    $mutations = ['login','logout','register','updateAccount','updateShippingDefault','updatePassword','forgotPassword','storeComment','sendEmail','subscribe','order','save','update','delete','deletes','confirm','active','disable','activeOrDisableMulti','saveRole','updateRole','deleteRole','updateRoleAction','send'];
    if ($controllerName === 'cart' && in_array($action, ['add','update','delete'], true)) {
        require_post();
        verify_csrf();
    } elseif (in_array($action, $mutations, true) && !($controllerName === 'newsletter' && $action === 'sendEmail')) {
        if (!in_array($action, ['logout','delete','deletes','deleteRole','confirm','active','disable'], true)) require_post();
        verify_csrf();
    }
    $class = ucfirst($controllerName) . 'Controller';
    (new $class())->$action();
}

set_exception_handler(function (Throwable $error) {
    global $conn;
    if (isset($conn) && $conn instanceof mysqli) {
        try { $conn->rollback(); } catch (Throwable $ignored) {}
    }
    if (ob_get_level()) ob_clean();
    error_log((string) $error);
    $message = 'Không thể xử lý yêu cầu. Vui lòng thử lại.';
    if (filter_var(getenv('APP_DEBUG'), FILTER_VALIDATE_BOOL)) {
        $message .= ' [' . get_class($error) . ': ' . $error->getMessage() . ']';
    }
    $status = 500;
    if ($error instanceof InvalidArgumentException || $error instanceof DomainException) {
        $message = $error->getMessage();
        $status = 422;
    } elseif ($error instanceof mysqli_sql_exception && in_array($error->getCode(), [1062,1451,1452], true)) {
        $message = 'Dữ liệu bị trùng hoặc đang được sử dụng. Vui lòng kiểm tra lại.';
        $status = 409;
    }
    http_response_code($status);
    if (strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest') {
        header('Content-Type: text/plain; charset=utf-8');
        echo $message;
    } else {
        header('Content-Type: text/html; charset=utf-8');
        echo '<!doctype html><html lang="vi"><meta charset="utf-8"><title>Thông báo</title><body><h1>Thông báo</h1><p>' . h($message) . '</p><p><a href="' . h(app_url()) . '">Về trang chủ</a></p></body></html>';
    }
});
