<?php
/**
 * The base class for msDiscount.
 */

class msDiscount {
	/* @var modX $modx */
	public $modx;
	public $debug = array();
	protected $cache = array(
		'sales' => array(),
		'users' => array(),
		'products' => array(),
	);
	protected $percent = '0%';
	protected $absolute = 0;

	/**
	 * @param modX $modx
	 * @param array $config
	 */
	function __construct(modX &$modx, array $config = array()) {
		$this->modx =& $modx;

		$corePath = $this->modx->getOption('msdiscount_core_path', $config, $this->modx->getOption('core_path') . 'components/msdiscount/');
		$assetsUrl = $this->modx->getOption('msdiscount_assets_url', $config, $this->modx->getOption('assets_url') . 'components/msdiscount/');
		$connectorUrl = $assetsUrl . 'connector.php';
		$actionUrl = $assetsUrl . 'action.php';

        $prefix_length = $this->modx->getOption('msd_coupons_prefix_length', NULL, false);
		$this->config = array_merge(array(
			'assetsUrl' => $assetsUrl,
			'actionUrl' => $actionUrl,
			'pageId'    => $this->modx->resource->id,
			'coupon_js' => $assetsUrl.'js/web/coupon.js',
			'cssUrl' => $assetsUrl . 'css/',
			'jsUrl' => $assetsUrl . 'js/',
			'imagesUrl' => $assetsUrl . 'images/',
			'connectorUrl' => $connectorUrl,

			'corePath' => $corePath,
			'modelPath' => $corePath . 'model/',
			'chunksPath' => $corePath . 'elements/chunks/',
			'templatesPath' => $corePath . 'elements/templates/',
			'chunkSuffix' => '.chunk.tpl',
			'snippetsPath' => $corePath . 'elements/snippets/',
			'processorsPath' => $corePath . 'processors/',
			'debug' => false,
            'prefix_length' => $prefix_length > 20 ? 20 : $prefix_length,
            'discount_sum_cost' => $this->modx->getOption('msd2_discount_sum_cost', NULL, false),
		), $config);

echo '<pre>';
print_r($prefix_length); die;


		$this->modx->addPackage('msdiscount', $this->config['modelPath']);
		$this->modx->lexicon->load('msdiscount:default');
	}


	/**
	 * Sanitizes values for processors
	 *
	 * @param $key
	 * @param $value
	 *
	 * @return mixed|string
	 */
	public function sanitize($key, $value) {
		$value = trim($value);

		switch (strtolower(trim($key))) {
			case 'discount':
				$value = preg_replace(array('/[^0-9%,\.]/','/,/'), array('', '.'), $value);
				if (strpos($value, '%') !== false) {
					$value = str_replace('%', '', $value) . '%';
				}
				if (empty($value)) {$value = '0%';}
				break;
			case 'joinsum':
				if (empty($value)) {$value = 0;}
				break;
			case 'begins':
			case 'ends':
				if (empty($value)) {
					$value = '0000-00-00 00:00:00';
				}
				break;
		}

		return $value;
	}


	/**
	 * Return new product price with discounts
	 *
	 * @param integer $product_id
	 * @param float $price Current price of product
	 * @param string $user_id
	 * @param string $date
	 *
	 * @return bool|float|int|string
	 */
	public function getNewPrice($product_id, $price, $user_id = '', $date = '') {
		$time = microtime(true);
		if (empty($user_id)) {
			$user_id = $this->modx->user->id;
		}

		if ($user_id === '' || empty($product_id)) {
			return $price;
		}

		$this->debugMessage('msd_dbg_initial_price', array('product_id' => $product_id, 'price' => $price));
		$users = $this->getUserGroups($user_id);
		$this->debugMessage('msd_dbg_get_users', array('user_id' => $user_id, 'count' => count($users)));
		$products = $this->getProductGroups($product_id);
		$this->debugMessage('msd_dbg_get_products', array('product_id' => $product_id, 'count' => count($products)));
		if (empty($date)) {$date = date('Y-m-d H:i:s');}
		$sales = $this->getSales($date);
		$this->debugMessage('msd_dbg_get_sales', array('count' => count($sales)));

		$this->percent = '0%';	// Discount in percent
		$this->absolute = 0;	// Discount in absolute value

		// Get discount by sales
		if (!empty($sales)) {
			foreach ($sales as $sale) {
				$this->debugMessage('msd_dbg_sale_start', array('name' => $sale['name']));
				$exclude = false;
				// Exclude groups if so specified in sale
				// And convert relation to discount
				foreach (array('users','products') as $type) {
					foreach ($sale[$type] as $group_id => $relation) {
						if ($relation != 'in') {
							unset($sale[$type][$group_id]);
							if (isset(${$type}[$group_id])) {
								$this->debugMessage('msd_dbg_sale_'.$type.'_exclude', array('name' => $sale['name'], 'group_id' => $group_id));
								$exclude = true;
								break(2);
							}
						}
						elseif (isset(${$type}[$group_id])) {
							$sale[$type][$group_id] = ${$type}[$group_id];
						}
					}
				}
				if ($exclude) {continue;}
				$users_in = array_intersect(array_keys($sale['users']), array_keys($users));
				$products_in = array_intersect(array_keys($sale['products']), array_keys($products));

				if (empty($sale['users']) && empty($sale['products'])) {
					$discount = $sale['discount'];
					$this->debugMessage('msd_dbg_sale_all', array('name' => $sale['name']));
					$this->discount($discount, 'msd_dbg_sale_group_both', array('name' => $sale['name'], 'discount' => $discount));
					// Check group discount
					foreach (array('users', 'products') as $type) {
						foreach (${$type} as $group_id => $discount) {
							$this->discount($discount, 'msd_dbg_sale_personal_'.$type, array('group_id' => $group_id, 'discount' => $discount));
						}
					}
				}
				else {
					if (!empty($sale['users']) && !empty($sale['products'])) {
						if (!empty($users_in) && !empty($products_in)) {
							$discount = $sale['discount'];
							$this->discount($discount, 'msd_dbg_sale_group_both', array('name' => $sale['name'], 'discount' => $discount));
							// Check group discounts
							foreach (array('users', 'products') as $type) {
								foreach ($sale[$type] as $group_id => $discount) {
									$this->discount($discount, 'msd_dbg_sale_personal_'.$type, array('group_id' => $group_id, 'discount' => $discount));
								}
							}
						}
						else {
							$this->debugMessage('msd_dbg_sale_group_no', array('name' => $sale['name']));
							continue;
						}
					}
					elseif (!empty($sale['users']) && !empty($users_in)) {
						$discount = $sale['discount'];
						$this->discount($discount, 'msd_dbg_sale_group_users', array('name' => $sale['name'], 'discount' => $discount));
						// Check group discounts
						foreach ($sale['users'] as $group_id => $discount) {
							$this->discount($discount, 'msd_dbg_sale_personal_users', array('group_id' => $group_id, 'discount' => $discount));
						}
					}
					elseif (!empty($sale['products']) && !empty($products_in)) {
						$discount = $sale['discount'];
						$this->discount($discount, 'msd_dbg_sale_group_products', array('name' => $sale['name'], 'discount' => $discount));
						// Check group discounts
						foreach ($sale['products'] as $group_id => $discount) {
							$this->discount($discount, 'msd_dbg_sale_personal_products', array('group_id' => $group_id, 'discount' => $discount));
						}
					}
					else {
						$this->debugMessage('msd_dbg_sale_group_no', array('name' => $sale['name']));
						continue;
					}
				}
			}
			$this->debugMessage('msd_dbg_sale_end');
		}
		else {
			// Check group discounts
			foreach (array('users', 'products') as $type) {
				foreach (${$type} as $group_id => $discount) {
					$this->discount($discount, 'msd_dbg_personal_'.$type, array('group_id' => $group_id, 'discount' => $discount));
				}
			}
		}

		if ($this->percent == '0%' && $this->absolute == 0) {
			$this->debugMessage('msd_dbg_discount_no', array('price' => $price));
		}
		else {
			if ($this->percent != '0%') {
				$tmp = ($price / 100) * intval($this->percent);
				$this->debugMessage('msd_dbg_discount_percent_to_abs', array('percent' => $this->percent, 'price' => $price, 'discount' => $tmp));
				$this->percent = $tmp;
			}
			else {
				$this->percent = 0;
			}
			if ($this->absolute && $this->percent) {
				$this->debugMessage('msd_dbg_discount_abs_vs_percent', array('percent' => $this->percent, 'absolute' => $this->absolute));
			}

			$discount = $this->absolute > $this->percent
				? $this->absolute
				: $this->percent;
			$price -= $discount;
			if ($price < 0) {
				$price = 0;
			}

			$this->debugMessage('msd_dbg_discount_total', array('price' => $price, 'discount' => $discount));
		}
		$this->debugMessage('msd_dbg_time', array('time' => number_format(round(microtime(true) - $time, 4), 4)));

		return $price;
	}


	/**
	 * Set current discount
	 *
	 * @param $discount
	 * @param string $message
	 * @param array $data
	 */
	public function discount($discount, $message = '', $data = array()) {
		if (strpos($discount, '%') !== false) {
			if ((float) ($discount) > (float) $this->percent) {
				$this->percent = $discount;
				$this->debugMessage($message, $data);
			}
			elseif ($discount != '0%') {
				$this->debugMessage('msd_dbg_discount_less', array('discount' => $discount));
			}
		}
		elseif ((float) $discount > (float) $this->absolute) {
			$this->absolute = $discount;
			$this->debugMessage($message, $data);
		}
		else {
			$this->debugMessage('msd_dbg_discount_less', array('discount' => $discount));
		}
	}


	/**
	 * Check coupon by code
	 *
	 * @param $code
	 *
	 * @return bool|null|string
	 */
	public function checkCoupon($code) {

		if (!$coupon = $this->modx->getObject('msdCoupon', array('code' => $code))) {
			return $this->modx->lexicon('msd_err_coupon_code');
		}
		if (!$coupon->get('active')) {
			return $this->modx->lexicon('msd_err_coupon_active');
		}

		$group = $coupon->getOne('Group');
		$begins = $group->get('begins');
		if (!in_array($begins, array('', '0000-00-00-00 00:00:00', '-1-11-30 00:00:00')) && time() < strtotime($begins)) {
			return $this->modx->lexicon('msd_err_coupon_begins');
		}
		$ends = $group->get('ends');
		if (!in_array($ends, array('', '0000-00-00-00 00:00:00', '-1-11-30 00:00:00')) && time() > strtotime($ends)) {
			return $this->modx->lexicon('msd_err_coupon_ends');
		}

		return true;
	}


	/**
	 * Check coupon by code NEW
	 *
	 * @param $code
	 *
	 * @return bool|null|string
	 */
	public function checkCouponNew($code) {

        $code = trim($code);
        if(empty($code)) {
            return $this->error('msd_err_coupon_empty_code');
        }

        $msdCouponparam = array('code' => $code);
        if (!$tmp_coupon = $this->modx->getObject('msdCoupon', $msdCouponparam)) {
            return $this->error('msd_err_coupon_code');
        }

        $tmp_group = $tmp_coupon->getOne('Group');

        // is one coupon
        if($tmp_group->get('disposable') != true){
            $msdCouponparam['active'] = 1;
        }

		/** @var msdCoupon $coupon */
		if (!$coupon = $this->modx->getObject('msdCoupon', $msdCouponparam)) {
            $msd = 'msd_err_coupon_code';
            if($tmp_group->get('disposable') != true){
                $msd = 'msd_err_coupon_active';
            }
			return $this->error($msd);
		}

        if($tmp_group->get('disposable') == true){
            if (!$coupon->get('active')) {
                return $this->error('msd_err_coupon_active');
            }
        }


		$group = $coupon->getOne('Group');
		$begins = $group->get('begins');
		if (!in_array($begins, array('', '0000-00-00-00 00:00:00', '-1-11-30 00:00:00')) && time() < strtotime($begins)) {
			return $this->error($this->modx->lexicon('msd_err_coupon_begins',array('coupon' => $code)));
		}
		$ends = $group->get('ends');
		if (!in_array($ends, array('', '0000-00-00-00 00:00:00', '-1-11-30 00:00:00')) && time() > strtotime($ends)) {
			return $this->error($this->modx->lexicon('msd_err_coupon_ends',array('coupon' => $code)));
		}


        // check group user
        if($group_in = $group->getCouponDiscountGroupUser($coupon, 'users')) {
            return $this->error('msd_err_copons_code_access_die_user');
        }

        // check group product
        if($group_in = $group->getCouponDiscountGroupUser($coupon, 'products')) {
            return $this->error('msd_err_copons_code_product');
        }

        // check group product
        if($user_action = $this->getCouponUserAction($coupon, $group)) {
            return $this->error('msd_err_copons_code_activeted_user');
        }

        $_SESSION['minishop2']['order']['coupon_code']  = $code;

		return $this->success('');
	}


    /**
     * Check user action coupon one
     *
     * @param $coupon
     *
     * @return true|false
     */
    public function getCouponUserAction($coupon, $group) {

        if($group->get('disposable') != true){

            // check action user coupon
            $code_one = $this->modx->getOption('msd_coupons_code_one');
            if ($this->modx->user->isAuthenticated() and $code_one == true) {
                $user = $this->modx->getObject('modUser', $this->modx->user->id);
                $profile = $user->getOne('Profile');
                $extended = $profile->get('extended');
                $coupon_code = array();
                if ($coupons = $extended['msd_coupon_action']) {
                    $coupon_code = unserialize($coupons);
                }
                if (in_array($coupon->get('code'), $coupon_code)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Cancel coupon by code
     *
     * @return true
     */
    public function cancelCoupon() {

        unset($_SESSION['minishop2']['order']['coupon_code']);

        return true;
    }


    /**
     * status Order by code
     *
     *
     * @return array
     */
    public function statusCoupon() {

        $coupon = $_SESSION['minishop2']['order']['coupon_code'];
        $msg = '';
        $class = '';
        $disabled = 'disabled';
        $this->modx->setPlaceholder('coupon_code', $coupon);

        $response = $this->checkCouponNew($coupon);
        if(!$response['success']){
            if(!empty($coupon)){
                $msg = $response['message'];
                $class = 'has-error';
                $disabled = '';
            }

            $coupon = '';
            //$this->cancelCoupon();
        }

        if(empty($coupon))$disabled = '';

        $this->modx->setPlaceholder('coupon_disabled', $disabled);
        $this->modx->setPlaceholder('coupon_msg', $this->modx->lexicon($msg));
        $this->modx->setPlaceholder('coupon_class', $class);

        $miniShop2 = $this->modx->getService('miniShop2');
        $miniShop2->initialize('web');
        $statusCart = $miniShop2->cart->status();


        $total_base_cost = 0;
        $cart = $miniShop2->cart->get();
        foreach ($cart as $key => $item) {
                $get = $this->modx->getObject('msProduct', $item['id'])->toArray();
                $total_base_cost += $get['price'] * $item['count'];
        }


        $discount               = empty($coupon) ? 0 : $this->getCouponDiscount($coupon, $statusCart['total_cost']);
        $total_cost_full        = $statusCart['total_cost'];
        $total_cost             = $statusCart['total_cost'] - $discount;


        $discount_total_cost    = $total_base_cost - $total_cost;
        $discount_sale          = $discount_total_cost - $discount;

        $statusCart['total_discount']       = $discount;
        $statusCart['total_discount_coupone'] = $total_base_cost - $discount;
        $statusCart['total_discount_all']   = $discount_total_cost;
        $statusCart['total_discount_sale']  = $discount_sale;
        $statusCart['total_cost']           = $total_cost;
        $statusCart['total_cost_old']       = $total_cost;
        $statusCart['total_cost_full']      = $total_cost_full;
        $statusCart['total_base_cost']      = $total_base_cost;


        // discount sum order
        if($this->config['discount_sum_cost']){
            $whatCost = 'total_base_cost';
        } else {
            $whatCost = 'total_cost_old';
        }


        $discountMaxSum = $this->getSumAtExcessRates($statusCart[$whatCost]);

        $gibSum = true;
        $statusCart['total_discount_bigsum'] = 0;
        if($gibSum == true){

            if($whatCost == 'total_base_cost') {
                $total_cost_old = $statusCart[$whatCost] - $discountMaxSum;

                $discountMaxSum = $statusCart['total_base_cost'] - $total_cost_old;
                $itog = $statusCart['total_cost_old'] - $discountMaxSum;



            } else {

                $itog = $statusCart[$whatCost] - $discountMaxSum;

            }


            $statusCart['total_discount_bigsum_sum'] = $total_base_cost - $discountMaxSum;
            $statusCart['total_discount_bigsum'] = $discountMaxSum;
            $statusCart['total_discount_all'] = $discountMaxSum + $statusCart['total_discount_all'];
        }


        $statusCart['total_cost_old'] = $itog;
        $statusCart['total_cost'] = $itog;


        $statusCart['coupon_description']  = '';
        if ($coupons = $this->modx->getObject('msdCoupon', array('code' => $coupon))) {
            $group = $coupons->getOne('Group');
            $statusCart['coupon_description']  = $group->get('description');
        }


        foreach ($statusCart as $key => $val) {
            if($key == 'coupon_description'){
                $this->modx->setPlaceholder($key, $val);
            } else {
                $this->modx->setPlaceholder($key, $miniShop2->formatPrice($val));
            }
        }

        // btn
        $btn = !empty($coupon) ? 'msd_coupons_front_btn_cancel' : 'msd_coupons_front_btn_apply';
        $this->modx->setPlaceholder('coupon_btn', $this->modx->lexicon($btn));

        // action
        $btn = !empty($coupon) ? 'coupon/cancel' : 'coupon/apply';
        $this->modx->setPlaceholder('coupon_action', $this->modx->lexicon($btn));



        return $statusCart;
    }


    /**
     * status Order by code
     *
     * @param $code
     *
     * @return bool|null|string
     */
    public function getSumAtExcessRates($sum = 0) {
        $groups = array();
        $q = $this->modx->newQuery('msdUserGroup');
        $q->where(array('rates:!=' => 0));
        if ($q->prepare() && $q->stmt->execute()) {
            while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                //msdUserGroup_id
                if($sum >= $row['msdUserGroup_rates']){
                    $groups[] = $row['msdUserGroup_rates'];
                    $groupsAll[$row['msdUserGroup_rates']] = $row['msdUserGroup_id'];

                    $discount[$row['msdUserGroup_id']] = $row['msdUserGroup_discount'];
                }
            }
        }
        $val = max($groups, $sum);
        $max = max($val);
        $group_id = !empty($groupsAll[$max]) ? $groupsAll[$max] : 0;


        return $this->getBigSumDiscount($discount[$group_id], $sum);
    }


    /**
	 * Get discount from coupon
	 *
	 * @param $code
	 * @param $price
	 *
	 * @return float|int
	 */
	public function getCouponDiscount($code, $price) {
		$res = 0;
		/** @var msdCoupon $coupon */
		if ($coupon = $this->modx->getObject('msdCoupon', array('code' => $code))) {
			if ($group = $coupon->getOne('Group')) {
				$discount = $group->get('discount');
				if (strpos($discount, '%') !== false) {
					$res = $price * ((float)$discount / 100);
				}
				else {
					$res = (float)$discount;
				}
			}
		}

		return $res > 0
			? $res
			: 0;
	}


    /**
	 * Get discount from big summ
	 *
	 * @param $code
	 * @param $price
	 *
	 * @return float|int
	 */
	public function getBigSumDiscount($discount, $price) {
		$res = 0;

		/** @var msdCoupon $coupon */
        if (strpos($discount, '%') !== false) {
            $res = $price * ((float)$discount / 100);
        }
        else {
            $res = (float)$discount;
        }

		return $res > 0
			? $res
			: 0;
	}


	/**
	 * Return array with groups of user
	 *
	 * @param int $id
	 *
	 * @return array
	 */
	public function getUserGroups($id = 0) {
		if (isset($this->cache['users'][$id])) {
			return $this->cache['users'][$id];
		}
		$groups = array();

		if (!empty($id)) {
			$q = $this->modx->newQuery('modUserGroupMember', array('member' => $id));
			$q->leftJoin('msdUserGroup', 'msdUserGroup', 'msdUserGroup.id = modUserGroupMember.user_group');
			$q->select('user_group,discount');
			$q->sortby('discount');
			$tstart = microtime(true);
			if ($q->prepare() && $q->stmt->execute()) {
				$this->modx->queryTime += microtime(true) - $tstart;
				$this->modx->executedQueries++;
				while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
					$groups[$row['user_group']] = $row['discount'];
				}
			}
		}
		$this->cache['users'][$id] = $groups;

		return $groups;
	}


	/**
	 * Return array with groups of product
	 *
	 * @param $id
	 *
	 * @return array
	 */
	public function getProductGroups($id) {
		if (isset($this->cache['products'][$id])) {
			return $this->cache['products'][$id];
		}
		$groups = array();

		if ($product = $this->modx->getObject('msProduct', $id)) {
			$ids = $this->modx->getParentIds($id, 10, array('context' => $product->get('context_key')));
			$ids[] = $id;
		}
		else {
			$ids = array($id);
		}
		$q = $this->modx->newQuery('msCategoryMember', array('product_id' => $id));
		$q->select('category_id');
		$tstart = microtime(true);
		if ($q->prepare() && $q->stmt->execute()) {
			$this->modx->queryTime += microtime(true) - $tstart;
			$this->modx->executedQueries++;
			if ($tmp = $q->stmt->fetchAll(PDO::FETCH_COLUMN)) {
				$ids = array_merge($ids, $tmp);
			}
		}
		$ids = array_unique($ids);
		$where = count($ids) > 1
			? array('document:IN' => $ids)
			: array('document' => $ids[0]);

		$q = $this->modx->newQuery('modResourceGroupResource', $where);
		$q->leftJoin('msdProductGroup', 'msdProductGroup', 'msdProductGroup.id = modResourceGroupResource.document_group');
		$q->select('document_group, discount');
		$q->sortby('discount');
		$q->groupby('msdProductGroup.id');
		$tstart = microtime(true);
		if ($q->prepare() && $q->stmt->execute()) {
			$this->modx->queryTime += microtime(true) - $tstart;
			$this->modx->executedQueries++;
			while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
				$groups[$row['document_group']] = $row['discount'];
			}
		}
		$this->cache['products'][$id] = $groups;

		return $groups;
	}


	/**
	 * Return array with current active sales
	 *
	 * @param string $date
	 * @param bool $force_date
	 *
	 * @return array
	 */
	public function getSales($date = '', $force_date = false) {
		$groups = array();
		if (empty($date)) {
			$date = date('Y-m-d H:i:s');
		}
		elseif (is_numeric($date)) {
			$date = date('Y-m-d H:i:s', $date);
		}

		if (isset($this->cache['sales'][$date])) {
			return $this->cache['sales'][$date];
		}

		$q = $this->modx->newQuery('msdSale', array('active' => 1));
		$q->leftJoin('msdSaleMember', 'msdSaleMember', 'msdSaleMember.sale_id = msdSale.id');
		if ($force_date) {
			$q->andCondition(array(
				'begins:<=' => $date,
				'ends:>=' => $date,
			));
		}
		else {
			$q->orCondition(array(
				'begins:=' => '0000-00-00 00:00:00',
				'begins:<=' => $date,
			), '', 1);
			$q->orCondition(array(
				'ends:=' => '0000-00-00 00:00:00',
				'ends:>=' => $date,
			), '', 2);
		}

		$q->select('id,discount,name,begins,ends,group_id,type,relation');
		$tstart = microtime(true);
		if ($q->prepare() && $q->stmt->execute()) {
			$this->modx->queryTime = microtime(true) - $tstart;
			$this->modx->executedQueries++;
			while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
				if (!isset($groups[$row['id']])) {
					$groups[$row['id']] = array(
						'id' => $row['id'],
						'discount' => $row['discount'],
						'name' => $row['name'],
						'begins' => $row['begins'],
						'ends' => $row['ends'],
						'users' => array(),
						'products' => array(),
					);
				}
				if (!empty($row['type']) && !empty($row['group_id'])) {
					$groups[$row['id']][$row['type']][$row['group_id']] = $row['relation'];
				}
			}
		}
		$this->cache['sales'][$date] = $groups;

		return $groups;
	}


	/**
	 * Adds debug messages
	 *
	 * @param $message
	 * @param array $data
	 */
	public function debugMessage($message, $data = array()) {
		if ($this->config['debug']) {
			$this->debug[] = $this->modx->lexicon($message, $data);
		}
	}


	/**
	 * Compares MODX version
	 *
	 * @param string $version
	 * @param string $dir
	 *
	 * @return bool
	 */
	public function systemVersion($version = '2.3.0', $dir = '>=') {
		$this->modx->getVersionData();

		return !empty($this->modx->version) && version_compare($this->modx->version['full_version'], $version, $dir);
	}


    /**
     * This method returns an error of the order
     *
     * @param string $message A lexicon key for error message
     * @param array $data.Additional data, for example cart status
     * @param array $placeholders Array with placeholders for lexicon entry
     *
     * @return array|string $response
     */
    public function error($message = '', $data = array(), $placeholders = array()) {
        $response = array(
            'success' => false,
            'message' => $this->modx->lexicon($message, $placeholders),
            'data' => $data,
        );

        return $this->config['json_response'] ? $this->modx->toJSON($response) : $response;
    }


    /**
     * This method returns an success of the order
     *
     * @param string $message A lexicon key for success message
     * @param array $data.Additional data, for example cart status
     * @param array $placeholders Array with placeholders for lexicon entry
     *
     * @return array|string $response
     */
    public function success($message = '', $data = array(), $placeholders = array()) {
        $response = array(
            'success' => true,
            'message' => $this->modx->lexicon($message, $placeholders),
            'data' => $data,
        );

        return $this->config['json_response'] ? $this->modx->toJSON($response) : $response;
    }
}
