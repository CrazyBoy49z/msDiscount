<?php

/**
 * @property int id
 */
class msdCouponGroup extends xPDOSimpleObject {
	protected $_chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';


	/**
	 * @param null $cacheFlag
	 *
	 * @return bool
	 */
	public function save($cacheFlag = null) {
		$coupons = abs($this->get('coupons'));

		if (!$this->isNew()) {
			$children = $this->xpdo->getCount('msdCoupon', array('group_id' => $this->id));
			if ($children < $coupons) {
				$generate = $coupons - $children;
			}
			else {
				$generate = 0;
			}
		}
		else {
			$generate = $coupons;
			parent::save($cacheFlag);
		}

		if ($generate > 0) {
			$this->generateCoupons($generate);
		}

		return parent::save();
	}


	/**
	 * @param $number
	 */
	public function generateCoupons($number) {
        $sql = "INSERT INTO {$this->xpdo->getTableName('msdCoupon')} (`group_id`, `code`, `active`) VALUES (?, ?, ?)";
        $query = $this->xpdo->prepare($sql);

        $prefix_true = $this->get('disposable');
        $prefix = $this->get('prefix');

        if($prefix_true == true){
            if (empty($prefix) || !preg_match('#[A-Z0-9]{5}#i', $prefix)) {
                $prefix = 'MS' . sprintf('%03d', $this->id);
                $this->set('prefix', $prefix);
            }
        }

        $chars = str_split($this->_chars);
        $length = count($chars) - 1;
        while ($number > 0) {
            if($prefix_true == true){
                $blocks = array($prefix);
                for ($i = 0; $i < 3; $i++) {
                    $idx = $i + 1;
                    $blocks[$idx] = '';
                    for ($i2 = 0; $i2 < 4; $i2++) {
                        $blocks[$idx] .= $chars[rand(0, $length)];
                    }
                }
                $blocks = implode('-', $blocks);

            } else {

                $blocks = $prefix;

            }


            if ($query->execute(array($this->id, $blocks, 1))) {

                $number--;
            }
        }

        $this->updateCounters();
	}


	/**
	 *
	 */
	public function updateCounters() {
		$total = $this->xpdo->getCount('msdCoupon', array('group_id' => $this->id));
		$activated = $this->xpdo->getCount('msdCoupon', array('group_id' => $this->id, 'active' => false));

		$this->set('coupons', $total);
		$this->set('activated', $activated);

		$this->save();
	}


    /**
     * Get discount from coupon
     *
     * @param $code
     * @param $price
     *
     * @return float|int
     */
    public function getCouponDiscountGroupUser($code = array(),$types = '')
    {

        $groups = array();
        if (empty($date)) {
            $date = date('Y-m-d H:i:s');
        }
        elseif (is_numeric($date)) {
            $date = date('Y-m-d H:i:s', $date);
        }

        // TODO раскометировать на продакшене
      /*  if (isset($this->cache['coupons'][$date])) {

            $groups =  $this->cache['coupons'][$date];

        } else {*/

            // serach copone
            $q = $this->xpdo->newQuery('msdCouponGroup', array('id' => $this->get('id')));
            $q->leftJoin('msdCouponsMember', 'msdCouponsMember', 'msdCouponsMember.coupons_id = msdCouponGroup.id');
            $q->orCondition(array(
                'begins:=' => '0000-00-00 00:00:00',
                'begins:<=' => $date,
            ), '', 1);
            $q->orCondition(array(
                'ends:=' => '0000-00-00 00:00:00',
                'ends:>=' => $date,
            ), '', 2);
            $q->select('id,discount,name,description,group_id,type,relation');
            if ($q->prepare() && $q->stmt->execute()) {
                while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                    if (!isset($groups[$row['id']])) {
                        $groups[$row['id']] = array(
                            'id' => $row['id'],
                            'discount' => $row['discount'],
                            'name' => $row['name'],
                            'description' => $row['description'],
                            'users' => array(),
                            'products' => array(),
                        );
                    }
                    if (!empty($row['type']) && !empty($row['group_id'])) {
                        $groups[$row['id']][$row['type']][$row['group_id']] = $row['relation'];
                    }
                }
            }
            $this->cache['coupons'][$date] = $groups;
        //}

        $groups  = array_values($groups);

        $msDiscount = $this->xpdo->getService('msDiscount');
        $exclude = false;

        if($types == 'users'){

            $exclude = true;

            # проверка доступа
            if($this->xpdo->user->isAuthenticated()){

                // check group user
                if(empty($groups[0]['users'])) {
                    $exclude = false;
                } else {
                    $userGroup = $this->xpdo->user->getUserGroups();
                    foreach ($groups as $sale) {
                        // Exclude groups if so specified in sale
                        // And convert relation to discount
                        foreach (array('users') as $type) {
                            foreach ($sale[$type] as $group_id => $relation) {
                                if ($relation != 'in') {
                                    // out
                                    if(in_array($group_id, $userGroup)){
                                        $exclude = true;
                                        break(2);
                                    }
                                } else {
                                    $exclude = false;
                                }
                            }
                        }
                    }
                }
            } else {
                $exclude = false;
                // Не авторизованный пользователь
                if(!empty($groups[0]['users'])){
                    $exclude = false;
                    foreach ($groups[0]['users'] as $group_id => $relation) {
                        if ($relation == 'in') {
                            $exclude = true;
                            break;
                        }
                    }
                }

            }
        }



        // check group resource
        if($types == 'products'){
            $exclude = true;
            if(!$products = $groups[0]['products']) {
                $exclude = true;
            } else {
                $exclude = true;
                if($cart = $_SESSION['minishop2']['cart']){
                    foreach($cart as $product){
                        $products_in = array();
                        if($groupsResource = $msDiscount->getProductGroups($product['id'])){
                            $products_in = array_intersect(array_keys($groupsResource), array_keys($products));
                            if(!empty($products_in)){
                                foreach ($products as $group_id => $relation) {
                                    if(in_array($group_id, $products_in)){
                                        if ($relation != 'in') {
                                            $exclude = true;
                                        } else {
                                            $exclude = false;
                                            break(2);
                                        }
                                    }
                                }
                            } else {
                                $exclude = true;
                            }
                        }
                    }
                }
            }
        }
        return $exclude;
    }

    public function getCouponProductGroupResource($product_id = '', $code = ''){

        $code = !empty($code) ? $code : $_SESSION['minishop2']['coupon']['code'];
        if(empty($code)) { return;}


        if($msdgroupsResource  = $this->getCouponDiscountGroup($code)){
            $msdgroupsResource  = array_values($msdgroupsResource);
            $products           = $msdgroupsResource[0]['products'];
        }

        if(!empty($products)){
            if($groupsResource = $this->getProductGroups($product_id)){
                $products_in = array_intersect(array_keys($groupsResource), array_keys($products));
                if(!empty($products_in)){
                    return true;
                }
            }
        }
        return false;
    }


	/**
	 * @param array $ancestors
	 *
	 * @return bool
	 */
	public function remove(array $ancestors = array()) {
		$this->xpdo->removeCollection('msdCoupon', array('group_id' => $this->get('id')));

		return parent::remove($ancestors);
	}

}