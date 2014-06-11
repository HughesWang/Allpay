<?php

	require_once ALLPAY_PATH.'/AllpayObject.class.php';

	abstract class AllpayPaymentMethod extends AllpayObject {
		abstract public function validate();
	}	