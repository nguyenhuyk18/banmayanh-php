<?php 
use \Firebase\JWT\JWT;
class AuthController {
	function login() {
		require_fields(['username','password']);
		$username = input_text('username');
		$password = input_text('password');
		$staffRepository = new StaffRepository();
		$staff = $staffRepository->findUsername($username);
		if ($staff && (password_verify($password, $staff->getPassword()) || hash_equals($staff->getPassword(), md5($password)))) {
			if (strlen($staff->getPassword()) === 32) {
				$staff->setPassword(password_hash($password, PASSWORD_DEFAULT));
				$staffRepository->update($staff);
			}
			//check active or not
			
			if ($staff->getIsActive()==0) {
				//Login thất bại. Về login.php
				$_SESSION["error"] = "Tài khoản bị vô hiệu hóa. Vui lòng liên hệ người quản trị";
				header("location:login.php");
				exit;
			}
			//Đã login thành công
			$_SESSION["username"] = $username;
			$_SESSION["name"] = $staff->getName();
			session_regenerate_id(true);

			//Lưu thêm trong cookie
			if (!empty($_POST["remember-me"])) {
				$key = JWT_KEY;
				$payload = array(
				    "username" => $username,
				    "name" => $staff->getName(),
				    "version" => hash('sha256', $staff->getPassword())
				);
				$payload['exp'] = time() + 24 * 60 * 60;
				$token = JWT::encode($payload, $key, 'HS256');
				setcookie("token", $token, time()+ 24 * 60 * 60 );
			}
			header("location:index.php");
		}
		else {
			//Login thất bại. Về login.php
			$_SESSION["error"] = "Sai username hoặc password";
			header("location:login.php");
			exit;
		}
	}

	function logout() {
		session_destroy();
		setcookie("token", "", time() - 60);
		header("location:login.php");
		exit;
	}
}
