<?php

	require_once ALLPAY_PATH.'/payment_method/AllpayPaymentMethod.class.php';

	/**
	 * CreditInstallment	int			刷卡分期期數					N
	 * 									Note : 若會員選擇信用卡付款時
	 * 									商家是否願意提供分期
	 * 									如果願意,請帶可分期期數。
	 * 									如不提供分期,請帶 0
	 * InstallmentAmount	int			使用刷卡分期的付款金額			N
	 * 									Note : 如果使用刷卡分期
	 * 									的消費金額會大於不分期的消費金額時
	 * 									請帶使用分期的消費金額。
	 * 									若不使用信用卡分期時,請帶 0
	 * Redeem				string	1	信用卡是否使用紅利折抵			N
	 * 									設為 Y 時,當歐付寶會員選擇		
	 * 									信用卡付款時,會進入紅利折抵的交易流程。
	 * UnionPay				int		1	是否為銀聯卡					N
	 *									否 - 請帶 0 (預設值)
	 *									是 - 請帶 1
	 */
	class AllpayCredit extends AllpayPaymentMethod {
		const FEEDBACK_USED = 'Y';
		const FEEDBACK_UNUSED = 'N';
		public $CreditInstallment = 0;
		public $InstallmentAmount = 0;
		public $Redeem = AllpayCredit::FEEDBACK_UNUSED;
		public $UnionPay = 0;
		public function __construct($object = null) {
			parent::__construct($object);
		}
		public function validate() {
			$this->_valided = !$this->hasErrors();
			return $this;
		}
		public function getValues() {
			$values = parent::getValues();
			if (strtoupper($values['Redeem']) != self::FEEDBACK_USED) {
				unset($values['Redeem']);
			}
			return $values;
		}
	}	