<?php

	require_once ALLPAY_PATH.'/AllpayPayment.class.php';
	require_once ALLPAY_PATH.'/order/AllpayAbstractOrder.class.php';

	/**
	 * allpay order basic object
	 * 
	 * MerchantID			string	10	廠商編號(由 AllPay 提供)	Y
	 * MerchantTradeNo		string	20	廠商交易編號				Y
	 * 									Note : 廠商交易編號不可重覆
	 * MerchantTradeDate	string	20	廠商交易時間				Y
	 * 									Note : Y/m/d H:i:s
	 * PaymentType			string	20	交易類型					Y
	 * 									Note : 目前帶 "aio"		Y
	 * TotalAmount			int			交易金額					Y
	 * TradeDesc			string	200	交易描述					Y
	 * ItemName				string	200	商品名稱					Y
	 * 									Note : 如果商品名稱有多筆			
	 * 									需在金流選擇頁一行一行顯示商品名稱的話
	 * 									商品名稱請以井號分隔 (#)
	 * 									example : 尺 20 元 X2#刀 60 元 X1
	 * ReturnURL			string	200	回傳網址					Y
	 * 									Note : 當消費者付款完成後
	 * 									會將付款結果回傳到該網址。
	 * ChoosePayment		string	20	選擇預設付款方式			Y
	 * 									Note : 
	 * 									Credit :信用卡
	 * 									WebATM : 網路ATM
	 * 									ATM : 自動櫃員機
	 * 									CVS : 超商代碼
	 * 									BARCODE : 超商條碼
	 * 									Alipay : 支付寶
	 * 									Tenpay : 財付通
	 * 									TopUpUsed : 儲值消費
	 * 									ALL : 不指定付款方式
	 * 										  由歐付寶顯示付款方式選擇頁面
	 * 										  當 DeviceSource 為 M 時
	 * 										  ChoosePayment 請帶 ALL 給歐付寶
	 * CheckMacValue		string		檢查碼					Y
	 * ClientBackURL		string	200	Client 端回傳網址			N
	 * 									Note : 此網址為付款完成後
	 * 									銀行將頁面導回到歐付寶時
	 * 									歐付寶會顯示付款完成頁
	 * 									該頁面上會有[回到廠商]的按鈕
	 * 									會員點選按鈕後,會將頁面導回到此設定的網址
	 * ItemURL				string	200	商品銷售網址				N
	 * Remark				string	100	備註欄位(目前都請放空白)		N
	 * ChooseSubPayment		string	20	選擇預設付款子項目			N
	 * 									若正確設定此欄位
	 * 									使用者則無法看見金流選擇頁
	 * 									直接使用設定的付款方式
	 * 									但信用卡(Credit)與儲值消費(TopUpUsed)無此功能
	 * 									例如:ChoosePayment 設定 WebATM, ChooseSubPayment 設定 TAISHIN
	 * 									此次交易就會以台新銀行的網路ATM付款
	 * OrderResultURL		string	200	Client 端回傳付款結果網址	N
	 * 									Note : 此網址為付款完成後
	 * 									銀行將頁面導回到歐付寶時
	 * 									會將頁面導回到此設定的網址
	 * 									並帶回付款結果的參數
	 * 									沒帶此參數則會顯示歐付寶的顯示付款完成頁
	 * 									Note 2 : 有些銀行的 WebATM 在交易成功後
	 * 									會停留在銀行的頁面,並不會導回給歐付寶
	 * 									所以歐付寶也不會將頁面導回到 OrderResultURL 的頁面
	 * NeedExtraPaidInfo	string	1	是否需要額外的付款資訊		N
	 * 									Note : 設定付款完成通知及訂單查詢的回覆資料
	 * 									是否需要額外的付款資訊
	 * 									預設為 N 表示不回傳額外資料
	 * 									設定為 Y 表示要回傳額外資料。
	 * DeviceSource			string	10	裝置來源					N
	 * 									Note : 此參數會因為設定的值不同
	 * 									而顯示不同 layout 的付款方式選擇頁面
	 * 									參數值如下: 
	 * 									P:桌機版頁面(此為預設值)
	 * 									M:行動裝置版頁面, 手機 APP 付款時,請帶此參數值 且 ChoosePayment 請帶 ALL 給歐付寶
	 */
	interface DeviceSouce {
		const PC = 'P';
		const MOBILE = 'M';
	}

	class AllpayOrderObject extends AllpayAbstractOrder {
		public $MerchantID = null;
		public $MerchantTradeNo = null;
		public $MerchantTradeDate = null;
		public $PaymentType = null;
		public $TotalAmount = 0;
		public $TradeDesc = null;
		public $ItemName = null;
		public $ReturnURL = null;
		public $ChoosePayment = null;
		public $ClientBackURL = null;
		public $ItemURL = null;
		public $Remark = null;
		public $ChooseSubPayment = null;
		public $OrderResultURL = null;
		public $NeedExtraPaidInfo = 'Y';
		public $DeviceSource = DeviceSouce::PC;
		protected $CheckMacValue = null;
		protected $HashKey = null;
		protected $HashIV = null;
		private $payment_method = null;
		private $payment_config = null;
		private $payment_class = null;
		private $required_fields = array(
				'MerchantID', 'MerchantTradeNo', 'MerchantTradeDate',
				'PaymentType', 'TotalAmount', 'TradeDesc',
				'ItemName', 'ReturnURL', 'ChoosePayment'
		);
		public function __construct($order = null) {
			parent::__construct($order);
			$this->payment_method = new ReflectionClass('AllpayPayment');
		}
		public function getValues() {
			$values = array(
					get_class($this->payment_class) => $this->payment_class->getValues()
			);
			return array_merge(parent::getValues(), $values);
		}
		public function dump($filter_empty_value = false) {
			if (!$this->isValided()) {
				/* 不該做，該提醒或是顯示錯誤 */
			}
			$output = (object) array_merge(parent::getValues(), $this->payment_class->getValues());
			$output->ItemName = implode('#', $output->ItemName);
			$output = (array) $output;
			if ($filter_empty_value) {
				foreach ($output as $field_name => $field_value) {
					if (!Validator::notEmpty($field_value)) {
						unset($output[$field_name]);
					}
				}
			}
			if ($output['DeviceSource'] == DeviceSouce::MOBILE) {
				$output['ChoosePayment'] = 'ALL';
			}
			$this->CheckMacValue = self::genrate_check_mac_value($output);
			return array_merge($output, array('CheckMacValue' => $this->CheckMacValue));
		}
		public function validate() {
			$reflect = new ReflectionClass($this);
			$properties = $reflect->getProperties(ReflectionProperty::IS_PUBLIC);
			foreach ($properties as $property) {
				if ($property->class !== get_class($this)) {
					continue;
				}
				if (!is_null($this->{$property->name}) || in_array($property->name, (array) $this->required_fields)) {
					$valid_func_name = sprintf('valid_%s', $property->name);
					if (method_exists($this, $valid_func_name)) {
						$result = call_user_func_array(array($this, $valid_func_name), array($this->{$property->name}));
						if (!$result) {
							$this->addError(sprintf('%s had been valided fault.', $property->name));
						}
					} else {
						throw new Exception(sprintf('Error : %s method is not exists.', $valid_func_name));
					}
				}
			}
			if ($sub_payment_valided = $this->payment_class->validate()->hasErrors()) {
				$this->addError(sprintf('sub payment %s had been valided fault.', get_class($this->payment_class)));
			}
			$this->_valided = (!$this->hasErrors() and !$sub_payment_valided);
			return $this;
		}
		private function genrate_check_mac_value(Array $order_info) {
			ksort($order_info);
			$output_query = http_build_query($order_info);
			$output_query = sprintf('HashKey=%s&%s&HashIV=%s', $this->HashKey, $output_query, $this->HashIV);
			$output_query = urlencode(urldecode($output_query));
			$output_query = strtolower($output_query);
			$check_mac_value = md5($output_query);
			return $check_mac_value;
		}
		public function setHashKey($hash_key = null) {
			if (is_string($hash_key)) {
				$this->HashKey = $hash_key;
			}
			return $this;
		}
		public function setHashIV($hash_iv = null) {
			if (is_string($hash_iv)) {
				$this->HashIV = $hash_iv;
			}
			return $this;
		}
		public function setPaymentConfig($config = array()) {
			if (!empty($config)) {
				$this->payment_config = $config;
			}
			return $this;
		}
		private function valid_MerchantID($check) {
			return (boolean) (is_string($check) and Validator::maxLength($check, 10));
		}
		private function valid_MerchantTradeNo($check) {
			/* @todo 如果歐付寶有提供訂單編號查詢再補上不可以重覆訂單編號 */
			return (boolean) (is_string($check) and Validator::maxLength($check, 20));
		}
		private function valid_MerchantTradeDate($check) {
			return (boolean) Validator::datetime($check, 'Y/m/d H:i:s');
		}
		private function valid_PaymentType($check) {
			/* 目前只有 aio */
			return (boolean) Validator::inList($check, array('aio'));
		}
		private function valid_TotalAmount($check) {
			return (boolean) (Validator::numeric($check) and $check >= 0);
		}
		private function valid_TradeDesc($check) {
			return (boolean) (is_string($check) and Validator::maxLength($check, 200));
		}
		private function valid_ItemName($check) {
			return (boolean) (is_array($check) and Validator::maxLength(implode('#', $check), 200));
		}
		private function valid_ReturnURL($check) {
			return (boolean) Validator::url($check);
		}
		private function valid_ChoosePayment($check) {
			return (boolean) Validator::inList($check, $this->payment_method->getConstants());
		}
		private function valid_ClientBackURL($check) {
			return (boolean) Validator::url($check);
		}
		private function valid_ItemURL($check) {
			return (boolean) Validator::url($check);
		}
		private function valid_Remark($check) {
			return (boolean) Validator::maxLength($check, 100);
		}
		private function valid_ChooseSubPayment($check) {
			$interface_name = nameToUpper(strtolower($this->ChoosePayment));
			$interface_path = sprintf(ALLPAY_PATH.'/payment_method/%s/%s.interface.php', strtolower($this->ChoosePayment), $interface_name);
			$class_name = sprintf(
				'Allpay%s%s', nameToUpper(strtolower($this->ChoosePayment)), nameToUpper(strtolower($check))
			);
			$class_path = sprintf(ALLPAY_PATH.'/payment_method/%s/%s.class.php', strtolower($this->ChoosePayment), $class_name);
			$result = (boolean) (
				file_exists($interface_path) and
				interface_exists($interface_name) and
				file_exists($class_path)
				);
			if ($result and !class_exists($class_name)) {
				require_once $class_path;
			}
			try {
				$this->payment_class = new $class_name($this->payment_config);
			} catch (AllpayException $e) {
				$result = false;
				$this->addError($e->getMessage());
			}
			return $result;
		}
		private function valid_OrderResultURL($check) {
			return (boolean) Validator::url($check);
		}
		private function valid_NeedExtraPaidInfo($check) {
			return (boolean) Validator::inList(strtoupper($check), array('N', 'Y'));
		}
		private function valid_DeviceSource($check) {
			$conds = array();
			if (interface_exists('DeviceSouce')) {
				$reflect = new ReflectionClass('DeviceSouce');
				$conds = $reflect->getConstants();
			}
			return (boolean) Validator::inList(strtoupper($check), $conds);
		}
	}	