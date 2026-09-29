<?php 
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;
if (empty($_SESSION["username"])) {
	if (!empty($_COOKIE["token"])) {
		//giải mã
		$key = JWT_KEY;
		try {
			$payload = JWT::decode($_COOKIE["token"], new Key($key, 'HS256'));
			$staff = (new StaffRepository())->findUsername($payload->username ?? '');
			if (!$staff || !$staff->getIsActive() || !hash_equals(hash('sha256', $staff->getPassword()), $payload->version ?? '')) throw new RuntimeException('Invalid account');

			$_SESSION["username"] = $payload->username;
			$_SESSION["name"] = $payload->name;
		} catch(Throwable $e) {
			setcookie('token', '', time() - 3600);
			header('Location: login.php');
			exit;
		}
	}
	else {
		header("location:login.php");
		exit;
	}
	
}
