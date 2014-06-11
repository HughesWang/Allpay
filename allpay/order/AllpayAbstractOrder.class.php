<?php

	require_once ALLPAY_PATH.'/AllpayObject.class.php';
	require_once ALLPAY_PATH.'/AllpayPayment.class.php';

	abstract class AllpayAbstractOrder extends AllpayObject {
		abstract public function validate();
		abstract public function setHashKey($hash_key);
		abstract public function setHashIV($hash_iv);
	}	