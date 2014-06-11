<?php

	require_once CORE_PATH.'/UnitTest.class.php';

	require_once ALLPAY_PATH.'/AllpayProcessor.class.php';
	require_once ALLPAY_PATH.'/AllpayPayment.class.php';
	require_once ALLPAY_PATH.'/order/AllpayOrderObject.class.php';
	require_once ALLPAY_PATH.'/payment_method/credit/Credit.interface.php';

	class AllpayUnitTest extends UnitTest {
		const MODE_TEST = 'test';
		const MODE_ONLINE = 'online';
		const CURRENT_MODE = AllpayUnitTest::MODE_TEST;
		private $config = array(
				AllpayUnitTest::MODE_TEST => array(
						'store' => '2000132',
						'aio_hash_key' => '5294y06JbISpM5x9',
						'aio_hash_iv' => 'v77hoKGq4kWxNNIS',
						'order_url' => 'http://payment-stage.allpay.com.tw/Cashier'
				),
				AllpayUnitTest::MODE_ONLINE => array(
						'store' => null,
						'aio_hash_key' => null,
						'aio_hash_iv' => null,
						'order_url' => 'https://payment.allpay.com.tw/Cashier'
				)
		);
		public function order($unit_name = null) {
			self::execute($unit_name, $prefix = 'Order_');
		}
		private function Unit_Order_Create() {
			$config = (object) $this->config[AllpayUnitTest::CURRENT_MODE];
			$ao_obj = new AllpayOrderObject(array(
					'MerchantID' => $config->store, // 商家代號
					'MerchantTradeNo' => sprintf('O%014s%03d', date('YmdHis'), 1), // 訂單編號
					'MerchantTradeDate' => date('Y/m/d H:i:s'),
					'PaymentType' => 'aio', // 文件寫入先帶死的
					'TotalAmount' => 3000, // 訂單金額
					'TradeDesc' => '測試交易描述', // 交易描述
					'ItemName' => array('尺 20 元 X2', '刀 60 元 X1'), // 商品名稱
					'ReturnURL' => 'http://payment.pht-studio.com/confirm/', // 當消費者付款完成後會將付款結果回傳到該網址
					'ChoosePayment' => AllpayPayment::CREDIT,
					'ClientBackURL' => 'http://www.pht-studio.com', // 此網址為付款完成後,銀行將頁面導回到歐付寶時, 歐付寶會顯示付款完成頁,該頁面上會有[回到廠商]的按鈕
					'ItemURL' => null,
					'Remark' => '',
					'ChooseSubPayment' => Credit::NORMAL
			));

			$ao_obj->setHashIV($config->aio_hash_iv)->setHashKey($config->aio_hash_key)->setPaymentConfig(array(
					'CreditInstallment' => 0,
					'InstallmentAmount' => 0,
					'Redeem' => 'N'
			));

			$processor = new AllpayProcessor(array(
					'store' => $config->store,
					'hash_key' => $config->aio_hash_key,
					'hash_iv' => $config->aio_hash_iv
			));

			self::output(
				$processor->getInstance('order')->addOrder($ao_obj)->prepare()->create(), '新建訂單'
			);
		}
		private function execute($unit_name = null, $prefix = null) {
			if (null === $unit_name) {
				throw new Exception('You should chose your unit test as you want !');
			}
			$func_name = $this->_prefix.$prefix.nameToUpper($unit_name);
			if (method_exists($this, $func_name)) {
				$result = call_user_func_array(array($this, $func_name), array());
			} else {
				throw new Exception(sprintf('Do not have this unit test [ %s ]', $func_name));
			}
		}
		protected function output($content, $title = null) {
			parent::render($content, $title);
		}
	}	