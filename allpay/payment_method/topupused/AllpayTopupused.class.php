<?php

	require_once ALLPAY_PATH.'/payment_method/AllpayPaymentMethod.class.php';

	class AllpayTopupused extends AllpayPaymentMethod {
		public function __construct($object = null) {
			parent::__construct($object);
		}
		public function validate() {
			$this->_valided = !$this->hasErrors();
			return $this;
		}
	}	