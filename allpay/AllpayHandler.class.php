<?php

	require_once CORE_PATH.'/IHandler.class.php';
	
	abstract class AllpayHandler implements IHandler {
		const COMMAND_ORDER_CREATE = 'AioCheckOut';
		protected $prepared = false;
		protected $config = array();
		public function hasErrors() {
			$errors = self::getErrors();
			return !empty($errors);
		}
		public function isPrepared() {
			return $this->prepared;
		}
		public function setConfig($config = null) {
			if (null !== $config and !empty($config)) {
				if (is_array($config)) {
					$this->config = json_decode(json_encode($config));
				} elseif (is_object($config)) {
					$this->config = $config;
				}
			}
			return $this;
		}
		abstract public function prepare();
	}	