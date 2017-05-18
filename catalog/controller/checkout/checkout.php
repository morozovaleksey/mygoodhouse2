<?php
class ControllerCheckoutCheckout extends Controller {
	public function index() {
		function filter($post){
			return htmlspecialchars(trim($post));
		}
		$json = array();
		$this->load->model('localisation/currency');
		$results_currency = $this->model_localisation_currency->getCurrencies();
		$orderNameProduct = $_POST['orderNameProduct'];
		$orderIdProduct = $_POST['orderIdProduct'];
		$orderCount = $_POST['orderCount'];
		if(isset($_POST['orderPriceProductEur'])) {
			$orderPriceProductEur = $_POST['orderPriceProductEur'];
			$orderPriceProductEur = filter($orderPriceProductEur);
		}

		$orderPriceProduct = $_POST['orderPriceProduct'];
		$orderArticulProduct = $_POST['orderArticulProduct'];
		$orderNameCustomer = $_POST['orderNameCustomer'];
		$orderPhone = $_POST['orderPhone'];
		$orderEmail = $_POST['orderEmail'];
		$orderComment = $_POST['orderComment'];


		$orderNameProduct = filter($orderNameProduct);
		$orderIdProduct = filter($orderIdProduct);
		$orderCount = filter($orderCount);
		$orderPriceProduct = filter($orderPriceProduct);




		$orderArticulProduct = filter($orderArticulProduct);
		$orderNameCustomer = filter($orderNameCustomer);
		$orderPhone = filter($orderPhone);
		$orderEmail = filter($orderEmail);
		$orderComment = filter($orderComment);
		if(isset($orderPriceProductEur)) {
			$orderTotals = $this->currency->format($orderPriceProductEur*$orderCount + ($this->config->get('config_tax') ? ($this->config->get('config_tax') * $orderCount) : 0), 'RUB',  $results_currency['RUB']['value']);

		} else {
			$orderTotals = $this->currency->format($orderPriceProduct*$orderCount + ($this->config->get('config_tax') ? ($this->config->get('config_tax') * $orderCount) : 0), 'RUB',1);

		}


		if ($orderCount < 1) {
			$json['error'][] = array('name'=>'orderCount','error'=>'Количество должно быть больше 0');
		}

		if ((utf8_strlen($orderNameCustomer) < 3) || (utf8_strlen($orderNameCustomer) > 25)) {
			$json['error'][] = array('name'=>'orderNameCustomer','error'=>'Имя должно быть от 3 до 25 символов!');

		}

		if ((utf8_strlen($orderPhone) < 1)) {
			$json['error'][] = array('name'=>'orderPhone','error'=>'Телефон не должен быть пустым');

		}

		if (!preg_match("|^[-0-9a-z_\.]+@[-0-9a-z_^\.]+\.[a-z]{2,6}$|i", $orderEmail)) {
			$json['error'][] = array('name' => 'orderEmail', 'error' => 'Не правильно введено поле Email');
		}

		if ((utf8_strlen($orderComment) > 3000)) {
			$json['error'][] = array('name'=>'orderComment','error'=>'Комментарий не должен быть слишком длинным');

		}


		if (!isset($json['error'])) {

			// Validate cart has products and has stock.
			$this->load->model('checkout/order');

			$order_data = array();

			$this->load->model('extension/extension');


			$this->load->language('checkout/checkout');

			$order_data['invoice_prefix'] = $this->config->get('config_invoice_prefix');
			$order_data['store_id'] = $this->config->get('config_store_id');
			$order_data['store_name'] = $this->config->get('config_name');

			if ($order_data['store_id']) {
				$order_data['store_url'] = $this->config->get('config_url');
			} else {
				$order_data['store_url'] = HTTP_SERVER;
			}


			$order_data['customer_id'] = 0;
			$order_data['customer_group_id'] = 1;
			$order_data['firstname'] = $orderNameCustomer;
			$order_data['lastname'] = $orderNameCustomer;
			$order_data['email'] = $orderEmail;
			$order_data['telephone'] = $orderPhone;
			$order_data['fax'] = '';
			$order_data['custom_field'] = '';


			$order_data['payment_firstname'] = $orderNameCustomer;
			$order_data['payment_lastname'] = $orderNameCustomer;
			$order_data['payment_company'] = '';
			$order_data['payment_address_1'] = 'Самовывоз';
			$order_data['payment_address_2'] = '';
			$order_data['payment_city'] = 'Самара';
			$order_data['payment_postcode'] = '';
			$order_data['payment_zone'] = 'Самарская область';
			$order_data['payment_zone_id'] = '2781';
			$order_data['payment_country'] = 'Российская Федерация';
			$order_data['payment_country_id'] = 176;
			$order_data['payment_address_format'] = '';
			$order_data['payment_custom_field'] = (isset($this->session->data['payment_address']['custom_field']) ? $this->session->data['payment_address']['custom_field'] : array());

			if (isset($this->session->data['payment_method']['title'])) {
				$order_data['payment_method'] = $this->session->data['payment_method']['title'];
			} else {
				$order_data['payment_method'] = 'Оплата при получении';
			}

			if (isset($this->session->data['payment_method']['code'])) {
				$order_data['payment_code'] = $this->session->data['payment_method']['code'];
			} else {
				$order_data['payment_code'] = 'cod';
			}


			$order_data['shipping_firstname'] = $orderNameCustomer;
			$order_data['shipping_lastname'] = $orderNameCustomer;
			$order_data['shipping_company'] = '';
			$order_data['shipping_address_1'] = 'Самовывоз';
			$order_data['shipping_address_2'] = '';
			$order_data['shipping_city'] = 'Самара';
			$order_data['shipping_postcode'] = '';
			$order_data['shipping_zone'] = 'Самарская область';
			$order_data['shipping_zone_id'] = '2781';
			$order_data['shipping_country'] = 'Российская Федерация';
			$order_data['shipping_country_id'] = '176';
			$order_data['shipping_address_format'] = '';
			$order_data['shipping_custom_field'] = array();
			$order_data['shipping_method'] = 'Фиксированная стоимость доставки';
			$order_data['shipping_code'] = 'flat.flat';


			$order_data['products'] = array();

			if(isset($orderPriceProductEur)) {
				$order_data['products'][] = array(
					'product_id' => $orderIdProduct,
					'name' => $orderNameProduct,
					'model' => $orderNameProduct,
					'download' => '0',
					'quantity' => $orderCount,
					'subtract' => 1,
					'price' => $orderPriceProductEur,
					'total' => $orderPriceProductEur*$orderCount,
					'tax' => '',
					'reward' => ''
				);
			} else {
				$order_data['products'][] = array(
					'product_id' => $orderIdProduct,
					'name' => $orderNameProduct,
					'model' => $orderNameProduct,
					'download' => '0',
					'quantity' => $orderCount,
					'subtract' => 1,
					'price' => $orderPriceProduct,
					'total' => $orderPriceProduct*$orderCount,
					'tax' => '',
					'reward' => ''
				);
			}



			// Gift Voucher
			$order_data['vouchers'] = array();

			if (!empty($this->session->data['vouchers'])) {
				foreach ($this->session->data['vouchers'] as $voucher) {
					$order_data['vouchers'][] = array(
						'description' => $voucher['description'],
						'code' => token(10),
						'to_name' => $voucher['to_name'],
						'to_email' => $voucher['to_email'],
						'from_name' => $voucher['from_name'],
						'from_email' => $voucher['from_email'],
						'voucher_theme_id' => $voucher['voucher_theme_id'],
						'message' => $voucher['message'],
						'amount' => $voucher['amount']
					);
				}
			}

			$order_data['comment'] = $orderComment;
			if(isset($orderPriceProductEur)) {
				$order_data['total'] = $orderPriceProductEur*$orderCount;
			} else {
				$order_data['total'] = $orderPriceProduct*$orderCount;
			}


			if (isset($this->request->cookie['tracking'])) {
				$order_data['tracking'] = $this->request->cookie['tracking'];

				$subtotal = $this->cart->getSubTotal();

				// Affiliate
				$this->load->model('affiliate/affiliate');

				$affiliate_info = $this->model_affiliate_affiliate->getAffiliateByCode($this->request->cookie['tracking']);

				if ($affiliate_info) {
					$order_data['affiliate_id'] = $affiliate_info['affiliate_id'];
					$order_data['commission'] = ($subtotal / 100) * $affiliate_info['commission'];
				} else {
					$order_data['affiliate_id'] = 0;
					$order_data['commission'] = 0;
				}

				// Marketing
				$this->load->model('checkout/marketing');

				$marketing_info = $this->model_checkout_marketing->getMarketingByCode($this->request->cookie['tracking']);

				if ($marketing_info) {
					$order_data['marketing_id'] = $marketing_info['marketing_id'];
				} else {
					$order_data['marketing_id'] = 0;
				}
			} else {
				$order_data['affiliate_id'] = 0;
				$order_data['commission'] = 0;
				$order_data['marketing_id'] = 0;
				$order_data['tracking'] = '';
			}

			$order_data['language_id'] = $this->config->get('config_language_id');
//			$order_data['currency_id'] = $this->currency->getId();
//			$order_data['currency_code'] = $this->currency->getCode();
//			$order_data['currency_value'] = $this->currency->getValue($this->currency->getCode());

//
			$order_data['currency_id'] = $results_currency['RUB']['currency_id'];
			$order_data['currency_code'] = $results_currency['RUB']['code'];
			if(isset($orderPriceProductEur)) {
				$order_data['currency_value'] = $results_currency['RUB']['value'];
			} else {
				$order_data['currency_value'] = 1;
			}


			$order_data['ip'] = $this->request->server['REMOTE_ADDR'];

			if (!empty($this->request->server['HTTP_X_FORWARDED_FOR'])) {
				$order_data['forwarded_ip'] = $this->request->server['HTTP_X_FORWARDED_FOR'];
			} elseif (!empty($this->request->server['HTTP_CLIENT_IP'])) {
				$order_data['forwarded_ip'] = $this->request->server['HTTP_CLIENT_IP'];
			} else {
				$order_data['forwarded_ip'] = '';
			}

			if (isset($this->request->server['HTTP_USER_AGENT'])) {
				$order_data['user_agent'] = $this->request->server['HTTP_USER_AGENT'];
			} else {
				$order_data['user_agent'] = '';
			}

			if (isset($this->request->server['HTTP_ACCEPT_LANGUAGE'])) {
				$order_data['accept_language'] = $this->request->server['HTTP_ACCEPT_LANGUAGE'];
			} else {
				$order_data['accept_language'] = '';
			}
			$order_data['order_status_id'] = 1;


			$orderNumber = $this->model_checkout_order->addOrder($order_data);
			$json['success'] = 'Ваше сообщение о заказе успешно отправлено!';
			//		print_r($_POST);
			$to = "moraleksey.ya@yandex.ru";
			$nameSite = $_SERVER['HTTP_HOST'];
			$title = "Заказ с ".$nameSite;

			$from='noreply@mygoodhouse2.fancymedia.ru';
			$mess = "<body><b>Заказ с сайта </b>".$nameSite."<br />";
			$mess .= "<br />";
			$mess .= "<b>Данные заказа:</b><br /><br />";
			$mess .= "Номер заказа: <br />" . $orderNumber . "<br /><br />";
			$mess .= "Артикул: <br />" . $orderArticulProduct . "<br /><br />";
			$mess .= "Товар: <br />" . $orderNameProduct . "<br /><br />";
			$mess .= "Цена: <br />" . $orderPriceProduct . "<br /><br />";
			$mess .= "Количесвто: <br />" . $orderCount . "<br /><br />";
			$mess .= "E-mail: <br />" . $orderEmail . "<br /><br />";
			$mess .= "Телефон: <br />" . $orderPhone . "<br /><br />";
			$mess .= "ФИО: <br />" . $orderNameCustomer . "<br /><br />";
			$mess .= "Комментарий: <br />" . $orderComment . "<br /><br />";
			$mess .= "Итого: <br />" . $orderTotals . "<br /><br />";

			$header="Content-type: text/html; charset=\"utf-8\"\n";
			$header="From: ". $orderNameCustomer . "<" . $from . ">\n";
			$header.="Subject: " . $title . "\n";
			$header.="Content-type: text/html; charset=\"utf-8\"";

			if(!empty($to)){
				mail($to, $title, $mess, $header);
			}

			/////юзеру
			$to = $orderEmail;

			$title = "Вы сделали заказ на ".$nameSite;

			$from='noreply@mygoodhouse2.fancymedia.ru';
			$mess = "<body><b>Заказ с сайта </b>".$nameSite."<br />";
			$mess .= "<br />";
			$mess .= "<b>Данные заказа:</b><br /><br />";
			$mess .= "Номер заказа: <br />" . $orderNumber . "<br /><br />";
			if(isset($orderArticulProduct)) {
				$mess .= "Артикул: <br />" . $orderArticulProduct . "<br /><br />";
			}
			$mess .= "Товар: <br />" . $orderNameProduct . "<br /><br />";
			$mess .= "Цена: <br />" . $orderPriceProduct . "<br /><br />";
			$mess .= "Количесвто: <br />" . $orderCount . "<br /><br />";
			$mess .= "E-mail: <br />" . $orderEmail . "<br /><br />";
			$mess .= "Телефон: <br />" . $orderPhone . "<br /><br />";
			$mess .= "ФИО: <br />" . $orderNameCustomer . "<br /><br />";
			$mess .= "Комментарий: <br />" . $orderComment . "<br /><br />";

			$mess .= "Итого: <br />" . $orderTotals . "<br /><br />";

			$header="Content-type: text/html; charset=\"utf-8\"\n";
			$header="From: mygoodhouse<" . $from . ">\n";
			$header.="Subject: " . $title . "\n";
			$header.="Content-type: text/html; charset=\"utf-8\"";

			if(!empty($to)){
				mail($to, $title, $mess, $header);
			}
		}

		print_r(json_encode($json));

	}

	public function country() {
		$json = array();

		$this->load->model('localisation/country');

		$country_info = $this->model_localisation_country->getCountry($this->request->get['country_id']);

		if ($country_info) {
			$this->load->model('localisation/zone');

			$json = array(
				'country_id'        => $country_info['country_id'],
				'name'              => $country_info['name'],
				'iso_code_2'        => $country_info['iso_code_2'],
				'iso_code_3'        => $country_info['iso_code_3'],
				'address_format'    => $country_info['address_format'],
				'postcode_required' => $country_info['postcode_required'],
				'zone'              => $this->model_localisation_zone->getZonesByCountryId($this->request->get['country_id']),
				'status'            => $country_info['status']
			);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function customfield() {
		$json = array();

		$this->load->model('account/custom_field');

		// Customer Group
		if (isset($this->request->get['customer_group_id']) && is_array($this->config->get('config_customer_group_display')) && in_array($this->request->get['customer_group_id'], $this->config->get('config_customer_group_display'))) {
			$customer_group_id = $this->request->get['customer_group_id'];
		} else {
			$customer_group_id = $this->config->get('config_customer_group_id');
		}

		$custom_fields = $this->model_account_custom_field->getCustomFields($customer_group_id);

		foreach ($custom_fields as $custom_field) {
			$json[] = array(
				'custom_field_id' => $custom_field['custom_field_id'],
				'required'        => $custom_field['required']
			);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}