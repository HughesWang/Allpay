<?php

	require_once ALLPAY_PATH.'/AllpayException.class.php';
	require_once ALLPAY_PATH.'/payment_method/alipay/AllpayAlipay.class.php';
	require_once ALLPAY_PATH.'/payment_method/atm/AllpayAtm.class.php';
	require_once ALLPAY_PATH.'/payment_method/barcode/AllpayBarcode.class.php';
	require_once ALLPAY_PATH.'/payment_method/credit/AllpayCredit.class.php';
	require_once ALLPAY_PATH.'/payment_method/cvs/AllpayCvs.class.php';
	require_once ALLPAY_PATH.'/payment_method/tenpay/AllpayTenpay.class.php';
	require_once ALLPAY_PATH.'/payment_method/topupused/AllpayTopupused.class.php';
	require_once ALLPAY_PATH.'/payment_method/webatm/AllpayWebatm.class.php';
//	require_once ALLPAY_PATH.'/payment_method/all/AllpayAll.class.php';

	interface AllpayPayment {
		const CREDIT = 'Credit';   //	信用卡
		const WEB_ATM = 'WebATM';   //	網路ATM
		const ATM = 'ATM';  //	自動櫃員機
		const CVS = 'CVS';  //	超商代碼
		const BARCODE = 'BARCODE';   //	超商代碼
		const ALIPAY = 'Alipay';   //	支付寶
		const TENPAY = 'Tenpay';   //	財付通
		const TOP_UP_USED = 'TopUpUsed'; //	儲值消費
		const ALL = 'ALL'; //	不指定付款方式
	}	