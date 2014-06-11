<?php

	require_once CORE_PATH.'/Http.class.php';
	require_once CORE_PATH.'/view/ApiView.class.php';
	require_once CORE_PATH.'/connector/ApiConnector.class.php';

	require_once ALLPAY_PATH.'/AllpayHandler.class.php';

	class AllpayOrderHandler extends AllpayHandler {
		private $_orders = array();
		public function addOrder(AllpayOrderObject $order) {
			$this->_orders[$order->MerchantTradeNo] = $order;
			return $this;
		}
		public function getOrders() {
			return $this->_orders;
		}
		public function removeOrder(AllpayOrderObject $order) {
			if (isset($this->_orders[$order->MerchantTradeNo])) {
				unset($this->_orders[$order->MerchantTradeNo]);
			}
			return $this;
		}
		public function prepare() {
			if (empty($this->_orders)) {
				throw new Exception('You do not have any order to be prepared.');
				return $this;
			}
			$errors = array();
			foreach ((array) $this->getOrders() as $order) {
				if (!$order->isValided()) {
					$order->validate();
				}
				if ($order->hasErrors()) {
					$errors[$order->MerchantTradeNo] = $order->getErrors();
				}
			}
			$this->prepared = empty($errors);
			return $this;
		}
		public function create() {
			try {
				if (!$this->isPrepared()) {
					throw new Exception('Before create you need to be proccessing prepare function first.');
				}

				$config = new stdClass();
				$config->request_type = Http::POST;
				$config->action = AllpayHandler::COMMAND_ORDER_CREATE;
				$config->api_url = $this->config->order_url;
				$config->data_type = ApiView::STRING_QUERY;

				$connector = ApiConnector::getInstance('redirect')->setConfig($config);
				$response = array();

				$view = new ApiView();
				$responses = array();

				foreach ((array) $this->getOrders() as $order) {
					$output = $order->dump(true);
					$responses[$order->MerchantTradeNo] = $connector->setData($order->dump(true))->prepare()->request();
				}
				/*
				 * for single response
				 * 
				 * if want to use multiple then
				 * use array $responses will be fine.
				 */

				foreach ($responses as $use_single_not_multiple) {
					$response = $use_single_not_multiple;
					$response->result = htmlspecialchars($response->result);
				}
			} catch (Exception $e) {
				$response = new stdClass();
				$response->status = false;
				$response->message = $e->getMessage();
				//Hughes($this);
			}
			return $response;
		}
		public function confirm($confirm_code = null, $confirm_message = null) {
			$output = array(0, 'None');
			if (in_array(intval($confirm_code), array(0, 1)) and $confirm_message !== null) {
				$output = array($confirm_code, $confirm_message);
			}
			header('Content-Type: text/html;charset=utf-8');
			echo implode('|', $output);
		}
	}	