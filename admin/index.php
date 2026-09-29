<?php 
	ob_start();
	require "../config.php";
	require "../connectDB.php";
	require_once "../vendor/autoload.php";
	include "../bootstrap.php";
	session_id() || session_start();
	ob_start('protect_html');
	$c = input_text('c', $_GET, 'dashboard');
	$a = input_text('a', $_GET, 'list');
	$routes = [
		'dashboard'=>['list'], 'auth'=>['login','logout'],
		'article'=>['list','add','edit','save','update','delete'],
		'product'=>['list','add','edit','save','update','delete','findBarcode'],
		'brand'=>['list','add','edit','save','update','delete','deletes','checkDelete','checkDeletes'],
		'category'=>['list','add','edit','save','update','delete','deletes','checkDelete'],
		'customer'=>['list','add','edit','save','update','delete','deletes'],
		'staff'=>['list','add','edit','save','update','active','disable','activeOrDisableMulti'],
		'order'=>['list','add','edit','update','save','delete','confirm','ajaxGetShippingInfoDefault'],
		'comment'=>['list','detail','delete','deletes'],
		'imageItem'=>['list','detail','save','delete','deletes'],
		'shippingfee'=>['list','edit','update'], 'status'=>['list','edit','update'],
		'permission'=>['listRole','editRole','updateRole','addRole','saveRole','checkDeleteRole','deleteRole','listAction','listRoleAction','updateRoleAction'],
		'newsletter'=>['list','sendEmail','send','delete'], 'address'=>['getProvinces','getDistricts','getWards','getShippingFee']
	];
	if (!isset($routes[$c]) || !in_array($a, $routes[$c], true)) { http_response_code(404); exit('Không tìm thấy trang.'); }
	include "load.php";
	session_id() || session_start();
	if (!($c == "auth" && $a == "login")) {
		include "checkLogin.php";
		//Đã login
	}
	//Check ACL
	if (!empty($_SESSION["username"])) {
		$aclService = new AclService();
		$staffRepository = new StaffRepository();
		$staff = $staffRepository->findUsername($_SESSION["username"]);
		if (!$staff || !$staff->getIsActive()) { unset($_SESSION['username']); header('Location: login.php'); exit; }
		if (!$aclService->hasPermission($staff, $c, $a)) {
			$_SESSION["error"] = $aclService->getMessage();
			header("location: index.php");
			exit;
		}
	}
	// var_dump($_SESSION);
	dispatch_action($c, $a, $routes);
