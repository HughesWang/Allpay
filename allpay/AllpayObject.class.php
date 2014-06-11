<?php

	require_once CORE_PATH.'/Object.class.php';

	class AllpayObject extends Object {
		protected $_valided = false;
		public function __construct($object = null) {
			parent::__construct();
			if ($object !== null) {
				$reflect = new ReflectionClass($this);
				$properties = $reflect->getProperties(ReflectionProperty::IS_PUBLIC);
				$allow_fields = array();
				foreach ($properties as $property) {
					$allow_fields[] = $property->getName();
				}
				foreach ((array) $object as $index => $value) {
					if (in_array($index, $allow_fields)) {
						if (is_string($value) and ($value == 'true' or $value == 'false')) {
							$value = $value == 'true';
						}
						$this->$index = $value;
					}
				}
			}
		}
		final public function isValided() {
			return (boolean) $this->_valided;
		}
		final public function setData(array $params = array()) {
			if (is_array($params) and !empty($params)) {
				foreach ($params as $index => $value) {
					if ($value === null or $value === '') {
						continue;
					}
					$this->{$index} = $value;
				}
			}
			return $this;
		}
		final public function getValue($field_name, $extra_var = false) {
			$value = null;
			if (isset($this->$field_name)) {
				$value = $this->$field_name;
			}
			$extra_vars = array();
			if ($extra_var and in_array($field_name, $extra_vars)) {
				switch ($field_name) {
					default:
						break;
				}
			}
			return $value;
		}
		public function getValues() {
			$reflect = new ReflectionClass($this);
			$public_properties = $reflect->getProperties(ReflectionProperty::IS_PUBLIC);
			$output_vars = array();
			foreach ($public_properties as $property) {
				if (!is_subclass_of($property->class, __CLASS__)) {
					continue;
				}
				$field_name = $property->getName();
				$output_vars[$field_name] = $this->getValue($field_name);
			}
			return $output_vars;
		}
	}	