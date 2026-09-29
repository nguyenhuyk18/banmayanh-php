<?php
// Authentication (Xác thực)
// Lưu ý khác với từ Authorization(Phân quyền)
class AuthController {

    function login() {
        // var_dump($_POST);
        // 1. email có tồn tại không ?
        $email = $_POST['email'];
        $customerRepository = new CustomerRepository();
        $customer = $customerRepository->findEmail($email);
        if(empty($customer)) {
            $_SESSION['error'] = "Email $email không tồn tại trong hệ thống. Vui lòng đăng nhập bằng email khác";
            // Trở về trang chủ
            header('Location: ' . app_url(''));
            exit;
        }
        // 2. kiểm tra mật khẩu đúng không
        $password = $_POST['password'];
        // tham số 1 là mật khẩu chưa mã hóa
        // tham số 2 là mật khẩu đã mã hóa
        // Hàm này kiểm ttra mật khẩu chưa mã hóa và mật khẩu đã mã hóa có phải là 1 không
        // nếu đúng trả về true sai trả về false
        if(!password_verify($password, $customer->getPassword())) {
            $_SESSION['error'] = 'Sai mật khẩu';

            header('Location: ' . app_url(''));
            exit;
        }

        if($customer->getIsActive() == 0) {
            $_SESSION['error'] = 'Tài khoản chưa được kích hoạt. Vui lòng vào email để kích hoạt tài khoản';

            header('Location: ' . app_url(''));
            exit;
        }

        $_SESSION['name'] = $customer->getName();
        $_SESSION['email'] = $email;
        session_regenerate_id(true);
        header('Location: ' . app_url('index.php?c=customer&a=show'));
        exit;
    }

    function loginGoogle()
    {
        try {
            $clientID = GOOGLE_CLIENT_ID;
            $clientSecret = GOOGLE_CLIENT_SECRET;
            $redirectUri = get_domain() . $_SERVER['PHP_SELF'] . "?c=auth&a=loginGoogle";

            // create Client Request to access Google API
            $client = new Google_Client();
            $client->setClientId($clientID);
            $client->setClientSecret($clientSecret);
            $client->setRedirectUri($redirectUri);
            $client->addScope("email");
            $client->addScope("profile");

            if (isset($_GET['code'])) {
				$state = input_text('state', $_GET);
				if (!$state || !hash_equals($_SESSION['google_oauth_state'] ?? '', $state)) throw new InvalidArgumentException('Phiên đăng nhập Google không hợp lệ.');
				unset($_SESSION['google_oauth_state']);
                $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
				if (empty($token['access_token'])) throw new RuntimeException('Google login token missing');

                $client->setAccessToken($token['access_token']);

                // get profile info
                $google_oauth = new Google_Service_Oauth2($client);
                $google_account_info = $google_oauth->userinfo->get();
				if (!$google_account_info->verifiedEmail) throw new DomainException('Google email not verified');
                $email =  $google_account_info->email;
                $name =  $google_account_info->name;
                $this->createCustomerBySocial($email, $name, "google");
				session_regenerate_id(true);
                $this->setupLoginEnv($email, $name);
                header("location: index.php");
				exit;
            }
		} catch (Throwable $e) {
			error_log('Google login failed: ' . $e->getMessage());
			$_SESSION['error'] = 'Đăng nhập Google chưa thành công. Vui lòng thử lại.';
			header('Location: ' . app_url());
			exit;
        }
    }

    function createCustomerBySocial($email, $name, $type)
    {
        $customerRepository = new CustomerRepository();
        $customer = $customerRepository->findEmail($email);

        if (empty($customer)) {
            //create new customer
            $data = array(
                "name" => $name,
                "mobile" => "",
                "password" => "",
                "email" => $email,
                "shipping_name" => $name,
                "shipping_mobile" => "",
                "ward_id" => null,
                "housenumber_street" => null,
                "login_by" => $type,
                "is_active" => 1
            );
            $customerRepository->save($data);
        }
    }

    function setupLoginEnv($email, $name, $remember_me = null)
    {
        $_SESSION["email"] = $email;
        $_SESSION["name"] = $name;
    }


    function logout() {
        // Xóa tất cả session và điều hướng về trang chủ
        session_destroy();
        header('Location: ' . app_url(''));
        exit;
    }

}
