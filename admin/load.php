<?php
	$controllerClass = $c === 'shippingfee' ? 'ShippingFee' : ucfirst($c);
	include_once "controller/" . $controllerClass . "Controller.php";
	include_once "service/ImageService.php";
	include_once "service/MailSenderService.php";
	include_once "service/ACLService.php";
	include_once "service/ParamToAclActionService.php";
