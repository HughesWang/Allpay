<?php

	require_once ALLPAY_PATH.'/payment_method/AllpayPaymentMethod.class.php';

	/**
	 * PeriodAmount			int				每次授權金額					N
	 *										Note : 當此參數有設定值時
	 *										歐付寶會認定此次要以每次授權金額
	 *										(PeriodAmount)所設定的金額做授權
	 *										則交易金額(TotalAmount)參數所設定的值
	 *										會被換成此參數的值,表示此筆交易要定期定額做扣款。
	 *										(當此參數有設定金額時,請把 TotalAmount
	 *										也設定成跟 PeriodAmount 一樣)
	 * PeriodType			string		1	週期種類						N
	 *										Note : ※當使用定期定期時,此參數必須要設定
	 *										當設定 D 時,表示以天為週期。
	 *										當設為 M 時,表示以月為週期。
	 *										當設為 Y 時,表示以年為週期。
	 * Frequency			int				執行頻率						N
	 *										Note : ※當使用定期定期時,此參數必須要設定
	 *										此參數用來定義多久要執行一次。
	 *										至少要大於等於 1 次以上。
	 *										當 PeriodType 設為 D 時,最多可設 365 次。
	 *										當 PeriodType 設為 M 時,最多可設 12 次。
	 *										當 PeriodType 設為 Y 時,最多可設 1 次。
	 * ExecTimes			int				執行次數						N
	 *										Note : ※當使用定期定期時,此參數必須要設定
	 *										總共要執行幾次。
	 *										至少要大於 1 次以上。
	 *										當 PeriodType 設為 D 時,最多可設 999 次。
	 *										當 PeriodType 設為 M 時,最多可設 99 次。
	 *										當 PeriodType 設為 Y 時,最多可設 9 次。
	 *	Ex1:當信用卡定期定額扣款為每個月扣 1 次 500 元,總共要扣 12 次時
	 *		PeriodAmount	請帶 500
	 *		PeriodType		請帶 M
	 *		Frequency		請帶 1
	 *		ExecTime		請帶 12
	 *	Ex2:當信用卡定期定額扣款為從 6000 元的交易金額中去固定扣款,每個月扣 1 次,總共要扣 12 次時
	 *		TotalAmount(交易金額)		請帶 6000
	 *		PeriodType				請帶 M
	 *		Frequency				請帶 1
	 *		ExecTime				請帶 12。
	 * 
	 * PeriodReturnURL		string		200	定期定額的執行結果回應 URL		N
	 *										若交易是信用卡定期定額的方式,則每次執行
	 *										授權完,會將授權結果回傳到這個設定的 URL。
	 * 
	 * **** 當訂單是使用信用卡定期定額的交易時,在每次授權成功後,			****
	 * **** 以下參數會以 Server POST 方式傳送至						****
	 * **** 您設定的 PeriodReturnURL,請於收到訊息後,回應已接收訊息。	****
	 */
	class AllpayCreditInstallment extends AllpayPaymentMethod {
		const BY_YEAR = 'Y';
		const BY_MONTH = 'M';
		const BY_DAY = 'D';
		public $PeriodAmount = 0;
		public $PeriodType = AllpayCreditInstallment::BY_MONTH;
		public $Frequency = 1;
		public $ExecTimes = 1;
		public $PeriodReturnURL = null;
		public function __construct($object = null) {
			parent::__construct($object);
		}
		public function validate() {
			$this->_valided = !$this->hasErrors();
			return $this;
		}
	}	