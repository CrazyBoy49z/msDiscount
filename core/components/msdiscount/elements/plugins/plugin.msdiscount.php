<?php

/** @var msDiscount $msDiscount */
$msDiscount = $modx->getService('msDiscount');

switch ($modx->event->name) {

	case 'msOnGetProductPrice':
		if ($modx->context->key == 'mgr') {return;}
		/**
		 * Counts discount of current product for current user, based on rules in msDiscount component
		 * New price must be set in $modx->event->returnedValues['price']
		 *
		 * @var msProductData $product Object with product properties
		 * @var array $data Array with product properties. Can be empty!
		 * @var float $price Current price of product
		 */
		if (!isset($modx->event->returnedValues['price'])) {
			$modx->event->returnedValues['price'] = $price;
		}
		// Get link to product price
		$price = & $modx->event->returnedValues['price'];
		$new_price = $msDiscount->getNewPrice($product->id, $price);
		if ($new_price !== false) {
			$price = $new_price;
		}
		break;

	case 'msOnChangeOrderStatus':
		/**
		 * Add user to discounts group if he spent required sum for join
		 *
		 * @var msOrder $order
		 * @var integer $status
		 */
		if ($status != 2) {return;}

		/** @var modUser $user */
		if ($user = $order->getOne('User')) {
			if ($profile = $modx->getObject('msCustomerProfile', $user->id)) {
				$spent = $profile->spent;
				if ($spent > 0) {
					$q = $modx->newQuery('msdUserGroup');
					$q->where('joinsum > 0');
					$q->select('id,joinsum');
					if ($q->prepare() && $q->stmt->execute()) {
						$groups = $msDiscount->getUserGroups($user->id);
						while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
							if ($spent > $row['joinsum'] && !isset($groups[$row['id']])) {
								$user->joinGroup((integer) $row['id'], 1);
							}
						}
					}
				}
			}
		}
		break;

	case 'msOnBeforeAddToOrder':
		/** @var string $key */
		if ($key == 'coupon_code' && !empty($value)) {
			$check = $msDiscount->checkCoupon($value);
			if ($check !== true) {
				$modx->event->output($check);
			}
		}
		break;

	case 'msOnGetOrderCost':
		/**@var msOrderInterface $order */
		if (!empty($with_cart) && !empty($cost)) {
			if ($data = $order->get()) {
				if (!empty($data['coupon_code']) && $msDiscount->checkCoupon($data['coupon_code']) === true) {
					if ($discount = $msDiscount->getCouponDiscount($data['coupon_code'], $cost)) {
						$cost -= $discount;
						if ($cost >= 0) {
							$modx->event->returnedValues['cost'] = $cost;
						}
					}
				}
			}
		}
		break;

	case 'msOnCreateOrder':
		/**@var msOrderInterface $order */
		if ($data = $order->get()) {

            $msdCouponparam = array();
            $msdCouponparam['code'] = $data['coupon_code'];
            if (!empty($data['coupon_code'])){
                if ($tmp_coupon = $modx->getObject('msdCoupon', array('code' => $data['coupon_code']))) {
                    $group = $tmp_coupon->getOne('Group');
                    if($group->get('disposable') == false){
                         $msdCouponparam = array(
                             'active' => true,
                             'code' => $data['coupon_code']
                         );
                    }
                }
            }


			/**@var msdCoupon $coupon */
			if (!empty($data['coupon_code']) && $coupon = $modx->getObject('msdCoupon', $msdCouponparam)) {

                /**@var msOrder $msOrder */
				$coupon->fromArray(array(
					'active' => false,
					'activatedon' => date('Y-m-d H:i:s'),
					'order_id' => $msOrder->get('id'),
				));
				$coupon->save();
				$properties = $msOrder->get('properties');
				if (!is_array($properties)) {
					$properties = array();
				}
				$properties['coupon_code'] = $coupon->get('code');
				if ($group = $coupon->getOne('Group')) {
					$properties['coupon_discount'] = $group->get('discount');
				}
				$msOrder->set('properties', $properties);
				$msOrder->save();

                // save coupon users
                if ($modx->user->isAuthenticated()) {

                    $user = $modx->getObject('modUser', $modx->user->id);
                    $profile = $user->getOne('Profile');
                    $extended = $profile->get('extended');
                    $coupon_code = array();
                    if ($coupon = $extended['msd_coupon_action']) {
                        $coupon_code = unserialize($coupon);
                    }

                    $coupon_code = empty($coupon_code) ? array($data['coupon_code']) : array_merge($coupon_code, array($data['coupon_code']));
                    $extended['msd_coupon_action'] = serialize($coupon_code);

                    $profile->set('extended', $extended);
                    $profile->save();
                    $user->save();

                }
			}
		}

		break;

	case 'OnWebLogin':
	case 'OnWebLogout':
		/** Set flag for cart reload */
		$_SESSION['minishop2']['cart_reload'] = true;
		break;

	case 'OnLoadWebDocument':

        /**
         * use of coupons
         */
		if (!empty($_REQUEST['msd_action'])) {

            $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest';
            $action = trim($_REQUEST['msd_action']);
            $ctx = !empty($_REQUEST['ctx']) ? (string) $_REQUEST['ctx'] : 'web';
            if ($ctx != 'web') {$modx->switchContext($ctx);}

            $miniShop2 = $modx->getService('miniShop2');
            $miniShop2->initialize($modx->context->key, array('json_response' => true));

            switch ($action) {
                case 'coupon/apply':  $response = $msDiscount->checkCouponNew(@$_POST['coupon_code']); break;
                case 'coupon/cancel': $response = $msDiscount->cancelCoupon(); break;
                case 'coupon/status': $response = $msDiscount->statusCoupon(); break;
                default:
                    $message = ($_REQUEST['msd_action'] != $action)
                        ? 'ms2_err_register_globals'
                        : 'ms2_err_unknown';
                    $response = $modx->error($message);
            }

            if($response['success']){
                $response = $miniShop2->success('', array('status' => $msDiscount->statusCoupon()));
            } else {
                $response = $miniShop2->error($response['message'], array('status' => $msDiscount->statusCoupon()));
            }

            if ($isAjax) {
                @session_write_close();
                exit($response);
            }
        }

		/**
		 * Recalculate cart of user if flag is set
		 * @var miniShop2 $miniShop2
		 */
		if (empty($_SESSION['minishop2']['cart_reload'])) {return;}

		$miniShop2 = $modx->getService('miniShop2');
		$miniShop2->initialize($modx->context->key);

		$cart = $miniShop2->cart->get();
		if (!empty($cart)) {
			foreach ($cart as $key => $item) {
				/** @var msProduct $product */
				if ($product = $modx->getObject('msProductData', $item['id'])) {
					$cart[$key]['price'] = $product->getPrice();
				}
			}
			$miniShop2->cart->set($cart);
		}
		unset($_SESSION['minishop2']['cart_reload']);
		break;
}