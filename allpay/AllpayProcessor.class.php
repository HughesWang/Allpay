<?php

	require_once CORE_PATH.'/Validator.php';

	require_once ALLPAY_PATH.'/order/AllpayOrderHandler.class.php';

	class AllpayProcessor {
		const MODE_TEST = 'test';
		const MODE_ONLINE = 'online';
		private $store_id = null; // same as MerchantID
		private $aio_hash_key = null;
		private $aio_hash_iv = null;
		private $mode = AllpayProcessor::MODE_TEST;
		private $_order = null;
		private $settings = array(
				AllpayProcessor::MODE_TEST => array(
						'order_url' => 'http://payment-stage.allpay.com.tw/Cashier',
				),
				AllpayProcessor::MODE_ONLINE => array(
						'order_url' => 'https://payment.allpay.com.tw/Cashier',
				)
		);
		public function __construct($config = array()) {
			$config = (object) $config;
			$this->setStore($config->store)
				->setHashKey($config->hash_key)
				->setHashIV($config->hash_iv);
			return $this;
		}
		public function setStore($store_id = null) {
			if (is_string($store_id) and Validator::maxLength($store_id, 10)) {
				$this->store_id = $store_id;
			}
			return $this;
		}
		public function setHashKey($hash_key = null) {
			if (is_string($hash_key)) {
				$this->aio_hash_key = $hash_key;
			}
			return $this;
		}
		public function setHashIV($hash_iv = null) {
			if (is_string($hash_iv)) {
				$this->aio_hash_iv = $hash_iv;
			}
			return $this;
		}
		public function setMode($mode = null) {
			if (is_string($mode) and in_array($mode, array(self::MODE_TEST, self::MODE_ONLINE))) {
				$this->mode = $mode;
			}
			return $this;
		}
		protected function getStore() {
			return $this->store_id;
		}
		public function getInstance($type = null) {
			$instance = null;
			$config = $this->settings[$this->mode];
			$major_settings = array(
					'MerchantID' => $this->store_id,
					'HashKey' => $this->aio_hash_key,
					'HashIV' => $this->aio_hash_iv
			);
			$config = array_merge($config, $major_settings);

			switch ($type) {
				case 'order':
					if (null === $this->_order) {
						$this->_order = new AllpayOrderHandler();
						$this->_order->setConfig($config);
					}
					$instance = $this->_order;
					break;
			}
			if (null === $instance) {
				throw new Exception(sprintf('Can not find %s instance', $type));
			}
			return $instance;
		}
	}	