<?php

	error_reporting(E_ALL);

	date_default_timezone_set('Asia/Taipei');

	define('PHT_BASE_PATH', getcwd());

	define('CORE_PATH', PHT_BASE_PATH.'/core');

	define('ARCRMA_PATH', PHT_BASE_PATH.'/arcrma');

	define('ALLPAY_PATH', PHT_BASE_PATH.'/allpay');
	
	require_once PHT_BASE_PATH.'/Functions.php';

	require_once ARCRMA_PATH.'/test/ArcrmaUnitTest.class.php';

	require_once ALLPAY_PATH.'/test/AllpayUnitTest.class.php';

	try {
		/*
		$tester = new AllpayUnitTest();
		$tester->order('create');
		*/
	} catch (Exception $e) {
		$report = array(
				'File' => $e->getFile(),
				'Line' => $e->getLine(),
				'Code' => $e->getCode(),
				'Message' => $e->getMessage()
		);
	}

	